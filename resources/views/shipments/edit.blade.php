@extends('layouts.app')
@section('title', 'Редагування доставки')
@section('content')<div class="card mx-auto" style="max-width: 800px"><div class="card-header bg-white py-3"><h1 class="h3 mb-0">Редагування доставки #{{ $shipment->id }}</h1></div><div class="card-body"><form action="{{ route('shipments.update', $shipment) }}" method="POST">@csrf @method('PUT') @include('shipments._form', ['submitLabel' => 'Зберегти зміни'])</form></div></div>@endsection
