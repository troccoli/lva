@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <a id="seasons-table" href="{{ route('admin.data-management.seasons.index') }}" class="btn btn-primary"><i class="fa fa-table"></i> Seasons</a>
                <a id="clubs-table" href="{{ route('admin.data-management.clubs.index') }}" class="btn btn-primary"><i class="fa fa-table"></i> Clubs</a>
                <a id="venues-table" href="{{ route('admin.data-management.venues.index') }}" class="btn btn-primary"><i class="fa fa-table"></i> Venues</a>
                <a id="roles-table" href="{{ route('admin.data-management.roles.index') }}" class="btn btn-primary"><i class="fa fa-table"></i> Roles</a>
                <a id="divisions-table" href="{{ route('admin.data-management.divisions.index') }}" class="btn btn-primary"><i class="fa fa-table"></i> Divisions</a>
                <a id="teams-table" href="{{ route('admin.data-management.teams.index') }}" class="btn btn-primary"><i class="fa fa-table"></i> Teams</a>
                <a id="fixtures-table" href="{{ route('admin.data-management.fixtures.index') }}" class="btn btn-primary"><i class="fa fa-table"></i> Fixtures</a>
                <a id="available-appointments-table"
                   href="{{ route('admin.data-management.available-appointments.index') }}" class="btn btn-primary"><i
                            class="fa fa-table"></i> Available appointments</a>
            </div>
        </div>
    </div>
@endsection