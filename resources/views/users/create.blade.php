@extends('layouts.app')
@section('title', 'Новий користувач')
@section('content')<div class="card mx-auto" style="max-width: 800px"><div class="card-header bg-white py-3"><h1 class="h3 mb-0">Новий користувач</h1></div><div class="card-body"><form action="{{ route('users.store') }}" method="POST">@csrf @include('users._form', ['submitLabel' => 'Створити'])</form></div></div>@endsection
