@extends('layouts.app')
@section('title', 'Головна')
@section('content')
<div class="p-5 mb-4 bg-dark text-white rounded-4 shadow">
    <span class="badge text-bg-primary mb-3">Лабораторна робота №7</span>
    <h1 class="display-5 fw-bold">Система керування iPhoneHub</h1>
    <p class="col-lg-8 fs-5 text-white-50">Повний серверний інтерфейс Laravel Blade: CRUD, зв'язки Eloquent, HTML-форми, валідація, CSRF та Method Spoofing.</p>
    <a href="{{ route('catalog.index') }}" class="btn btn-primary btn-lg">Перейти до каталогу</a>
</div>
<div class="row g-3">
    @foreach ([
        ['Товари', 'products', 'catalog.index'], ['Категорії', 'categories', 'categories.index'], ['Теги', 'tags', 'tags.index'],
        ['Користувачі', 'users', 'users.index'], ['Кошики', 'carts', 'carts.index'], ['Замовлення', 'orders', 'orders.index'], ['Доставки', 'shipments', 'shipments.index']
    ] as [$label, $key, $route])
        <div class="col-6 col-md-4 col-xl">
            <a href="{{ route($route) }}" class="text-decoration-none text-dark">
                <div class="card stat-card h-100"><div class="card-body"><div class="text-muted small">{{ $label }}</div><div class="display-6 fw-bold">{{ $counts[$key] }}</div></div></div>
            </a>
        </div>
    @endforeach
</div>
@endsection
