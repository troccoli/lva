@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Add a new club</h1>
        <hr/>

        @include('_partial.crud-errors')

        {!! Form::open(['url' => 'admin/data-management/clubs', 'class' => 'form-horizontal']) !!}

        @include('admin.data-management.clubs._form', ['submitText' => 'Add'])

        {!! Form::close() !!}
    </div>

@endsection