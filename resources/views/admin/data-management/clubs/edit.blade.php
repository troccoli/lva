@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <h1>Edit Club</h1>
    <hr/>

    {!! Form::model($club, [
        'method' => 'PATCH',
        'url' => ['admin/data-management/clubs', $club->id],
        'class' => 'form-horizontal'
    ]) !!}

            <div class="form-group {{ $errors->has('club') ? 'has-error' : ''}}">
                {!! Form::label('club', 'Club: ', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-6">
                    {!! Form::text('club', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('club', '<p class="help-block">:message</p>') !!}
                </div>
            </div>


    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-3">
            {!! Form::submit('Update', ['class' => 'btn btn-primary form-control']) !!}
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