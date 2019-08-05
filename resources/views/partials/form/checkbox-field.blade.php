<div class="custom-control custom-checkbox mb-3">
    <input type="checkbox"
           id="{{ Str::camel('input-' . $fieldName) }}"
           class="custom-control-input"
           name="{{ $fieldName }}"
           dusk="{{ $fieldName }}-field"
           {{ old($fieldName) ? 'checked' : '' }}>
    <label for="{{ Str::camel('input-' . $fieldName) }}" class="custom-control-label">{{ $label }}</label>
</div>
