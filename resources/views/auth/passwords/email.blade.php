@extends('layouts.app')

@section('content')
    <div class="container login-page">
        <div class="row">
            <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
                <div class="card card-signin my-5">
                    <div class="card-body">
                        <h5 class="card-title text-center">{{ __('Reset password') }}</h5>
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <form method="POST" action="{{ route('password.email') }}" class="form-signin">
                            @csrf
                            <div class="form-label-group">
                                <input type="email" id="inputEmail" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="{{ __('Email address') }}" required autofocus autocomplete="email">
                                <label for="inputEmail">{{ __('Email address') }}</label>

                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <button class="btn btn-lg btn-primary btn-block text-uppercase" type="submit">{{ __('Send password reset link') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
