@extends('layouts.app')
@section('title', 'Редагування товару')
@section('content')
<div class="card mx-auto" style="max-width: 900px"><div class="card-header bg-white py-3"><h1 class="h3 mb-0">Редагування товару #{{ $product->id }}</h1></div><div class="card-body">
    <form action="{{ route('catalog.update', $product) }}" method="POST">@csrf @method('PUT') @include('catalog._form', ['submitLabel' => 'Зберегти зміни'])</form>
</div></div>
@endsection
