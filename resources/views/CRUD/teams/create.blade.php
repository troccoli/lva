@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col text-center">
                <h5>{{ __('Add a new team in the :club club', ['club' => $club->getName()]) }}</h5>
            </div>
        </div>
        <div class="row">
            <div class="col">
                {{ Form::open(['route' => ['teams.store', $club]]) }}
                @include('CRUD.teams._form', [
                    'nameDefaultValue' => '',
                    'venueDefaultValue' => null,
                    'submitText' => __('Add team')
                ])
                {{ Form::close() }}
            </div>
        </div>
    </div>

@endsection
