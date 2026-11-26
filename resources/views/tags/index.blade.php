@extends('layouts.app')
@section('title', 'Теги')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><div><h1 class="page-heading mb-1">Теги</h1><p class="text-muted mb-0">Зв'язок M:M з товарами</p></div><a href="{{ route('tags.create') }}" class="btn btn-primary">Додати тег</a></div>
<div class="card"><div class="table-responsive"><table class="table table-hover mb-0"><thead class="table-dark"><tr><th>ID</th><th>Назва</th><th>Slug</th><th>Товарів</th><th>Дії</th></tr></thead><tbody>@forelse($tags as $tag)<tr><td>{{ $tag->id }}</td><td><span class="badge text-bg-secondary">{{ $tag->name }}</span></td><td><code>{{ $tag->slug }}</code></td><td>{{ $tag->products_count }}</td><td><div class="actions"><a class="btn btn-sm btn-outline-info" href="{{ route('tags.show', $tag) }}">Перегляд</a><a class="btn btn-sm btn-outline-warning" href="{{ route('tags.edit', $tag) }}">Редагувати</a>@include('partials.delete-button', ['action' => route('tags.destroy', $tag)])</div></td></tr>@empty<tr><td colspan="5" class="text-center text-muted py-5">Тегів немає.</td></tr>@endforelse</tbody></table></div></div><div class="mt-3">{{ $tags->links() }}</div>
@endsection
