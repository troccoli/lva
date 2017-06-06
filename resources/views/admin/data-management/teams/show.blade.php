@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Team</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th>Id</th>
                    <th>Club</th>
                    <th>Team</th>
                    <th>Trigram</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{{ $team->id }}</td>
                    <td>{{ $team->club->club }}</td>
                    <td>{{ $team->team }}</td>
                    <td>{{ $team->trigram }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

@endsection