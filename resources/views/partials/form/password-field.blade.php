<div class="form-label-group">
    <input type="password"
           id="{{ Str::camel('input-' . $fieldName) }}"
           class="form-control @error($fieldName) is-invalid @enderror"
           name="{{ $fieldName }}"
           dusk="{{ $fieldName }}-field"
           value="{{ old($fieldName) ?? $defaultValue ?? '' }}"
           placeholder="{{ $label }}"
           @if($required)required @endif
           autofocus
           autocomplete="{{ $fieldName }}"
    />
    <label for="{{ Str::camel('input-' . $fieldName) }}">{{ $label }}</label>

    @errorField()
</div>

@if($withConfirmation ?? false)
<div class="form-label-group">
    <input type="password"
           id="{{ Str::camel('input-confirm-' . $fieldName) }}"
           class="form-control"
           name="{{ $fieldName }}_confirmation"
           placeholder="{{ __('Confirm ' . strtolower($label)) }}"
           @if($required)required @endif
           autofocus
           autocomplete="{{ $fieldName }}"
    />
    <label for="{{ Str::camel('input-confirm-' . $fieldName) }}">{{ __('Confirm ' . strtolower($label)) }}</label>
</div>
@endif
