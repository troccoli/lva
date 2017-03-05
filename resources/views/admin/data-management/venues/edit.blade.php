@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Edit venue</h1>
        <hr/>

        @include('_partial.crud-errors')

        {!! Form::model($venue, [
            'method' => 'PATCH',
            'url' => ['admin/data-management/venues', $venue->id],
            'class' => 'form-horizontal'
        ]) !!}

        <div class="form-group {{ $errors->has('venue') ? 'has-error' : ''}}">
            {!! Form::label('venue', 'Venue: ', ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                {!! Form::text('venue', null, ['class' => 'form-control', 'required' => true, 'autofocus' => true]) !!}
                {!! $errors->first('venue', '<p class="help-block">:message</p>') !!}
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