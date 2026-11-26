@php
    $formItems = old('items', isset($order) ? $order->items->map(fn($item) => ['product_id' => $item->product_id, 'quantity' => $item->quantity])->values()->all() : [['product_id' => '', 'quantity' => 1]]);
@endphp
<div class="row"><div class="col-md-6"><x-form.select name="user_id" label="Користувач" required><option value="">Оберіть користувача</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected((string) old('user_id', $order->user_id ?? '') === (string) $user->id)>{{ $user->name }} · {{ $user->email }}</option>@endforeach</x-form.select></div>
@if(isset($order))<div class="col-md-3"><x-form.select name="status" label="Статус" required>@foreach(['pending','confirmed','processing','completed','cancelled'] as $status)<option value="{{ $status }}" @selected(old('status', $order->status) === $status)>{{ $status }}</option>@endforeach</x-form.select></div><div class="col-md-3"><x-form.select name="payment_status" label="Оплата" required>@foreach(['unpaid','paid','refunded'] as $status)<option value="{{ $status }}" @selected(old('payment_status', $order->payment_status) === $status)>{{ $status }}</option>@endforeach</x-form.select></div>@endif</div>
<x-form.textarea name="comment" label="Коментар" :value="$order->comment ?? null" rows="3" />
<div class="d-flex justify-content-between align-items-center mb-2"><label class="form-label mb-0 required">Позиції замовлення</label><button type="button" id="add-item" class="btn btn-sm btn-outline-primary">Додати позицію</button></div>
<div id="order-items">
@foreach($formItems as $index => $row)
    <div class="row g-2 order-item align-items-end mb-2">
        <div class="col-md-8"><label class="form-label">Товар</label><select class="form-select @error("items.$index.product_id") is-invalid @enderror" name="items[{{ $index }}][product_id]" required><option value="">Оберіть товар</option>@foreach($products as $product)<option value="{{ $product->id }}" @selected((string) ($row['product_id'] ?? '') === (string) $product->id)>{{ $product->model_name }} · {{ $product->storage_gb }} GB · {{ number_format($product->price_kop / 100, 2, ',', ' ') }} грн · доступно {{ $product->availableQty() }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">Кількість</label><input class="form-control @error("items.$index.quantity") is-invalid @enderror" type="number" name="items[{{ $index }}][quantity]" min="1" max="99" value="{{ $row['quantity'] ?? 1 }}" required></div>
        <div class="col-md-2 d-grid"><button type="button" class="btn btn-outline-danger remove-item">Прибрати</button></div>
    </div>
@endforeach
</div>
<div class="mt-4"><button class="btn btn-primary">{{ $submitLabel }}</button><a href="{{ isset($order) ? route('orders.show', $order) : route('orders.index') }}" class="btn btn-outline-secondary">Скасувати</a></div>
@push('scripts')
<template id="item-template"><div class="row g-2 order-item align-items-end mb-2"><div class="col-md-8"><label class="form-label">Товар</label><select class="form-select" name="items[__INDEX__][product_id]" required><option value="">Оберіть товар</option>@foreach($products as $product)<option value="{{ $product->id }}">{{ $product->model_name }} · {{ $product->storage_gb }} GB · {{ number_format($product->price_kop / 100, 2, ',', ' ') }} грн · доступно {{ $product->availableQty() }}</option>@endforeach</select></div><div class="col-md-2"><label class="form-label">Кількість</label><input class="form-control" type="number" name="items[__INDEX__][quantity]" min="1" max="99" value="1" required></div><div class="col-md-2 d-grid"><button type="button" class="btn btn-outline-danger remove-item">Прибрати</button></div></div></template>
<script>
(() => {
    const box = document.getElementById('order-items');
    const template = document.getElementById('item-template').innerHTML;
    let index = {{ count($formItems) }};
    document.getElementById('add-item').addEventListener('click', () => { box.insertAdjacentHTML('beforeend', template.replaceAll('__INDEX__', index++)); });
    box.addEventListener('click', event => { if (event.target.classList.contains('remove-item') && box.querySelectorAll('.order-item').length > 1) event.target.closest('.order-item').remove(); });
})();
</script>
@endpush
