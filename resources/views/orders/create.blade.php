@extends('layouts.app')
@section('title', 'Нове замовлення')
@section('content')<div class="card mx-auto" style="max-width: 950px"><div class="card-header bg-white py-3"><h1 class="h3 mb-0">Нове замовлення</h1></div><div class="card-body"><form action="{{ route('orders.store') }}" method="POST">@csrf @include('orders._form', ['submitLabel' => 'Створити замовлення'])</form></div></div>@endsection
