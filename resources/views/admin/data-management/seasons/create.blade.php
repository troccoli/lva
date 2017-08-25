@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Add a new season</h1>
        <hr/>

        @include('_partial.crud-errors')

        {!! Form::open(['route' => 'seasons.store', 'class' => 'form-horizontal']) !!}

        @include('admin.data-management.seasons._form', ['submitText' => 'Add'])

        {!! Form::close() !!}
    </div>

@endsection