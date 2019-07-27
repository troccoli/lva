@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="mr-auto"><h1>{{ $venue->getName() }}</h1></div>
    </div>
    <table class="table">
        <tbody>
        <tr>
            <td>Name</td>
            <td>{{ $venue->getName() }}</td>
        </tr>
        </tbody>
    </table>
@endsection
