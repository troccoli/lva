@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <h1>Club</h1>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>ID.</th><th>Club</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $club->id }}</td> <td> {{ $club->club }} </td>
                </tr>
            </tbody>    
        </table>
    </div>
</div>

@endsection