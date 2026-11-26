@php($selectedTags = old('tags', isset($product) ? $product->tags->pluck('id')->all() : []))
<div class="row">
    <div class="col-md-8"><x-form.input name="model_name" label="Модель" :value="$product->model_name ?? null" required /></div>
    <div class="col-md-4"><x-form.input name="sku" label="Артикул / SKU" :value="$product->sku ?? null" required /></div>
    <div class="col-md-4"><x-form.input name="color" label="Колір" :value="$product->color ?? null" required /></div>
    <div class="col-md-4"><x-form.input name="storage_gb" label="Пам'ять, GB" type="number" min="64" step="64" :value="$product->storage_gb ?? 128" required /></div>
    <div class="col-md-4"><x-form.input name="price_kop" label="Ціна, копійки" type="number" min="1" :value="$product->price_kop ?? null" help="Наприклад: 5499900 = 54 999,00 грн" required /></div>
    <div class="col-md-6">
        <x-form.select name="category_id" label="Категорія">
            <option value="">Без категорії</option>
            @foreach($categories as $category)<option value="{{ $category->id }}" @selected((string) old('category_id', $product->category_id ?? '') === (string) $category->id)>{{ $category->name }}</option>@endforeach
        </x-form.select>
    </div>
    <div class="col-md-3"><x-form.input name="stock_qty" label="На складі" type="number" min="0" :value="$product->stock_qty ?? 0" required /></div>
    <div class="col-md-3"><x-form.input name="reserved_qty" label="Зарезервовано" type="number" min="0" :value="$product->reserved_qty ?? 0" /></div>
</div>
<div class="mb-3">
    <label class="form-label">Теги / маркери (зв'язок M:M)</label>
    <div class="border rounded-3 p-3 bg-light">
        @forelse($tags as $tag)
            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="tags[]" id="tag_{{ $tag->id }}" value="{{ $tag->id }}" @checked(in_array($tag->id, $selectedTags))><label class="form-check-label" for="tag_{{ $tag->id }}">{{ $tag->name }}</label></div>
        @empty<span class="text-muted">Спочатку створіть хоча б один тег.</span>@endforelse
    </div>
    @error('tags')<div class="text-danger small">{{ $message }}</div>@enderror
</div>
<div class="form-check form-switch mb-4"><input type="hidden" name="active" value="0"><input class="form-check-input" type="checkbox" name="active" id="active" value="1" @checked(old('active', $product->active ?? true))><label class="form-check-label" for="active">Товар активний</label></div>
<button class="btn btn-primary">{{ $submitLabel }}</button>
<a href="{{ isset($product) ? route('catalog.show', $product) : route('catalog.index') }}" class="btn btn-outline-secondary">Скасувати</a>
