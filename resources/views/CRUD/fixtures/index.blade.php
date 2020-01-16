@extends('layouts.app')

@section('content')
<div id="fixtures"></div>
@endsection

@push('stylesheets')
    <link href='https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900|Material+Icons' rel="stylesheet">
    <link href="{{ mix('css/vuetify.min.css') }}" rel="stylesheet">
@endpush
