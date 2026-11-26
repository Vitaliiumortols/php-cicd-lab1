@extends('layouts.app')
@section('title', $tag->name)
@section('content')
<div class="d-flex justify-content-between align-items-start mb-3"><div><a href="{{ route('tags.index') }}" class="text-decoration-none">← Теги</a><h1 class="page-heading mt-2"><span class="badge text-bg-secondary">{{ $tag->name }}</span></h1><code>{{ $tag->slug }}</code></div><div class="actions"><a href="{{ route('tags.edit', $tag) }}" class="btn btn-warning">Редагувати</a>@include('partials.delete-button', ['action' => route('tags.destroy', $tag)])</div></div>
<div class="card"><div class="card-header bg-white"><h2 class="h5 mb-0">Позначені товари ({{ $tag->products->count() }})</h2></div><div class="list-group list-group-flush">@forelse($tag->products as $product)<a class="list-group-item list-group-item-action d-flex justify-content-between" href="{{ route('catalog.show', $product) }}"><span>{{ $product->model_name }} <span class="text-muted">· {{ $product->category?->name }}</span></span><span class="money">{{ number_format($product->price_kop / 100, 2, ',', ' ') }} грн</span></a>@empty<div class="list-group-item text-muted">Цим тегом ще не позначено товарів.</div>@endforelse</div></div>
@endsection
