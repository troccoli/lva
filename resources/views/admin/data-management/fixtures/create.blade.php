@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Add new fixture</h1>
        <hr/>

        @include('_partial.crud-errors')

        {!! Form::open(['url' => 'admin/data-management/fixtures', 'class' => 'form-horizontal']) !!}

        @include('admin.data-management.fixtures._form', ['submitText' => 'Add'])

        {!! Form::close() !!}
    </div>

@endsection