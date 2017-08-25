@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Add new team</h1>
        <hr/>

        @include('_partial.crud-errors')

        {!! Form::open(['route' => 'teams.store', 'class' => 'form-horizontal']) !!}

        @include('admin.data-management.teams._form', ['submitText' => 'Add'])

        {!! Form::close() !!}
    </div>

@endsection