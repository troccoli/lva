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
                <form method="POST" action="{{ route('clubs.store') }}">
                    @csrf
                    @include('CRUD.clubs._form', [
                    'nameDefaultValue' => '',
                    'venueDefaultValue' => null,
                    'submitText' => __('Add club')
                    ])
                </form>
            </div>
        </div>
    </div>

@endsection
