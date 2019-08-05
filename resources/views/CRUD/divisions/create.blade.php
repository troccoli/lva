@extends('layouts.app')

@section('content')

    @component('components.forms.crud')
        @slot('title')
            {{ __('Add a new division for the :competition :season competition', [
                'competition' => $competition->getName(),
                'season' => $competition->getSeason()->getName(),
                ]) }}
        @endslot

        <form method="post" action="{{ route('divisions.store', [$competition]) }}">
            @csrf

            @include('CRUD.divisions._form', ['submitText' => __('Add division')])
        </form>
    @endcomponent

@endsection
