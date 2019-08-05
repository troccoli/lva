@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col text-center">
                <h5>{{ __('Edit the :team (:club) team', ['team' => $team->getName(), 'club' => $club->getName()]) }}</h5>
            </div>
        </div>
        <div class="row">
            <div class="col">
                {{ Form::model($team, ['route' => ['teams.update', $club, $team], 'method' => 'PUT']) }}
                @include('CRUD.teams._form', [
                'nameDefaultValue' => $team->getName(),
                'venueDefaultValue' => $team->getVenueId(),
                'submitText' => __('Save changes')
                ])
                {{ Form::close() }}
            </div>
        </div>
    </div>

@endsection
