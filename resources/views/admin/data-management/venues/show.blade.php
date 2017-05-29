@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Venue</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th>Id</th>
                    <th>Venue</th>
                    <th>Directions</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{{ $venue->id }}</td>
                    <td>{{ $venue->venue }}</td>
                    <td>{{ $venue->directions }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

@endsection