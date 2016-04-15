@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Fixture</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th>Season</th>
                    <th>Division</th>
                    <th>Match #</th>
                    <th>Date</th>
                    <th>Warm-up time</th>
                    <th>Start time</th>
                    <th>Home</th>
                    <th>Away</th>
                    <th>Venue</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{{ $fixture->division->season->season }}</td>
                    <td>{{ $fixture->division->division }}</td>
                    <td>{{ $fixture->match_number }}</td>
                    <td>{{ $fixture->match_date->format('j M Y') }}</td>
                    <td>{{ $fixture->warm_up_time }}</td>
                    <td>{{ $fixture->start_time }}</td>
                    <td>{{ $fixture->home_team->team }}</td>
                    <td>{{ $fixture->away_team->team }}</td>
                    <td>{{ $fixture->venue->venue }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

@endsection