@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Edit season</h1>
        <hr/>

        @include('_partial.crud-errors')

        {!! Form::model($season, [
            'method' => 'PATCH',
            'url' => ['admin/data-management/seasons', $season->id],
            'class' => 'form-horizontal'
        ]) !!}
        {!! Form::hidden('id', $season->id) !!}

        @include('admin.data-management.seasons._form', ['submitText' => 'Update'])

        {!! Form::close() !!}
    </div>

@endsection