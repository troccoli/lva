@extends('layouts.app')

@section('stylesheets')
    <link href="{{ mix('css/data-management.css') }}" rel="stylesheet"/>
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-3">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            Direct data management
                        </h3>
                    </div>
                    <div class="panel-body">
                        <p>
                            This is where you manage raw data: add, delete or update records.
                        </p>
                        <ul class="nav nav-pills nav-stacked">
                            <li role="presentation">
                                <a id="seasons-table" href="{{ route('seasons.index') }}">
                                    <i class="fa fa-table"></i> Seasons
                                </a>
                            </li>
                            <li role="presentation">
                                <a id="clubs-table" href="{{ route('clubs.index') }}">
                                    <i class="fa fa-table"></i> Clubs
                                </a>
                            </li>
                            <li role="presentation">
                                <a id="venues-table" href="{{ route('venues.index') }}">
                                    <i class="fa fa-table"></i> Venues
                                </a>
                            </li>
                            <li role="presentation">
                                <a id="roles-table" href="{{ route('roles.index') }}">
                                    <i class="fa fa-table"></i> Roles
                                </a>
                            </li>
                            <li role="presentation">
                                <a id="divisions-table" href="{{ route('divisions.index') }}">
                                    <i class="fa fa-table"></i> Divisions
                                </a>
                            </li>
                            <li role="presentation">
                                <a id="teams-table" href="{{ route('teams.index') }}">
                                    <i class="fa fa-table"></i> Teams
                                </a>
                            </li>
                            <li role="presentation">
                                <a id="fixtures-table" href="{{ route('fixtures.index') }}">
                                    <i class="fa fa-table"></i> Fixtures
                                </a>
                            </li>
                            <li role="presentation">
                                <a id="available-appointments-table"
                                   href="{{ route('available-appointments.index') }}">
                                    <i class="fa fa-table"></i> Available appointments
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-xs-3">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <div class="panel-title">
                            Start of season
                        </div>
                    </div>
                    <div class="panel-body">
                        <p>
                            Tasks to be typically carried out at the beginning of a new season
                        </p>
                        <ul class="nav nav-pills nav-stacked">
                            <li role="presentation">
                                <a href="{{ route('uploadFixtures') }}">
                                    <i class="fa fa-upload" aria-hidden="true"></i> Load fixtures
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection