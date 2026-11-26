@props(['name', 'label', 'required' => false])
<div class="mb-3">
    <label for="{{ $name }}" class="form-label {{ $required ? 'required' : '' }}">{{ $label }}</label>
    <select name="{{ $name }}" id="{{ $name }}" {{ $required ? 'required' : '' }} {{ $attributes->class(['form-select', 'is-invalid' => $errors->has($name)]) }}>{{ $slot }}</select>
    @error($name)<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
