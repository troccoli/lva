@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Edit season</h1>
        <hr/>

        @include('_partial.crud-errors')

        {!! Form::model($season, [
            'method' => 'PATCH',
            'route' => ['seasons.update', $season->id],
            'class' => 'form-horizontal'
        ]) !!}

        @include('admin.data-management.seasons._form', ['submitText' => 'Update'])

        {!! Form::close() !!}
    </div>

@endsection