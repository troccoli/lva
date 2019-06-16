@extends('layouts.app')

@section('content')

    @component('components.crud.crud-form')
        @slot('title')
            {{ __('Add a new season') }}
        @endslot

        <form method="post" action="{{ route('seasons.store') }}">
            @csrf
            @include('CRUD.seasons._form', ['submitText' => __('Add season')])
        </form>
    @endcomponent

@endsection
