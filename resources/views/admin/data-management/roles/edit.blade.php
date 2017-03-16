@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Edit role</h1>
        <hr/>

        @include('_partial.crud-errors')

        {!! Form::model($role, [
            'method' => 'PATCH',
            'url' => ['admin/data-management/roles', $role->id],
            'class' => 'form-horizontal'
        ]) !!}
        {!! Form::hidden('id', $role->id) !!}

        @include('admin.data-management.roles._form', ['submitText' => 'Update'])

        {!! Form::close() !!}
    </div>

@endsection