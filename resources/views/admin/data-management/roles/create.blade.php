@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Add new role</h1>
        <hr/>

        @include('_partial.crud-errors')

        {!! Form::open(['url' => 'admin/data-management/roles', 'class' => 'form-horizontal']) !!}

        <div class="form-group {{ $errors->has('role') ? 'has-error' : ''}}">
            {!! Form::label('role', 'Role: ', ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                {!! Form::text('role', null, ['class' => 'form-control', 'required' => true]) !!}
                {!! $errors->first('role', '<p class="help-block">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-3">
                {!! Form::submit('Add', ['class' => 'btn btn-primary form-control']) !!}
            </div>
        </div>

        {!! Form::close() !!}
    </div>

@endsection