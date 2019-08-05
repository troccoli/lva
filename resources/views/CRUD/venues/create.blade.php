@extends('layouts.app')

@section('content')

    @component('components.forms.crud')
        @slot('title')
            {{ __('Add a new venue') }}
        @endslot

        <form method="post" action="{{ route('venues.store') }}">
            @csrf
            @include('CRUD.venues._form', ['submitText' => __('Add venue')])
        </form>
    @endcomponent

@endsection
