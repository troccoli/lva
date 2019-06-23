<div class="form-label-group">
    <input type="text"
           id="{{ Str::camel('input-' . $fieldName) }}"
           class="form-control  @error($fieldName) is-invalid @enderror"
           name="{{ $fieldName }}"
           value="{{ old($fieldName) ?? $defaultValue ?? '' }}"
           placeholder="{{ $label }}"
           @if($required)required @endif
           autofocus
           autocomplete="{{ $fieldName }}"
    />
    <label for="{{ Str::camel('input-' . $fieldName) }}">{{ $label }}</label>

    @errorField()
</div>
