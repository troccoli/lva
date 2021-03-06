<div class="form-group row">
    <label for="inputClub" class="col-sm-2 col-form-label">{{ __('Club') }}</label>
    <div class="col-sm-10">
        <input type="text"
               id="inputClub"
               class="form-control"
               name="club"
               dusk="club-field"
               value="{{ $club->getName() }}"
               disabled
        />
        <input type="hidden"
               name="club_id"
               value="{{ $club->getId() }}"
        />
    </div>
</div>

<div class="form-group row">
    <label for="inputName" class="col-sm-2 col-form-label">{{ __("Team's name") }}</label>
    <div class="col-sm-10">
        <input type="text"
               id="inputName"
               class="form-control @error('name') is-invalid @enderror"
               name="name"
               dusk="name-field"
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
    <label for="selectVenue" class="col-sm-2 col-form-label">{{ __('Venue') }}</label>
    <div class="col-sm-10">
        <select id="selectVenue"
                class="form-control @error('venue_id') is-invalid @enderror"
                name="venue_id"
                dusk="selectVenue-field"
        >
            <option value="" @if(null === $venueDefaultValue) selected @endif>{{ __("Club's venue (:venue)", ['venue' => $club->getVenue() ? $club->getVenue()->getName() : 'No venue']) }}</option>
            @foreach ($venues as $venue)
                <option value="{{ $venue->getId() }}" @if($venue->getId() === $venueDefaultValue) selected @endif>{{ $venue->getName() }}</option>
            @endforeach
        </select>
        @error('venue_id')
        <span class="invalid-feedback" role="alert" dusk="venue_id-error">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="form-group row">
    <div class="col-sm-10 offset-sm-2">
        <button type="submit" class="btn btn-primary" dusk="submit-button">{{ $submitText }}</button>
    </div>
</div>
