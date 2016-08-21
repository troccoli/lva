@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Available appointment</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th>Id</th>
                    <th>Fixture</th>
                    <th>Role</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{{ $availableAppointment->id }}</td>
                    <td>{{ $availableAppointment->fixture }}</td>
                    <td>{{ $availableAppointment->role }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

@endsection