<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    private const RESERVING_STATUSES = ['pending', 'confirmed', 'processing'];

    public function index(Request $request): View|JsonResponse
    {
        $orders = Order::with(['user:id,name,email', 'shipment'])->withCount('items')->latest();

        return $request->expectsJson()
            ? response()->json($orders->with('items.product')->get())
            : view('orders.index', ['orders' => $orders->paginate(10)]);
    }

    public function create(): View
    {
        return view('orders.create', $this->formDictionaries());
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $this->validated($request);
        $checkoutKey = $data['checkout_key'] ?? (string) Str::uuid();
        $oldOrder = Order::where('checkout_key', $checkoutKey)->first();
        if ($oldOrder) {
            return $request->expectsJson()
                ? response()->json($oldOrder->load('items'))
                : redirect()->route('orders.show', $oldOrder)->with('success', 'Замовлення вже існувало.');
        }

        $order = DB::transaction(function () use ($data, $checkoutKey): Order {
            $status = $data['status'] ?? 'pending';
            $prepared = $this->prepareItems($data['items'], $this->usesReservation($status));
            $order = Order::create([
                'user_id' => $data['user_id'],
                'number' => 'ORD-'.now()->format('YmdHis').'-'.random_int(10, 99),
                'status' => $status,
                'payment_status' => $data['payment_status'] ?? 'unpaid',
                'total_kop' => $prepared['total'],
                'comment' => $data['comment'] ?? null,
                'checkout_key' => $checkoutKey,
                'reserve_expires_at' => $status === 'pending' ? now()->addMinutes(30) : null,
                'paid_at' => ($data['payment_status'] ?? 'unpaid') === 'paid' ? now() : null,
            ]);
            $this->saveItems($order, $prepared['items']);

            return $order;
        });

        return $request->expectsJson()
            ? response()->json($order->load('items'), 201)
            : redirect()->route('orders.show', $order)->with('success', 'Замовлення створено.');
    }

    public function show(Order $order, Request $request): View|JsonResponse
    {
        $order->load(['user:id,name,email,phone', 'items.product', 'shipment']);

        return $request->expectsJson()
            ? response()->json($order)
            : view('orders.show', ['order' => $order]);
    }

    public function edit(Order $order): View
    {
        $order->load('items');

        return view('orders.edit', ['order' => $order] + $this->formDictionaries());
    }

    public function update(Request $request, Order $order): RedirectResponse|JsonResponse
    {
        $data = $this->validated($request, $order);

        DB::transaction(function () use ($data, $order): void {
            if ($this->usesReservation($order->status)) {
                $this->releaseReservations($order);
            }

            $prepared = $this->prepareItems($data['items'], $this->usesReservation($data['status']));
            $order->items()->delete();
            $order->update([
                'user_id' => $data['user_id'],
                'status' => $data['status'],
                'payment_status' => $data['payment_status'],
                'total_kop' => $prepared['total'],
                'comment' => $data['comment'] ?? null,
                'reserve_expires_at' => $data['status'] === 'pending' ? ($order->reserve_expires_at ?? now()->addMinutes(30)) : null,
                'paid_at' => $data['payment_status'] === 'paid' ? ($order->paid_at ?? now()) : null,
            ]);
            $this->saveItems($order, $prepared['items']);
        });

        return $request->expectsJson()
            ? response()->json($order->fresh()->load('items'))
            : redirect()->route('orders.show', $order)->with('success', 'Замовлення оновлено.');
    }

    public function destroy(Order $order, Request $request): RedirectResponse|JsonResponse
    {
        if ($order->status === 'completed' || in_array($order->shipment?->status, ['shipped', 'in_transit', 'delivered'], true)) {
            $message = 'Відправлене або завершене замовлення не можна скасувати через звичайний CRUD.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 422)
                : back()->with('error', $message);
        }

        DB::transaction(function () use ($order): void {
            if ($this->usesReservation($order->status)) {
                $this->releaseReservations($order);
            }
            $order->update([
                'status' => 'cancelled',
                'reserve_expires_at' => null,
            ]);
        });

        return $request->expectsJson()
            ? response()->json(['status' => 'cancelled'])
            : redirect()->route('orders.index')->with('success', 'Замовлення скасовано, історію збережено.');
    }

    private function validated(Request $request, ?Order $order = null): array
    {
        return $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'checkout_key' => [$order ? 'nullable' : 'nullable', 'uuid'],
            'status' => [$order ? 'required' : 'nullable', Rule::in(['pending', 'confirmed', 'processing', 'completed', 'cancelled'])],
            'payment_status' => [$order ? 'required' : 'nullable', Rule::in(['unpaid', 'paid', 'refunded'])],
            'comment' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);
    }

    private function prepareItems(array $items, bool $reserve): array
    {
        $total = 0;
        $prepared = [];
        foreach ($items as $item) {
            $product = Product::lockForUpdate()->findOrFail($item['product_id']);
            if ($reserve && (! $product->active || $product->availableQty() < $item['quantity'])) {
                abort(422, "Недостатньо товару {$product->model_name} на складі.");
            }
            if ($reserve) {
                $product->increment('reserved_qty', $item['quantity']);
            }
            $total += $product->price_kop * $item['quantity'];
            $prepared[] = [$product, (int) $item['quantity']];
        }

        return ['total' => $total, 'items' => $prepared];
    }

    private function saveItems(Order $order, array $items): void
    {
        foreach ($items as [$product, $quantity]) {
            $order->items()->create([
                'product_id' => $product->id,
                'product_name' => "{$product->model_name} {$product->storage_gb} GB {$product->color}",
                'sku' => $product->sku,
                'quantity' => $quantity,
                'unit_price_kop' => $product->price_kop,
            ]);
        }
    }

    private function releaseReservations(Order $order): void
    {
        $order->loadMissing('items.product');
        foreach ($order->items as $item) {
            if ($item->product) {
                $item->product->update([
                    'reserved_qty' => max(0, $item->product->reserved_qty - $item->quantity),
                ]);
            }
        }
    }

    private function usesReservation(string $status): bool
    {
        return in_array($status, self::RESERVING_STATUSES, true);
    }

    private function formDictionaries(): array
    {
        return [
            'users' => User::orderBy('name')->get(),
            'products' => Product::where('active', true)->orderBy('model_name')->get(),
        ];
    }
}
