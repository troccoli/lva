@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <h1>Division</h1>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>Id</th> <th>Season Id</th><th>Division</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $division->id }}</td> <td> {{ $division->season_id }} </td><td> {{ $division->division }} </td>
                </tr>
            </tbody>    
        </table>
    </div>
</div>

@endsection