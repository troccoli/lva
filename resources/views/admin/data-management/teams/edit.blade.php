@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Edit team</h1>
        <hr/>

        @include('_partial.crud-errors')

        {!! Form::model($team, [
            'method' => 'PATCH',
            'url' => ['admin/data-management/teams', $team->id],
            'class' => 'form-horizontal'
        ]) !!}

        <div class="form-group {{ $errors->has('club_id') ? 'has-error' : ''}}">
            {!! Form::label('club_id', 'Club Id: ', ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                {!! Form::select('club_id', array_column($clubs->toArray(), 'club', 'id'), $team->club->id, ['class' => 'form-control']) !!}
                {!! $errors->first('club_id', '<p class="help-block">:message</p>') !!}
            </div>
        </div>

        <div class="form-group {{ $errors->has('team') ? 'has-error' : ''}}">
            {!! Form::label('team', 'Team: ', ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                {!! Form::text('team', null, ['class' => 'form-control', 'required' => true]) !!}
                {!! $errors->first('team', '<p class="help-block">:message</p>') !!}
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