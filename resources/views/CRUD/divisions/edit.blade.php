@extends('layouts.app')

@section('content')

    @component('components.forms.crud')
        @slot('title')
            {{ __('Edit the :division division in the :competition :season competition', [
                'division' => $division->getName(),
                'competition' => $competition->getName(),
                'season' => $competition->getSeason()->getName()]
                ) }}
        @endslot

        <form method="post" action="{{ route('divisions.update', [$competition, $division]) }}">
            @csrf
            @method('PUT')
            @include('CRUD.divisions._form', ['submitText' => __('Save changes')])
        </form>
    @endcomponent

@endsection
