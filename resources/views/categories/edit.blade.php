@extends('layouts.app')
@section('title', 'Редагування категорії')
@section('content')<div class="card mx-auto" style="max-width: 700px"><div class="card-header bg-white py-3"><h1 class="h3 mb-0">Редагування категорії</h1></div><div class="card-body"><form action="{{ route('categories.update', $category) }}" method="POST">@csrf @method('PUT') @include('categories._form', ['submitLabel' => 'Зберегти'])</form></div></div>@endsection
