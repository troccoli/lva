@extends('layouts.app')

@section('stylesheets')
    <link href="{{ url(elixir('css/load-fixtures.css')) }}" rel="stylesheet"/>
@endsection

@section('content')
    {!! Form::hidden('api_token', Auth::user()->api_token, ['id' => 'api_token']) !!}
    {!! Form::hidden('job_id', $job->id, ['id' => 'job_id']) !!}
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h3>Processing <strong>{{$job->file}}</strong></h3>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0"
                         aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                        <span class="sr-only">0% Complete</span>
                    </div>
                </div>
            </div>
        </div>
        <p id="message"></p>
        <div id="unrecoverable-errors" class="alert alert-danger hidden" role="alert">
            <p>It looks like something went really wrong. I'm afraid I cannot recover from this.</p>
            <p>Please look carefully at the errors, fix them and start over.</p>
            <p>The errors are on line <span id="error-line-number"></span></p>
            <ul></ul>
        </div>
    </div>

    @include('_partial.load-fixture-modal')
@endsection

@section('javascript')
    <script type="text/javascript"
            src="https://cdn.jsdelivr.net/jquery.loadingoverlay/1.4.1/loadingoverlay.min.js"></script>
    <script src="{{ url(elixir('js/load-fixtures-status-update.js')) }}"></script>
@endsection