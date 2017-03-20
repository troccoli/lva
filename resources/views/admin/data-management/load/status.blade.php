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
                <div id="validating-progress" class="hidden">
                    <h4>Validating records</h4>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0"
                             aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                            <span class="sr-only">0% Complete</span>
                        </div>
                    </div>
                </div>
                <div id="inserting-progress" class="hidden">
                    <h4>Inserting records</h4>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0"
                             aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                            <span class="sr-only">0% Complete</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <p id="message"></p>
        <div id="unrecoverable-errors" class="alert alert-danger hidden" role="alert">
            <p>It looks like something went really wrong. I'm afraid I cannot recover from this.</p>
            <p>Please look carefully at the errors, fix them and start over.</p>
            <p id="error-line-number" class="hidden">The errors are on line <span></span></p>
            <ul></ul>
        </div>
    </div>

    <div id="user-action-modal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">Fixture</h3>
                        </div>
                        <div class="panel-body">
                            <span id="fixture-division"></span>-<span id="fixture-match-number"></span>
                            <span id="fixture-home-team"></span>&nbsp;v&nbsp;<span id="fixture-away-team"></span>
                            <br/>
                            on <span id="fixture-date"></span>
                            warm-up: <span id="fixture-warm-up-time"></span>
                            start: <span id="fixture-start-time"></span>
                            <br/>
                            at <span id="fixture-venue"></span>
                        </div>
                    </div>
                    <p>
                        The following data has not been found in the database. If it's new then add it. If not, then
                        please check the available options and map it to one of the existing.
                    </p>
                    <h4>What would you like to do?</h4>
                    <div id="unknowns" class="container-fluid"></div>
                    <div id="unknown-data-template" class="unknown row hidden">
                        <button type="button"
                                class="col-xs-1 add-button btn btn-primary"
                                autocomplete="off"
                                data-apiurl="">
                            Add
                        </button>
                        <p class="col-xs-5"></p>
                        <div class="col-xs-5">
                            <select class="form-control"></select>
                        </div>
                        <button type="button"
                                class="col-xs-1 map-button btn btn-primary"
                                autocomplete="off"
                                data-apiurl="">
                            Map
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="resume-button" type="button" class="btn btn-success disabled" data-dismiss="modal">
                        Continue
                    </button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div id="user-confirmation-modal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">User confirmation required</h4>
                </div>
                <div class="modal-body">
                    <p>
                        The system has now finished validating all the records and the data is ready to be inserted
                        into the system. This action can <strong>not</strong> be interrupted.
                    </p>
                    <p>
                        This is your last chance to abandon the process and start over, if for example you have
                        made any mistakes.
                    </p>
                </div>
                <div class="modal-footer">
                    <button id="abandon-button" type="button" class="btn btn-default" data-dismiss="modal">
                        Abandon
                    </button>
                    <button id="continue-button" type="button" class="btn btn-danger" data-dismiss="modal">
                        Continue
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script src="https://cdn.jsdelivr.net/jquery.loadingoverlay/1.4.1/loadingoverlay.min.js"></script>
    <script src="{{ url(elixir('js/load-fixtures-status-update.js')) }}"></script>
@endsection