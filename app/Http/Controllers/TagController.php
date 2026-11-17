<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TagController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Tag::withCount('products')->orderBy('name')->get());
    }

    public function store(Request $request): JsonResponse
    {
        return response()->json(Tag::create($this->validated($request)), 201);
    }

    public function show(Tag $tag): JsonResponse
    {
        return response()->json($tag->load('products.category'));
    }

    public function update(Request $request, Tag $tag): JsonResponse
    {
        $tag->update($this->validated($request, $tag));

        return response()->json($tag);
    }

    public function destroy(Tag $tag): JsonResponse
    {
        $tag->delete();

        return response()->json(['status' => 'deleted']);
    }

    private function validated(Request $request, ?Tag $tag = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('tags')->ignore($tag)],
            'slug' => ['required', 'string', 'max:100', Rule::unique('tags')->ignore($tag)],
        ]);
    }
}
