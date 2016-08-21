@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <h1>Season</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th>Id</th>
                    <th>Season</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{{ $season->id }}</td>
                    <td>{{ $season->season }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

@endsection