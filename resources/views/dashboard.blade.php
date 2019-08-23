@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @if (session('status'))
                    <div class="alert alert-dismissible alert-success" role="alert">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        {{ session('status') }}
                    </div>
                @endif
                <h1>{{ __('Welcome to your dashboard') }}</h1>
                <p>{{ __('From here you can access all the sections of the site you need as a League Administrator.') }}</p>
                <div class="row">
                    <div class="col">
                        <div class="card border-primary mb-3" dusk="raw-data-panel">
                            <div class="card-header bg-primary text-uppercase text-white">{{ __('Raw Data') }}</div>
                            <div class="card-body">
                                <p>
                                    From here you can access the raw data for your competitions, teams, venues, etc.
                                    If you need to start a new season, or move teams between divisions, please use the
                                    <strong>Manage Data</strong> panel below.
                                </p>
                                <ul class="nav nav-tabs">
                                    <li class="nav-item" dusk="structure-tab-header">
                                        <a class="nav-link active" data-toggle="tab" href="#structure">{{ __('Structure') }}</a>
                                    </li>
                                    <li class="nav-item" dusk="participants-tab-header">
                                        <a class="nav-link" data-toggle="tab" href="#participants">{{ __('Participants') }}</a>
                                    </li>
                                    <li class="nav-item" dusk="venues-tab-header">
                                        <a class="nav-link" data-toggle="tab" href="#venues">{{ __('Venues') }}</a>
                                    </li>
                                </ul>
                                <div id="myTabContent" class="tab-content">
                                    <div class="tab-pane fade active show" id="structure" dusk="structure-tab-content">
                                        <h5 class="pt-3" dusk="header">{{ __('Seasons, competitions and divisions') }}</h5>
                                        <p>{{ __('This is where you create and edit all the data for the seasons, competitions and divisions.') }}</p>
                                        <a href="{{ route('seasons.index') }}" class="btn btn-outline-primary">{{ __('Manage seasons') }}</a>
                                    </div>
                                    <div class="tab-pane" id="participants" dusk="participants-tab-content">
                                        <h5 class="pt-3" dusk="header">{{ __('Clubs and teams') }}</h5>
                                        <p>{{ __('This is where you create and edit all the data for clubs and their teams.') }}</p>
                                        <a href="{{ route('clubs.index') }}" class="btn btn-outline-primary">{{ __('Manage clubs') }}</a>
                                    </div>
                                    <div class="tab-pane" id="venues" dusk="venues-tab-content">
                                        <h5 class="pt-3" dusk="header">{{ __('Venues') }}</h5>
                                        <p>{{ __('This is where you manage all the venues for all clubs and teams, regardless of season or competition.') }}</p>
                                        <a href="{{ route('venues.index') }}" class="btn btn-outline-primary">{{ __('Manage venues') }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
