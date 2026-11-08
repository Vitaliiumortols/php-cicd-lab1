<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private array $products = [
        [
            'id' => 1,
            'model_name' => 'iPhone 15',
            'color' => 'Black',
            'storage_gb' => 128,
            'price' => 29999,
        ],
        [
            'id' => 2,
            'model_name' => 'iPhone 16',
            'color' => 'White',
            'storage_gb' => 256,
            'price' => 42999,
        ],
        [
            'id' => 3,
            'model_name' => 'iPhone 16 Pro',
            'color' => 'Titanium',
            'storage_gb' => 256,
            'price' => 52999,
        ],
    ];

    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->products,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'model_name' => ['required', 'string', 'min:2', 'max:100'],
            'color' => ['required', 'string', 'min:2', 'max:50'],
            'storage_gb' => ['required', 'integer', 'min:64'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $product = [
            'id' => count($this->products) + 1,
            'model_name' => $data['model_name'],
            'color' => $data['color'],
            'storage_gb' => $data['storage_gb'],
            'price' => $data['price'],
        ];

        return response()->json([
            'status' => 'created',
            'data' => $product,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        foreach ($this->products as $product) {
            if ($product['id'] === (int) $id) {
                return response()->json([
                    'status' => 'success',
                    'data' => $product,
                ]);
            }
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Товар не знайдено',
        ], 404);
    }
}
