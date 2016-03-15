@extends('layouts.app')


@section('content')
    <div id="lva-header" class="container-fluid">
        <div class="row">
            <div class="col-md-1 col-sm-2 col-xs-3">
                <img src="{{ asset('/images/lva-logo.png') }}" style="float: left;"/>
            </div>
            <div class="col-md-10 col-sm-8 col-xs-6 lva-header-text">
                <p>London Volleyball Association</p>
            </div>
            <div class="col-md-1 col-sm-2 col-xs-3">
                <img src="{{ asset('/images/lva-logo.png') }}" style="float: right"/>
            </div>
        </div>
    </div>

@endsection
