@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <h1>Create New Season</h1>
    <hr/>

    {!! Form::open(['url' => 'admin/data-management/seasons', 'class' => 'form-horizontal']) !!}

                <div class="form-group {{ $errors->has('season') ? 'has-error' : ''}}">
                {!! Form::label('season', 'Season: ', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-6">
                    {!! Form::text('season', null, ['class' => 'form-control', 'required' => 'required']) !!}
                    {!! $errors->first('season', '<p class="help-block">:message</p>') !!}
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