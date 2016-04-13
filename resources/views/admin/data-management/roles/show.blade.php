@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <h1>Role</h1>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>Id</th> <th>Role</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $role->id }}</td> <td> {{ $role->role }} </td>
                </tr>
            </tbody>    
        </table>
    </div>
</div>

@endsection