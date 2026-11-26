@extends('layouts.app')
@section('title', 'Редагування замовлення')
@section('content')<div class="card mx-auto" style="max-width: 950px"><div class="card-header bg-white py-3"><h1 class="h3 mb-0">Редагування {{ $order->number }}</h1></div><div class="card-body"><form action="{{ route('orders.update', $order) }}" method="POST">@csrf @method('PUT') @include('orders._form', ['submitLabel' => 'Зберегти зміни'])</form></div></div>@endsection
