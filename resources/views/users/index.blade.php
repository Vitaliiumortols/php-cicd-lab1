@extends('layouts.app')
@section('title', 'Користувачі')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><div><h1 class="page-heading mb-1">Користувачі</h1><p class="text-muted mb-0">Покупці, менеджери та адміністратори</p></div><a href="{{ route('users.create') }}" class="btn btn-primary">Додати користувача</a></div>
<div class="card"><div class="table-responsive"><table class="table table-hover mb-0"><thead class="table-dark"><tr><th>ID</th><th>Ім'я</th><th>Контакти</th><th>Роль</th><th>Кошик</th><th>Замовлень</th><th>Статус</th><th>Дії</th></tr></thead><tbody>
@forelse($users as $user)<tr><td>{{ $user->id }}</td><td><strong>{{ $user->name }}</strong></td><td>{{ $user->email }}<div class="small text-muted">{{ $user->phone ?: 'Телефон не вказано' }}</div></td><td><span class="badge text-bg-info">{{ $user->role }}</span></td><td>@if($user->cart)<a href="{{ route('carts.show', $user->cart) }}">#{{ $user->cart->id }}</a>@else—@endif</td><td>{{ $user->orders_count }}</td><td><span class="badge {{ $user->active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $user->active ? 'Активний' : 'Вимкнений' }}</span></td><td><div class="actions"><a class="btn btn-sm btn-outline-info" href="{{ route('users.show', $user) }}">Перегляд</a><a class="btn btn-sm btn-outline-warning" href="{{ route('users.edit', $user) }}">Редагувати</a>@include('partials.delete-button', ['action' => route('users.destroy', $user)])</div></td></tr>
@empty<tr><td colspan="8" class="text-center text-muted py-5">Користувачів немає.</td></tr>@endforelse
</tbody></table></div></div><div class="mt-3">{{ $users->links() }}</div>
@endsection
