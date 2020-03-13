@extends('layouts.app')

@section('content')
    <v-app id="fixtures"></v-app>
@endsection

@push('stylesheets')
    <link href='https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900|Material+Icons' rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@latest/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="{{ mix('css/vuetify.min.css') }}" rel="stylesheet">
@endpush
