<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TagController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $tags = Tag::withCount('products')->orderBy('name');

        return $request->expectsJson()
            ? response()->json($tags->get())
            : view('tags.index', ['tags' => $tags->paginate(10)]);
    }

    public function create(): View
    {
        return view('tags.create');
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $tag = Tag::create($this->validated($request));

        return $request->expectsJson()
            ? response()->json($tag, 201)
            : redirect()->route('tags.index')->with('success', 'Тег створено.');
    }

    public function show(Tag $tag, Request $request): View|JsonResponse
    {
        $tag->load('products.category');

        return $request->expectsJson()
            ? response()->json($tag)
            : view('tags.show', ['tag' => $tag]);
    }

    public function edit(Tag $tag): View
    {
        return view('tags.edit', ['tag' => $tag]);
    }

    public function update(Request $request, Tag $tag): RedirectResponse|JsonResponse
    {
        $tag->update($this->validated($request, $tag));

        return $request->expectsJson()
            ? response()->json($tag)
            : redirect()->route('tags.show', $tag)->with('success', 'Тег оновлено.');
    }

    public function destroy(Tag $tag, Request $request): RedirectResponse|JsonResponse
    {
        $tag->delete();

        return $request->expectsJson()
            ? response()->json(['status' => 'deleted'])
            : redirect()->route('tags.index')->with('success', 'Тег видалено.');
    }

    private function validated(Request $request, ?Tag $tag = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('tags')->ignore($tag)],
            'slug' => ['required', 'string', 'max:100', 'alpha_dash:ascii', Rule::unique('tags')->ignore($tag)],
        ]);
    }
}
