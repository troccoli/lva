@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Edit fixture</h1>
        <hr/>

        @include('_partial.crud-errors')

        {!! Form::model($fixture, [
            'method' => 'PATCH',
            'url' => ['admin/data-management/fixtures', $fixture->id],
            'class' => 'form-horizontal'
        ]) !!}

        @include('admin.data-management.fixtures._form', ['submitText' => 'Update'])

        {!! Form::close() !!}
    </div>

@endsection