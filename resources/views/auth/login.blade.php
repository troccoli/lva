@extends('layouts.app')

@section('content')
    @component('components.forms.auth')
        @slot('title')
            {{ __('Login') }}
        @endslot

        <form method="post" action="{{ route('login') }}">
            @csrf
            <div class="form-label-group">
                <input type="email" id="inputEmail"
                       class="form-control @error('email') is-invalid @enderror" name="email"
                       placeholder="{{ __('Email address') }}" required autocomplete="email" autofocus
                       value="{{ old('email') ?? '' }}">
                <label for="inputEmail">{{ __('Email address') }}</label>
                @error('email')
                <span class="invalid-feedback" role="alert" dusk="email-error">{{ $message }}</span>
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
    @endcomponent
@endsection
