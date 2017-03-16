@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Edit division</h1>
        <hr/>

        @include('_partial.crud-errors')

        {!! Form::model($division, [
            'method' => 'PATCH',
            'url' => ['admin/data-management/divisions', $division->id],
            'class' => 'form-horizontal'
        ]) !!}
        {!! Form::hidden('id', $division->id) !!}

        @include('admin.data-management.divisions._form', ['submitText' => 'Update'])

        {!! Form::close() !!}
    </div>

@endsection