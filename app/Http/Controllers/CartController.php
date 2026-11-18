<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function show(Cart $cart): JsonResponse
    {
        return response()->json($cart->load(['user:id,name,email', 'items.product.tags']));
    }

    public function addItem(Request $request, Cart $cart): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        $item = $cart->items()->updateOrCreate(
            ['product_id' => $data['product_id']],
            ['quantity' => $data['quantity']],
        );

        return response()->json($item->load('product'), 201);
    }
}
