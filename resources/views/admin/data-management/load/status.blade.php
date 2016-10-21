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
                    <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="60"
                         aria-valuemin="0" aria-valuemax="100" style="width: 60%;">
                        <span class="sr-only">60% Complete</span>
                    </div>
                </div>
            </div>
        </div>
        <p id="message"></p>
    </div>

    @include('_partial.load-fixture-modal');
@endsection

@section('javascript')
    <script type="text/javascript"
            src="https://cdn.jsdelivr.net/jquery.loadingoverlay/1.4.1/loadingoverlay.min.js"></script>
    <script src="{{ url(elixir('js/load-fixtures-status-update.js')) }}"></script>
@endsection