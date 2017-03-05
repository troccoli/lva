@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Edit fixture</h1>
        <hr/>

        @include('_partial.crud-errors')

        {!! Form::model($fixture, [
            'method' => 'PATCH',
            'url' => ['admin/data-management/fixtures', $fixture->id],
            'class' => 'form-horizontal'
        ]) !!}

        <div class="form-group {{ $errors->has('division_id') ? 'has-error' : ''}}">
            {!! Form::label('division_id', 'Division Id: ', ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                {!! Form::select('division_id', array_column($divisions->toArray(), 'division', 'id'), null, ['class' => 'form-control', 'required' => true]) !!}
                {!! $errors->first('division_id', '<p class="help-block">:message</p>') !!}
            </div>
        </div>

        <div class="form-group {{ $errors->has('match_number') ? 'has-error' : ''}}">
            {!! Form::label('match_number', 'Match Number: ', ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                {!! Form::number('match_number', null, ['class' => 'form-control', 'required' => true, 'autofocus' => true]) !!}
                {!! $errors->first('match_number', '<p class="help-block">:message</p>') !!}
            </div>
        </div>

        <div class="form-group {{ $errors->has('match_date') ? 'has-error' : ''}}">
            {!! Form::label('match_date', 'Match Date: ', ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                {!! Form::date('match_date', $fixture->match_date->format('Y-m-d'), ['class' => 'form-control', 'required' => true]) !!}
                {!! $errors->first('match_date', '<p class="help-block">:message</p>') !!}
            </div>
        </div>

        <div class="form-group {{ $errors->has('warm_up_time') ? 'has-error' : ''}}">
            {!! Form::label('warm_up_time', 'Warm Up Time: ', ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                {!! Form::input('time', 'warm_up_time', $fixture->warm_up_time->format('H:i'), ['class' => 'form-control', 'required' => true]) !!}
                {!! $errors->first('warm_up_time', '<p class="help-block">:message</p>') !!}
            </div>
        </div>

        <div class="form-group {{ $errors->has('start_time') ? 'has-error' : ''}}">
            {!! Form::label('start_time', 'Start Time: ', ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                {!! Form::input('time', 'start_time', $fixture->start_time->format('H:i'), ['class' => 'form-control', 'required' => true]) !!}
                {!! $errors->first('start_time', '<p class="help-block">:message</p>') !!}
            </div>
        </div>

        <div class="form-group {{ $errors->has('home_team_id') ? 'has-error' : ''}}">
            {!! Form::label('home_team_id', 'Home Team Id: ', ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                {!! Form::select('home_team_id', array_column($teams->toArray(), 'team', 'id'), null, ['class' => 'form-control', 'required' => true]) !!}
                {!! $errors->first('home_team_id', '<p class="help-block">:message</p>') !!}
            </div>
        </div>

        <div class="form-group {{ $errors->has('away_team_id') ? 'has-error' : ''}}">
            {!! Form::label('away_team_id', 'Away Team Id: ', ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                {!! Form::select('away_team_id', array_column($teams->toArray(), 'team', 'id'), null, ['class' => 'form-control', 'required' => true]) !!}
                {!! $errors->first('away_team_id', '<p class="help-block">:message</p>') !!}
            </div>
        </div>

        <div class="form-group {{ $errors->has('venue_id') ? 'has-error' : ''}}">
            {!! Form::label('venue_id', 'Venue Id: ', ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                {!! Form::select('venue_id', array_column($venues->toArray(), 'venue', 'id'), null, ['class' => 'form-control', 'required' => true]) !!}
                {!! $errors->first('venue_id', '<p class="help-block">:message</p>') !!}
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