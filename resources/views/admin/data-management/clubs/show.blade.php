@extends('admin.data-management.home')

@section('crud')

    <div class="container-fluid">
        <h1>Club</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th>Id</th>
                    <th>Club</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{{ $club->id }}</td>
                    <td>{{ $club->club }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

@endsection