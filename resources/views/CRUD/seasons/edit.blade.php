@extends('layouts.app')

@section('content')

    @component('components.forms.crud')
        @slot('title')
            {{ __('Edit the :season season', ['season' => $season->getName()]) }}
        @endslot

        <form method="post" action="{{ route('seasons.update', $season) }}">
            @csrf
            @method('PUT')
            @include('CRUD.seasons._form', ['submitText' => __('Save changes')])
        </form>
    @endcomponent

@endsection
