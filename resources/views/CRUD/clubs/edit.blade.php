@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col text-center">
                <h5>{{ __('Edit the :club club', ['club' => $club->getName()]) }}</h5>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <form method="post" action="{{ route('clubs.update', $club) }}">
                    @csrf
                    @method('PUT')
                    @include('CRUD.clubs._form', [
                    'nameDefaultValue' => $club->getName(),
                    'venueDefaultValue' => $club->getVenueId(),
                    'submitText' => __('Save changes')
                    ])
                </form>
            </div>
        </div>
    </div>

@endsection
