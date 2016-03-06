@extends('layouts.app')

@section('breadcrumbs', Breadcrumbs::render('home'))

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-md-offset-1">
                <div class="panel panel-primary">
                    <div class="panel-heading">Referees</div>

                    <div class="panel-body">
                        <a href="#">Available matches</a>
                    </div>
                </div>
            </div>
            @if (!Auth::guest())
            <div class="col-md-3">
                <div class="panel panel-info">
                    <div class="panel-heading">Administrators</div>

                    <div class="panel-body">
                        <a href="{{ route('admin::dataManagement') }}">Data management</a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection
