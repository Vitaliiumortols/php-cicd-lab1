<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ShipmentController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $shipments = Shipment::with('order.user:id,name')->latest();

        return $request->expectsJson()
            ? response()->json($shipments->get())
            : view('shipments.index', ['shipments' => $shipments->paginate(10)]);
    }

    public function create(Request $request): View
    {
        return view('shipments.create', [
            'orders' => Order::doesntHave('shipment')->with('user:id,name')->latest()->get(),
            'selectedOrder' => $request->integer('order_id') ?: null,
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $this->validated($request);
        $order = Order::findOrFail($data['order_id']);
        unset($data['order_id']);
        $shipment = $order->shipment()->create($data);

        return $request->expectsJson()
            ? response()->json($shipment, 201)
            : redirect()->route('shipments.show', $shipment)->with('success', 'Доставку створено.');
    }

    public function storeForOrder(Request $request, Order $order): RedirectResponse|JsonResponse
    {
        $request->merge(['order_id' => $order->id]);

        return $this->store($request);
    }

    public function show(Shipment $shipment, Request $request): View|JsonResponse
    {
        $shipment->load('order.user', 'order.items');

        return $request->expectsJson()
            ? response()->json($shipment)
            : view('shipments.show', ['shipment' => $shipment]);
    }

    public function edit(Shipment $shipment): View
    {
        return view('shipments.edit', ['shipment' => $shipment->load('order.user')]);
    }

    public function update(Request $request, Shipment $shipment): RedirectResponse|JsonResponse
    {
        $data = $this->validated($request, $shipment);
        unset($data['order_id']);
        $shipment->update($data);

        return $request->expectsJson()
            ? response()->json($shipment)
            : redirect()->route('shipments.show', $shipment)->with('success', 'Доставку оновлено.');
    }

    public function destroy(Shipment $shipment, Request $request): RedirectResponse|JsonResponse
    {
        $shipment->delete();

        return $request->expectsJson()
            ? response()->json(['status' => 'deleted'])
            : redirect()->route('shipments.index')->with('success', 'Доставку видалено.');
    }

    private function validated(Request $request, ?Shipment $shipment = null): array
    {
        return $request->validate([
            'order_id' => [$shipment ? 'nullable' : 'required', 'integer', 'exists:orders,id', Rule::unique('shipments')->ignore($shipment)],
            'recipient' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:255'],
            'carrier' => ['required', 'string', 'max:100'],
            'tracking_number' => ['nullable', 'string', 'max:100', Rule::unique('shipments')->ignore($shipment)],
            'status' => ['required', Rule::in(['planned', 'shipped', 'in_transit', 'delivered', 'cancelled'])],
            'delivered_at' => ['nullable', 'date'],
        ]);
    }
}
