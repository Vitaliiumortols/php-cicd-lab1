@extends('layouts.app')
@section('title', 'Новий кошик')
@section('content')<div class="card mx-auto" style="max-width: 700px"><div class="card-header bg-white py-3"><h1 class="h3 mb-0">Новий кошик</h1></div><div class="card-body"><form action="{{ route('carts.store') }}" method="POST">@csrf @include('carts._form', ['submitLabel' => 'Створити'])</form></div></div>@endsection
