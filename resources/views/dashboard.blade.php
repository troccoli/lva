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
                        <div class="card border-primary mb-3" dusk="seasons-teams-panel">
                            <div class="card-header">{{ __('Seasons, competitions and divisions') }}</div>
                            <div class="card-body">
                                <h4 class="card-title">{{ __('Manage seasons') }}</h4>
                                <p class="card-text">{{ __('This is where you create and edit all the data for the seasons, competitions and divisions.') }}</p>
                                <a href="{{ route('seasons.index') }}" class="btn btn-outline-primary">{{ __('Manage seasons') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-primary mb-3" dusk="clubs-teams-panel">
                            <div class="card-header">{{ __('Clubs and teams') }}</div>
                            <div class="card-body">
                                <h4 class="card-title">{{ __('Manage clubs and their teams') }}</h4>
                                <p class="card-text">{{ __('This is where you create and edit all the data for clubs and their teams.') }}</p>
                                <a href="{{ route('clubs.index') }}" class="btn btn-outline-primary">{{ __('Manage clubs') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-primary mb-3" dusk="venues-panel">
                            <div class="card-header">{{ __('Venues') }}</div>
                            <div class="card-body">
                                <h4 class="card-title">{{ __('Manage all the venues') }}</h4>
                                <p class="card-text">{{ __('This is where you manage all the venues for all clubs and teams, regardless of season or competition.') }}</p>
                                <a href="{{ route('venues.index') }}" class="btn btn-outline-primary">{{ __('Manage venues') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
