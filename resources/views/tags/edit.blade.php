@extends('layouts.app')
@section('title', 'Редагування тегу')
@section('content')<div class="card mx-auto" style="max-width: 600px"><div class="card-header bg-white py-3"><h1 class="h3 mb-0">Редагування тегу</h1></div><div class="card-body"><form action="{{ route('tags.update', $tag) }}" method="POST">@csrf @method('PUT') @include('tags._form', ['submitLabel' => 'Зберегти'])</form></div></div>@endsection
