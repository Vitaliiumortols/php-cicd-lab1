<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $query = Product::query()->with(['category:id,name', 'tags:id,name'])->latest('id');

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(fn ($q) => $q
                ->where('model_name', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%")
                ->orWhere('color', 'like', "%{$search}%"));
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->integer('category'));
        }

        if ($request->filled('storage_gb')) {
            $query->where('storage_gb', $request->integer('storage_gb'));
        }

        if ($request->filled('color')) {
            $query->where('color', $request->string('color')->trim()->toString());
        }

        if ($request->filled('price_from')) {
            $query->where('price_kop', '>=', $request->integer('price_from') * 100);
        }

        if ($request->filled('price_to')) {
            $query->where('price_kop', '<=', $request->integer('price_to') * 100);
        }

        if ($request->filled('active')) {
            $query->where('active', $request->boolean('active'));
        }

        if ($request->expectsJson()) {
            return response()->json(['status' => 'success', 'data' => $query->get()]);
        }

        return view('catalog.index', [
            'items' => $query->paginate(10)->withQueryString(),
            'categories' => Category::orderBy('name')->get(),
            'storageOptions' => Product::query()->distinct()->orderBy('storage_gb')->pluck('storage_gb'),
            'colorOptions' => Product::query()->distinct()->orderBy('color')->pluck('color'),
        ]);
    }

    public function create(): View
    {
        return view('catalog.create', $this->formDictionaries());
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $this->validated($request);
        $tags = $validated['tags'] ?? [];
        unset($validated['tags']);
        $validated['active'] = $request->boolean('active');

        $product = Product::create($validated);
        $product->tags()->sync($tags);
        $product->load(['category:id,name', 'tags:id,name']);

        if ($request->expectsJson()) {
            return response()->json(['status' => 'created', 'data' => $product], 201);
        }

        return redirect()->route('catalog.index')->with('success', 'Товар успішно створено.');
    }

    public function show(Product $product, Request $request): View|JsonResponse
    {
        $product->load(['category:id,name', 'tags:id,name']);

        if ($request->expectsJson()) {
            return response()->json(['status' => 'success', 'data' => $product]);
        }

        return view('catalog.show', ['product' => $product]);
    }

    public function edit(Product $product): View
    {
        $product->load('tags:id');

        return view('catalog.edit', ['product' => $product] + $this->formDictionaries());
    }

    public function update(Request $request, Product $product): RedirectResponse|JsonResponse
    {
        $validated = $this->validated($request, $product);
        $tags = $validated['tags'] ?? [];
        unset($validated['tags']);
        $validated['active'] = $request->boolean('active');

        $product->update($validated);
        $product->tags()->sync($tags);
        $product->load(['category:id,name', 'tags:id,name']);

        if ($request->expectsJson()) {
            return response()->json(['status' => 'updated', 'data' => $product]);
        }

        return redirect()->route('catalog.show', $product)->with('success', 'Товар успішно оновлено.');
    }

    public function destroy(Product $product, Request $request): RedirectResponse|JsonResponse
    {
        if ($product->reserved_qty > 0) {
            $message = 'Неможливо архівувати товар, доки для нього існує активний резерв.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 422)
                : back()->with('error', $message);
        }

        $hasOrderHistory = $product->orderItems()->exists();
        if ($hasOrderHistory) {
            $product->update(['active' => false]);
        } else {
            $product->delete();
        }

        if ($request->expectsJson()) {
            return response()->json(['status' => $hasOrderHistory ? 'archived' : 'deleted']);
        }

        return redirect()->route('catalog.index')->with(
            'success',
            $hasOrderHistory ? 'Товар архівовано, історію замовлень збережено.' : 'Товар видалено.',
        );
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        return $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'model_name' => ['required', 'string', 'min:2', 'max:100'],
            'color' => ['required', 'string', 'min:2', 'max:50'],
            'storage_gb' => ['required', 'integer', 'min:64', 'max:2048'],
            'sku' => ['required', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($product)],
            'price_kop' => ['required', 'integer', 'min:1'],
            'stock_qty' => ['required', 'integer', 'min:0'],
            'reserved_qty' => ['nullable', 'integer', 'min:0', 'lte:stock_qty'],
            'active' => ['nullable', 'boolean'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
        ]);
    }

    private function formDictionaries(): array
    {
        return [
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ];
    }
}
