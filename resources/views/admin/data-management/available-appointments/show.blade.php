@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Availableappointment</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th>Id</th>
                    <th>Fixture Id</th>
                    <th>Role Id</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{{ $availableappointment->id }}</td>
                    <td> {{ $availableappointment->fixture_id }} </td>
                    <td> {{ $availableappointment->role_id }} </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

@endsection