@extends('layouts.app')

@push('stylesheets')
    <link href="{{ mix('css/login.css') }}" rel="stylesheet">
@endpush
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
                <div class="card card-signin my-5">
                    <div class="card-body">
                        <h5 class="card-title text-center">{{ __('Login') }}</h5>
                        <form class="form-signin" method="post">
                            @csrf
                            <div class="form-label-group">
                                <input type="email" id="inputEmail"
                                       class="form-control @error('email') is-invalid @enderror" name="email"
                                       placeholder="{{ __('Email address') }}" required autocomplete="email" autofocus
                                       value="{{ old('email') ?? '' }}">
                                <label for="inputEmail">{{ __('Email address') }}</label>
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="form-label-group">
                                <input type="password" id="inputPassword"
                                       class="form-control @error('password') is-invalid @enderror" name="password"
                                       placeholder="{{ __('Password') }}" required autocomplete="current-password">
                                <label for="inputPassword">{{ __('Password') }}</label>
                            </div>

                            <div class="custom-control custom-checkbox mb-3">
                                <input type="checkbox" class="custom-control-input" id="remember"
                                       name="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="remember">{{ __('Remember me') }}</label>
                            </div>
                            <button class="btn btn-lg btn-primary btn-block text-uppercase"
                                    type="submit">{{ __('Login') }}</button>
                            <div class="text-center">
                                <a class="small" href="{{ route('password.request') }}">
                                    {{ __('Forgot your password?') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
