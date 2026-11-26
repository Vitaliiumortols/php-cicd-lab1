@extends('layouts.app')
@section('title', 'Новий товар')
@section('content')
<div class="card mx-auto" style="max-width: 900px"><div class="card-header bg-white py-3"><h1 class="h3 mb-0">Створення товару</h1></div><div class="card-body">
    <form action="{{ route('catalog.store') }}" method="POST">@csrf @include('catalog._form', ['submitLabel' => 'Створити товар'])</form>
</div></div>
@endsection
