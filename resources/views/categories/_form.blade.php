<x-form.input name="name" label="Назва" :value="$category->name ?? null" required />
<x-form.input name="slug" label="Slug" :value="$category->slug ?? null" pattern="[A-Za-z0-9_-]+" help="Латинські літери, цифри, дефіси та підкреслення" required />
<x-form.textarea name="description" label="Опис" :value="$category->description ?? null" rows="4" />
<div class="form-check form-switch mb-4"><input type="hidden" name="active" value="0"><input class="form-check-input" type="checkbox" name="active" id="active" value="1" @checked(old('active', $category->active ?? true))><label class="form-check-label" for="active">Категорія активна</label></div>
<button class="btn btn-primary">{{ $submitLabel }}</button><a href="{{ isset($category) ? route('categories.show', $category) : route('categories.index') }}" class="btn btn-outline-secondary">Скасувати</a>
