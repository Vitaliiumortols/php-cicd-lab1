@extends('layouts.app')
@section('title', 'Редагування кошика')
@section('content')<div class="card mx-auto" style="max-width: 700px"><div class="card-header bg-white py-3"><h1 class="h3 mb-0">Редагування кошика #{{ $cart->id }}</h1></div><div class="card-body"><form action="{{ route('carts.update', $cart) }}" method="POST">@csrf @method('PUT') @include('carts._form', ['submitLabel' => 'Зберегти'])</form></div></div>@endsection
