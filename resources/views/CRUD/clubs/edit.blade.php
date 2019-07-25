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
                {{ Form::model($club, ['route' => ['clubs.update', $club], 'method' => 'PUT']) }}
                @include('CRUD.clubs._form', [
                'nameDefaultValue' => $club->getName(),
                'submitText' => __('Save changes')
                ])
                {{ Form::close() }}
            </div>
        </div>
    </div>

@endsection
