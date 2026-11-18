<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Order::with(['user:id,name,email', 'items.product', 'shipment'])->latest()->get());
    }

    public function show(Order $order): JsonResponse
    {
        return response()->json($order->load(['user:id,name,email', 'items.product', 'shipment']));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'checkout_key' => ['required', 'uuid'],
            'comment' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        $oldOrder = Order::where('checkout_key', $data['checkout_key'])->first();
        if ($oldOrder) {
            return response()->json($oldOrder->load('items'));
        }

        $order = DB::transaction(function () use ($data): Order {
            $total = 0;
            $preparedItems = [];

            foreach ($data['items'] as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                if (! $product->active || $product->availableQty() < $item['quantity']) {
                    abort(422, 'Недостатньо товару на складі');
                }

                $product->increment('reserved_qty', $item['quantity']);
                $total += $product->price_kop * $item['quantity'];
                $preparedItems[] = [$product, $item['quantity']];
            }

            $order = Order::create([
                'user_id' => $data['user_id'],
                'number' => 'ORD-'.now()->format('YmdHis').'-'.random_int(10, 99),
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'total_kop' => $total,
                'comment' => $data['comment'] ?? null,
                'checkout_key' => $data['checkout_key'],
                'reserve_expires_at' => now()->addMinutes(30),
            ]);

            foreach ($preparedItems as [$product, $quantity]) {
                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->model_name.' '.$product->storage_gb.' GB '.$product->color,
                    'sku' => $product->sku,
                    'quantity' => $quantity,
                    'unit_price_kop' => $product->price_kop,
                ]);
            }

            return $order;
        });

        return response()->json($order->load('items'), 201);
    }
}
