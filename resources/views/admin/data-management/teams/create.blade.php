@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <h1>Create New Team</h1>
    <hr/>

    {!! Form::open(['url' => 'admin/data-management/teams', 'class' => 'form-horizontal']) !!}

                <div class="form-group {{ $errors->has('club_id') ? 'has-error' : ''}}">
                {!! Form::label('club_id', 'Club: ', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-6">
                    {!! Form::select('club_id', array_column($clubs->toArray(), 'club', 'id'), null, ['class' => 'form-control', 'required' => 'required']) !!}
                    {!! $errors->first('club_id', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="form-group {{ $errors->has('team') ? 'has-error' : ''}}">
                {!! Form::label('team', 'Team: ', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-6">
                    {!! Form::text('team', null, ['class' => 'form-control', 'required' => 'required']) !!}
                    {!! $errors->first('team', '<p class="help-block">:message</p>') !!}
                </div>
            </div>


    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-3">
            {!! Form::submit('Create', ['class' => 'btn btn-primary form-control']) !!}
        </div>
    </div>
    {!! Form::close() !!}

    @if ($errors->any())
        <ul class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif
</div>

@endsection