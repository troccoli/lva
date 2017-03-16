@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Edit club</h1>
        <hr/>

        @include('_partial.crud-errors')

        {!! Form::model($club, [
            'method' => 'PATCH',
            'url' => ['admin/data-management/clubs', $club->id],
            'class' => 'form-horizontal'
        ]) !!}

        @include('admin.data-management.clubs._form', ['submitText' => 'Update'])

        {!! Form::close() !!}
    </div>

@endsection