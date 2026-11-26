@extends('layouts.app')
@section('title', 'Категорії')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><div><h1 class="page-heading mb-1">Категорії</h1><p class="text-muted mb-0">Зв'язок 1:M з товарами</p></div><a href="{{ route('categories.create') }}" class="btn btn-primary">Додати категорію</a></div>
<div class="card"><div class="table-responsive"><table class="table table-hover mb-0"><thead class="table-dark"><tr><th>ID</th><th>Назва</th><th>Slug</th><th>Товарів</th><th>Статус</th><th>Дії</th></tr></thead><tbody>
@forelse($categories as $category)<tr><td>{{ $category->id }}</td><td><strong>{{ $category->name }}</strong></td><td><code>{{ $category->slug }}</code></td><td>{{ $category->products_count }}</td><td><span class="badge {{ $category->active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $category->active ? 'Активна' : 'Вимкнена' }}</span></td><td><div class="actions"><a class="btn btn-sm btn-outline-info" href="{{ route('categories.show', $category) }}">Перегляд</a><a class="btn btn-sm btn-outline-warning" href="{{ route('categories.edit', $category) }}">Редагувати</a>@include('partials.delete-button', ['action' => route('categories.destroy', $category)])</div></td></tr>
@empty<tr><td colspan="6" class="text-center text-muted py-5">Категорій немає.</td></tr>@endforelse
</tbody></table></div></div><div class="mt-3">{{ $categories->links() }}</div>
@endsection
