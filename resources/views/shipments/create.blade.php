@extends('layouts.app')
@section('title', 'Нова доставка')
@section('content')<div class="card mx-auto" style="max-width: 800px"><div class="card-header bg-white py-3"><h1 class="h3 mb-0">Нова доставка</h1></div><div class="card-body"><form action="{{ route('shipments.store') }}" method="POST">@csrf @include('shipments._form', ['submitLabel' => 'Створити доставку'])</form></div></div>@endsection
