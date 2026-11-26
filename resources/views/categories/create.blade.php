@extends('layouts.app')
@section('title', 'Нова категорія')
@section('content')<div class="card mx-auto" style="max-width: 700px"><div class="card-header bg-white py-3"><h1 class="h3 mb-0">Нова категорія</h1></div><div class="card-body"><form action="{{ route('categories.store') }}" method="POST">@csrf @include('categories._form', ['submitLabel' => 'Створити'])</form></div></div>@endsection
