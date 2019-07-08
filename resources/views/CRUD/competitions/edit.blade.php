@extends('layouts.app')

@section('content')

    @component('components.forms.crud')
        @slot('title')
            {{ __('Edit the :competition competition in season :season', ['competition' => $competition->getName(), 'season' => $season->getName()]) }}
        @endslot

        <form method="post" action="{{ route('competitions.update', $competition) }}">
            @csrf
            @method('PUT')
            @include('CRUD.competitions._form', ['submitText' => __('Save changes')])
        </form>
    @endcomponent

@endsection
