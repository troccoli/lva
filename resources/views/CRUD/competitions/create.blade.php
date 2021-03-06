@extends('layouts.app')

@section('content')

    @component('components.forms.crud')
        @slot('title')
            {{ __('Add a new competition in the :season season', ['season' => $season->getName()]) }}
        @endslot

        <form method="post" action="{{ route('competitions.store', [$season]) }}">
            @csrf

            @include('CRUD.competitions._form', ['submitText' => __('Add competition')])
        </form>
    @endcomponent

@endsection
