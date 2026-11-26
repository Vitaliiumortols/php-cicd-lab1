<x-form.input name="name" label="Назва" :value="$tag->name ?? null" required />
<x-form.input name="slug" label="Slug" :value="$tag->slug ?? null" pattern="[A-Za-z0-9_-]+" help="Латинські літери, цифри, дефіси та підкреслення" required />
<button class="btn btn-primary">{{ $submitLabel }}</button><a href="{{ isset($tag) ? route('tags.show', $tag) : route('tags.index') }}" class="btn btn-outline-secondary">Скасувати</a>
