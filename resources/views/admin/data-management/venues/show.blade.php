@extends('admin.data-management.home')

@section('crud')

    <div class="container-fluid">
        <h1>Venue</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th>Id</th>
                    <th>Venue</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{{ $venue->id }}</td>
                    <td>{{ $venue->venue }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

@endsection