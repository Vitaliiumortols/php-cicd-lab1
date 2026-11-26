@props(['name', 'label', 'value' => null, 'required' => false, 'rows' => 3])
<div class="mb-3">
    <label for="{{ $name }}" class="form-label {{ $required ? 'required' : '' }}">{{ $label }}</label>
    <textarea name="{{ $name }}" id="{{ $name }}" rows="{{ $rows }}" {{ $required ? 'required' : '' }} {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($name)]) }}>{{ old($name, $value) }}</textarea>
    @error($name)<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
