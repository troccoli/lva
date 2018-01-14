@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Edit role</h1>
        <hr/>

        @include('_partial.crud-errors')

        {!! Form::model($role, [
            'method' => 'PATCH',
            'route' => ['roles.update', $role->id],
            'class' => 'form-horizontal'
        ]) !!}

        @include('admin.data-management.roles._form', ['submitText' => 'Update'])

        {!! Form::close() !!}
    </div>

@endsection