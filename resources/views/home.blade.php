@extends('layouts.app')


@section('content')
    <div id="lva-header" class="container-fluid">
        <div class="col-md-1 col-sm-2 col-xs-2">
            <img src="{{ asset('/images/lva-logo.png') }}"/>
        </div>
        <div class="col-md-10 col-sm-8 col-xs-10 lva-header-text">
            <p>London Volleyball Association</p>
        </div>
        <div class="col-md-1 col-sm-2 hidden-xs">
            <img src="{{ asset('/images/lva-logo.png') }}"/>
        </div>
    </div>
@endsection
