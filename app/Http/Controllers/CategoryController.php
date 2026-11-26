<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $categories = Category::withCount('products')->orderBy('name');

        return $request->expectsJson()
            ? response()->json($categories->get())
            : view('categories.index', ['categories' => $categories->paginate(10)]);
    }

    public function create(): View
    {
        return view('categories.create');
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $this->validated($request);
        $data['active'] = $request->boolean('active');
        $category = Category::create($data);

        return $request->expectsJson()
            ? response()->json($category, 201)
            : redirect()->route('categories.index')->with('success', 'Категорію створено.');
    }

    public function show(Category $category, Request $request): View|JsonResponse
    {
        $category->load(['products.tags']);

        return $request->expectsJson()
            ? response()->json($category)
            : view('categories.show', ['category' => $category]);
    }

    public function edit(Category $category): View
    {
        return view('categories.edit', ['category' => $category]);
    }

    public function update(Request $request, Category $category): RedirectResponse|JsonResponse
    {
        $data = $this->validated($request, $category);
        $data['active'] = $request->boolean('active');
        $category->update($data);

        return $request->expectsJson()
            ? response()->json($category)
            : redirect()->route('categories.show', $category)->with('success', 'Категорію оновлено.');
    }

    public function destroy(Category $category, Request $request): RedirectResponse|JsonResponse
    {
        $category->delete();

        return $request->expectsJson()
            ? response()->json(['status' => 'deleted'])
            : redirect()->route('categories.index')->with('success', 'Категорію видалено.');
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('categories')->ignore($category)],
            'slug' => ['required', 'string', 'max:100', 'alpha_dash:ascii', Rule::unique('categories')->ignore($category)],
            'description' => ['nullable', 'string', 'max:1000'],
            'active' => ['nullable', 'boolean'],
        ]);
    }
}
