@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Edit division</h1>
        <hr/>

        @include('_partial.crud-errors')

        {!! Form::model($division, [
            'method' => 'PATCH',
            'route' => ['divisions.update', $division->id],
            'class' => 'form-horizontal'
        ]) !!}

        @include('admin.data-management.divisions._form', ['submitText' => 'Update'])

        {!! Form::close() !!}
    </div>

@endsection