@extends('admin.data-management.home')

@section('crud')

    <div class="container-fluid">
        <h1>Team</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th>Id</th>
                    <th>Club</th>
                    <th>Team</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{{ $team->id }}</td>
                    <td>{{ $team->club->club }}</td>
                    <td>{{ $team->team }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

@endsection