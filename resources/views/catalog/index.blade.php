@extends('layouts.app')
@section('title', 'Каталог товарів')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
    <div><h1 class="page-heading mb-1">Каталог товарів</h1><p class="text-muted mb-0">Керування моделями, категоріями та тегами</p></div>
    <a href="{{ route('catalog.create') }}" class="btn btn-primary">Додати товар</a>
</div>
<div class="card mb-3"><div class="card-body">
    <form method="GET" class="row g-2">
        <div class="col-lg-4"><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Модель, SKU або колір"></div>
        <div class="col-lg-2 col-md-4"><select class="form-select" name="category"><option value="">Усі категорії</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>@endforeach</select></div>
        <div class="col-lg-2 col-md-4"><select class="form-select" name="storage_gb"><option value="">Будь-яка пам'ять</option>@foreach($storageOptions as $storage)<option value="{{ $storage }}" @selected((string) request('storage_gb') === (string) $storage)>{{ $storage }} GB</option>@endforeach</select></div>
        <div class="col-lg-2 col-md-4"><select class="form-select" name="color"><option value="">Усі кольори</option>@foreach($colorOptions as $color)<option value="{{ $color }}" @selected(request('color') === $color)>{{ $color }}</option>@endforeach</select></div>
        <div class="col-lg-2 col-md-4"><select class="form-select" name="active"><option value="">Усі статуси</option><option value="1" @selected(request('active') === '1')>Активні</option><option value="0" @selected(request('active') === '0')>Неактивні</option></select></div>
        <div class="col-lg-2 col-md-4"><input class="form-control" type="number" min="0" name="price_from" value="{{ request('price_from') }}" placeholder="Ціна від, грн"></div>
        <div class="col-lg-2 col-md-4"><input class="form-control" type="number" min="0" name="price_to" value="{{ request('price_to') }}" placeholder="Ціна до, грн"></div>
        <div class="col-lg-2 col-md-4 d-grid"><button class="btn btn-outline-primary">Застосувати</button></div>
        <div class="col-lg-2 col-md-4 d-grid"><a class="btn btn-outline-secondary" href="{{ route('catalog.index') }}">Скинути</a></div>
    </form>
</div></div>
<div class="card"><div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead class="table-dark"><tr><th>ID</th><th>Модель</th><th>Категорія</th><th>Теги</th><th>Ціна</th><th>Залишок</th><th>Статус</th><th>Дії</th></tr></thead>
        <tbody>
        @forelse($items as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td><strong>{{ $item->model_name }}</strong><div class="small text-muted">{{ $item->storage_gb }} GB · {{ $item->color }} · {{ $item->sku }}</div></td>
                <td>{{ $item->category?->name ?? 'Без категорії' }}</td>
                <td>@forelse($item->tags as $tag)<span class="badge text-bg-secondary me-1">{{ $tag->name }}</span>@empty<span class="text-muted">—</span>@endforelse</td>
                <td class="money">{{ number_format($item->price_kop / 100, 2, ',', ' ') }} грн</td>
                <td>{{ $item->availableQty() }} / {{ $item->stock_qty }}</td>
                <td><span class="badge {{ $item->active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $item->active ? 'Активний' : 'Вимкнений' }}</span></td>
                <td><div class="actions"><a class="btn btn-sm btn-outline-info" href="{{ route('catalog.show', $item) }}">Перегляд</a><a class="btn btn-sm btn-outline-warning" href="{{ route('catalog.edit', $item) }}">Редагувати</a>@include('partials.delete-button', ['action' => route('catalog.destroy', $item)])</div></td>
            </tr>
        @empty
            <tr><td colspan="8" class="text-center text-muted py-5">Товарів не знайдено.</td></tr>
        @endforelse
        </tbody>
    </table>
</div></div>
<div class="mt-3">{{ $items->links() }}</div>
@endsection
