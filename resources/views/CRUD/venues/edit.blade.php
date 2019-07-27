@extends('layouts.app')

@section('content')

    @component('components.forms.crud')
        @slot('title')
            {{ __('Edit the :venue venue', ['venue' => $venue->getName()]) }}
        @endslot

        <form method="post" action="{{ route('venues.update', $venue) }}">
            @csrf
            @method('PUT')
            @include('CRUD.venues._form', ['submitText' => __('Save changes')])
        </form>
    @endcomponent

@endsection
