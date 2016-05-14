@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <p>From here you can manage all the data. Click on the button of teh table you want to use and from there
                you will be able to add, modify and delete records.</p>
            <ul class="nav nav-pills">
                <li role="presentation">
                    <a id="seasons-table" href="{{ route('admin.data-management.seasons.index') }}"><i
                                class="fa fa-table"></i> Seasons</a>
                </li>
                <li role="presentation">
                    <a id="clubs-table" href="{{ route('admin.data-management.clubs.index') }}"><i
                                class="fa fa-table"></i> Clubs</a>
                </li>
                <li role="presentation">
                    <a id="venues-table" href="{{ route('admin.data-management.venues.index') }}"><i
                                class="fa fa-table"></i> Venues</a>
                </li>
                <li role="presentation">
                    <a id="roles-table" href="{{ route('admin.data-management.roles.index') }}"><i
                                class="fa fa-table"></i> Roles</a>
                </li>
                <li role="presentation">
                    <a id="divisions-table" href="{{ route('admin.data-management.divisions.index') }}"><i
                                class="fa fa-table"></i> Divisions</a>
                </li>
                <li role="presentation">
                    <a id="teams-table" href="{{ route('admin.data-management.teams.index') }}"><i
                                class="fa fa-table"></i> Teams</a>
                </li>
                <li role="presentation">
                    <a id="fixtures-table" href="{{ route('admin.data-management.fixtures.index') }}"><i
                                class="fa fa-table"></i> Fixtures</a>
                </li>
                <li role="presentation">
                    <a id="available-appointments-table"
                       href="{{ route('admin.data-management.available-appointments.index') }}"><i
                                class="fa fa-table"></i> Available appointments</a>
                </li>
            </ul>

            @yield('crud')
        </div>
    </div>
@endsection