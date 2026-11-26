@extends('layouts.app')
@section('title', $product->model_name)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><div><a href="{{ route('catalog.index') }}" class="text-decoration-none">← До каталогу</a><h1 class="page-heading mt-2">{{ $product->model_name }}</h1></div><div class="actions"><a class="btn btn-warning" href="{{ route('catalog.edit', $product) }}">Редагувати</a>@include('partials.delete-button', ['action' => route('catalog.destroy', $product)])</div></div>
<div class="card"><div class="card-body"><div class="row g-4">
    <div class="col-md-6"><h2 class="h5">Основні дані</h2><dl class="row mb-0"><dt class="col-5">SKU</dt><dd class="col-7">{{ $product->sku }}</dd><dt class="col-5">Категорія</dt><dd class="col-7">{{ $product->category?->name ?? '—' }}</dd><dt class="col-5">Колір</dt><dd class="col-7">{{ $product->color }}</dd><dt class="col-5">Пам'ять</dt><dd class="col-7">{{ $product->storage_gb }} GB</dd></dl></div>
    <div class="col-md-6"><h2 class="h5">Продаж</h2><dl class="row mb-0"><dt class="col-5">Ціна</dt><dd class="col-7 money">{{ number_format($product->price_kop / 100, 2, ',', ' ') }} грн</dd><dt class="col-5">На складі</dt><dd class="col-7">{{ $product->stock_qty }}</dd><dt class="col-5">Резерв</dt><dd class="col-7">{{ $product->reserved_qty }}</dd><dt class="col-5">Доступно</dt><dd class="col-7"><strong>{{ $product->availableQty() }}</strong></dd></dl></div>
    <div class="col-12"><h2 class="h5">Теги</h2>@forelse($product->tags as $tag)<a href="{{ route('tags.show', $tag) }}" class="badge text-bg-secondary text-decoration-none me-1">{{ $tag->name }}</a>@empty<span class="text-muted">Тегів немає.</span>@endforelse</div>
</div></div></div>
@endsection
