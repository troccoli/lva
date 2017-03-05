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

        <div class="form-group {{ $errors->has('season') ? 'has-error' : ''}}">
            {!! Form::label('season', 'Season: ', ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                {!! Form::text('season', null, ['class' => 'form-control', 'required' => true, 'autofocus' => true]) !!}
                {!! $errors->first('season', '<p class="help-block">:message</p>') !!}
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