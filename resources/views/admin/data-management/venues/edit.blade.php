@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Edit venue</h1>
        <hr/>

        @include('_partial.crud-errors')

        {!! Form::model($venue, [
            'method' => 'PATCH',
            'url' => ['admin/data-management/venues', $venue->id],
            'class' => 'form-horizontal'
        ]) !!}

        @include('admin.data-management.venues._form', ['submitText' => 'Update'])

        {!! Form::close() !!}
    </div>

@endsection