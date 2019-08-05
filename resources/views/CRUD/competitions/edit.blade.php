@extends('layouts.app')

@section('content')

    @component('components.forms.crud')
        @slot('title')
            {{ __('Edit the :competition :season competition', ['competition' => $competition->getName(), 'season' => $season->getName()]) }}
        @endslot

        <form method="post" action="{{ route('competitions.update', [$season, $competition]) }}">
            @csrf
            @method('PUT')
            @include('CRUD.competitions._form', ['submitText' => __('Save changes')])
        </form>
    @endcomponent

@endsection
