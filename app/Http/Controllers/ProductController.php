<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::query()
            ->with(['category:id,name', 'tags:id,name'])
            ->where('active', true)
            ->orderBy('model_name')
            ->get();

        return response()->json(['status' => 'success', 'data' => $products]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validateProduct($request);
        $tags = $validated['tags'] ?? [];
        unset($validated['tags']);

        $product = Product::create($validated);
        $product->tags()->sync($tags);
        $product->load(['category:id,name', 'tags:id,name']);

        return response()->json(['status' => 'created', 'data' => $product], 201);
    }

    public function show(Product $product): JsonResponse
    {
        $product->load(['category:id,name', 'tags:id,name']);

        return response()->json(['status' => 'success', 'data' => $product]);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $this->validateProduct($request, $product);
        $tags = $validated['tags'] ?? null;
        unset($validated['tags']);

        $product->update($validated);
        if ($tags !== null) {
            $product->tags()->sync($tags);
        }

        $product->load(['category:id,name', 'tags:id,name']);

        return response()->json(['status' => 'updated', 'data' => $product]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json(['status' => 'deleted']);
    }

    private function validateProduct(Request $request, ?Product $product = null): array
    {
        $id = $product?->id;

        return $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'model_name' => ['required', 'string', 'min:2', 'max:100'],
            'color' => ['required', 'string', 'min:2', 'max:50'],
            'storage_gb' => ['required', 'integer', 'min:64'],
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku,'.$id],
            'price_kop' => ['required', 'integer', 'min:1'],
            'stock_qty' => ['required', 'integer', 'min:0'],
            'reserved_qty' => ['nullable', 'integer', 'min:0', 'lte:stock_qty'],
            'active' => ['nullable', 'boolean'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
        ]);
    }
}
