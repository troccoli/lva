<div class="form-label-group">
    <select id="{{ Str::camel('input-' . $fieldName) }}"
            class="form-control  @error($fieldName) is-invalid @enderror"
            name="{{ $fieldName }}"
            dusk="{{ $fieldName }}-field">
        @foreach($options as $optionLabel)
        <option value="{{ $loop->index }}" @if($defaultValue === $loop->index) selected="" @endif>{{ $optionLabel }}</option>
        @endforeach
    </select>
    <label for="{{ Str::camel('input-' . $fieldName) }}">{{ $label }}</label>

    @errorField()
</div>
