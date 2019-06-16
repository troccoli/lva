@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @if (session('status'))
                    <div class="alert alert-dismissible alert-success" role="alert">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        {{ session('status') }}
                    </div>
                @endif
                <div class="card text-white bg-info mb-3">
                    <div class="card-header">{{ __('Dashboard') }}</div>
                    <div class="card-body">
                        {{ __('You are logged in!') }}
                    </div>
                </div>
                <div class="card border-primary mb-3">
                    <div class="card-header">Season</div>
                    <div class="card-body">
                        <h4 class="card-title">Manage a season</h4>
                        <p class="card-text">This is where you create and edit all the data for a season.</p>
                        <a href="{{ route('seasons.index') }}" class="btn btn-outline-primary">Manage seasons</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
