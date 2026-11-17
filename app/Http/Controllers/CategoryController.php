<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Category::withCount('products')->orderBy('name')->get());
    }

    public function store(Request $request): JsonResponse
    {
        return response()->json(Category::create($this->validated($request)), 201);
    }

    public function show(Category $category): JsonResponse
    {
        return response()->json($category->load('products.tags'));
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $category->update($this->validated($request, $category));

        return response()->json($category);
    }

    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json(['status' => 'deleted']);
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('categories')->ignore($category)],
            'slug' => ['required', 'string', 'max:100', Rule::unique('categories')->ignore($category)],
            'description' => ['nullable', 'string'],
            'active' => ['nullable', 'boolean'],
        ]);
    }
}
