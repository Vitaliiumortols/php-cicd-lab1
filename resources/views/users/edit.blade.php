@extends('layouts.app')
@section('title', 'Редагування користувача')
@section('content')<div class="card mx-auto" style="max-width: 800px"><div class="card-header bg-white py-3"><h1 class="h3 mb-0">Редагування користувача</h1></div><div class="card-body"><form action="{{ route('users.update', $user) }}" method="POST">@csrf @method('PUT') @include('users._form', ['submitLabel' => 'Зберегти'])</form></div></div>@endsection
