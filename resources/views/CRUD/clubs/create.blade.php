@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col text-center">
                <h5>{{ __('Add a new club') }}</h5>
            </div>
        </div>
        <div class="row">
            <div class="col">
                {{ Form::open(['route' => 'clubs.store']) }}
                @include('CRUD.clubs._form', ['submitText' => __('Add club')])
                {{ Form::close() }}
            </div>
        </div>
    </div>

@endsection
