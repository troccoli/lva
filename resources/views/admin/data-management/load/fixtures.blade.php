@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Upload fixtures</h1>
        <hr/>
        {!! Form::open(['url' => route('uploadFixtures'), 'class' => 'form-horizontal', 'files' => true]) !!}

        <div class="form-group {{ $errors->has('season_id') ? 'has-error' : ''}}">
            {!! Form::label('season_id', 'Season: ', ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                {!! Form::select('season_id', array_column($seasons->toArray(), 'season', 'id'), null, ['class' => 'form-control']) !!}
                {!! $errors->first('season_id', '<p class="help-block">:message</p>') !!}
            </div>
        </div>

        <div class="form-group {{ $errors->has('upload_file') ? 'has-error' : ''}}">
            {!! Form::label('upload_file', 'File: ', ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-6">
                <div class="input-group">
                    <label class="input-group-btn">
                    <span class="btn btn-primary">
                        Browse&hellip; <input name="upload_file" type="file" style="display: none;">
                    </span>
                    </label>
                    <input type="text" class="form-control" readonly>
                </div>
                {!! $errors->first('upload_file', '<p class="help-block">:message</p>') !!}
            </div>
        </div>


        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-3">
                {!! Form::submit('Start', ['class' => 'btn btn-primary form-control']) !!}
            </div>
        </div>

        {!! Form::close() !!}
    </div>

@endsection

@section('javascript')
    <script src="{{ url(elixir('js/file-browse.js')) }}"></script>
@endsection