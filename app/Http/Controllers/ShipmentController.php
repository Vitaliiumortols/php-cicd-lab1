<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    public function store(Request $request, Order $order): JsonResponse
    {
        $data = $request->validate([
            'recipient' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:255'],
            'carrier' => ['required', 'string', 'max:100'],
            'tracking_number' => ['nullable', 'string', 'max:100', 'unique:shipments,tracking_number'],
            'status' => ['nullable', 'string', 'max:30'],
        ]);

        return response()->json($order->shipment()->create($data), 201);
    }

    public function show(Shipment $shipment): JsonResponse
    {
        return response()->json($shipment->load('order.items'));
    }
}
