@extends('admin.data-management.home')

@section('crud')

    <div class="container-fluid">
        <h1>Add new division</h1>
        <hr/>

        @include('_partial.crud-errors')

        {!! Form::open(['url' => 'admin/data-management/divisions', 'class' => 'form-horizontal']) !!}

        <div class="form-group {{ $errors->has('season_id') ? 'has-error' : ''}}">
            {!! Form::label('season_id', 'Season: ', ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                {!! Form::select('season_id', array_column($seasons->toArray(), 'season', 'id'), null, ['class' => 'form-control']) !!}
                {!! $errors->first('season_id', '<p class="help-block">:message</p>') !!}
            </div>
        </div>

        <div class="form-group {{ $errors->has('division') ? 'has-error' : ''}}">
            {!! Form::label('division', 'Division: ', ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                {!! Form::text('division', null, ['class' => 'form-control', 'required' => 'required']) !!}
                {!! $errors->first('division', '<p class="help-block">:message</p>') !!}
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