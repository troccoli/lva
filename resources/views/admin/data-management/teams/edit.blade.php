@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Edit team</h1>
        <hr/>

        @include('_partial.crud-errors')

        {!! Form::model($team, [
            'method' => 'PATCH',
            'route' => ['teams.update', $team->id],
            'class' => 'form-horizontal'
        ]) !!}

        @include('admin.data-management.teams._form', ['submitText' => 'Update'])

        {!! Form::close() !!}
    </div>

@endsection