@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Edit fixture</h1>
        <hr/>

        @include('_partial.crud-errors')

        {!! Form::model($fixture, [
            'method' => 'PATCH',
            'route' => ['fixtures.update', $fixture->id],
            'class' => 'form-horizontal'
        ]) !!}
        {!! Form::hidden('id', $fixture->id) !!}

        @include('admin.data-management.fixtures._form', ['submitText' => 'Update'])

        {!! Form::close() !!}
    </div>

@endsection