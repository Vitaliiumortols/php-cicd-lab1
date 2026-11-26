<!doctype html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'iPhoneHub') · iPhoneHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; color: #182230; }
        .navbar-brand { font-weight: 800; letter-spacing: -.04em; }
        .card { border: 0; border-radius: 1rem; box-shadow: 0 .35rem 1.5rem rgba(20, 33, 61, .07); }
        .table > :not(caption) > * > * { padding: .85rem .75rem; vertical-align: middle; }
        .page-heading { font-weight: 800; letter-spacing: -.04em; }
        .stat-card { background: linear-gradient(135deg, #fff, #eef4ff); }
        .badge { font-weight: 600; }
        .form-label { font-weight: 600; }
        .required::after { content: ' *'; color: #dc3545; }
        .actions { display: flex; flex-wrap: wrap; gap: .35rem; }
        .money { white-space: nowrap; font-variant-numeric: tabular-nums; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-xl navbar-dark bg-dark shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">iPhoneHub</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('catalog.*') ? 'active' : '' }}" href="{{ route('catalog.index') }}">Товари</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">Категорії</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('tags.*') ? 'active' : '' }}" href="{{ route('tags.index') }}">Теги</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">Користувачі</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('carts.*') ? 'active' : '' }}" href="{{ route('carts.index') }}">Кошики</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}">Замовлення</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('shipments.*') ? 'active' : '' }}" href="{{ route('shipments.index') }}">Доставки</a></li>
            </ul>
        </div>
    </div>
</nav>

<main class="container pb-5">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Форму не збережено.</strong> Виправте помилки нижче.
            <ul class="mb-0 mt-2">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
