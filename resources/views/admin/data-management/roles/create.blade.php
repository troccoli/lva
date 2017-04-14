@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Add new role</h1>
        <hr/>

        @include('_partial.crud-errors')

        {!! Form::open(['url' => 'admin/data-management/roles', 'class' => 'form-horizontal']) !!}

        @include('admin.data-management.roles._form', ['submitText' => 'Add'])

        {!! Form::close() !!}
    </div>

@endsection