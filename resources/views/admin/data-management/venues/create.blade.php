@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Add new venue</h1>
        <hr/>

        @include('_partial.crud-errors')

        {!! Form::open(['route' => 'venues.store', 'class' => 'form-horizontal']) !!}

        @include('admin.data-management.venues._form', ['submitText' => 'Add'])

        {!! Form::close() !!}
    </div>

@endsection