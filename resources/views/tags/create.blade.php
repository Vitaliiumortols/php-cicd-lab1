@extends('layouts.app')
@section('title', 'Новий тег')
@section('content')<div class="card mx-auto" style="max-width: 600px"><div class="card-header bg-white py-3"><h1 class="h3 mb-0">Новий тег</h1></div><div class="card-body"><form action="{{ route('tags.store') }}" method="POST">@csrf @include('tags._form', ['submitLabel' => 'Створити'])</form></div></div>@endsection
