<div class="form-group row">
    <label for="inputName" class="col-sm-2 col-form-label">{{ __('Name') }}</label>
    <div class="col-sm-10">
        <input type="text"
               id="inputName"
               class="form-control @error('name') is-invalid @enderror"
               name="name"
               dusk="inputName-field"
               value="{{ old('name') ?? $nameDefaultValue ?? '' }}"
               placeholder="{{ __('Name') }}"
               required
               autofocus
               autocomplete="inputName"
        />
        @error('name')
        <span class="invalid-feedback" role="alert" dusk="name-error">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="form-group row">
    <div class="col-sm-10 offset-sm-2">
        <button type="submit" class="btn btn-primary" dusk="submit-button">{{ $submitText }}</button>
    </div>
</div>
