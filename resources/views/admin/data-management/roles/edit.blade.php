@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Edit role</h1>
        <hr/>

        @include('_partial.crud-errors')

        {!! Form::model($role, [
            'method' => 'PATCH',
            'url' => ['admin/data-management/roles', $role->id],
            'class' => 'form-horizontal'
        ]) !!}

        <div class="form-group {{ $errors->has('role') ? 'has-error' : ''}}">
            {!! Form::label('role', 'Role: ', ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                {!! Form::text('role', null, ['class' => 'form-control', 'required' => true]) !!}
                {!! $errors->first('role', '<p class="help-block">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-3">
                {!! Form::submit('Update', ['class' => 'btn btn-primary form-control']) !!}
            </div>
        </div>

        {!! Form::close() !!}
    </div>

@endsection