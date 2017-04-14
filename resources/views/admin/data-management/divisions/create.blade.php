@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Add new division</h1>
        <hr/>

        @include('_partial.crud-errors')

        {!! Form::open(['url' => 'admin/data-management/divisions', 'class' => 'form-horizontal']) !!}

        @include('admin.data-management.divisions._form', ['submitText' => 'Add'])

        {!! Form::close() !!}
    </div>

@endsection