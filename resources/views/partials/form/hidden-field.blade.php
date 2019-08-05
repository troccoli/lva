<div class="form-label-group">
    <input type="hidden"
           id="{{ Str::camel('input-' . $fieldName) }}"
           name="{{ $fieldName }}"
           dusk="{{ $fieldName }}-field"
           value="{{ old($fieldName) ?? $defaultValue ?? '' }}"
           @if($required)required @endif
    />
</div>
