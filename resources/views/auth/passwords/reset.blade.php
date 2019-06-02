@extends('layouts.app')

@section('content')
    @component('components.auth')
        @slot('title')
            {{ __('Reset password') }}
        @endslot
        <form method="POST" action="{{ route('password.update') }}" class="form-signin">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-label-group">
                <input type="email" id="inputEmail" class="form-control @error('email') is-invalid @enderror"
                       name="email" value="{{ $email ?? old('email') }}" placeholder="{{ __('Email address') }}"
                       required
                       autofocus autocomplete="email">
                <label for="inputEmail">{{ __('Email address') }}</label>

                @error('email')
                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-label-group">
                <input id="inputPassword" type="password" class="form-control @error('password') is-invalid @enderror"
                       name="password" placeholder="{{ __('Password') }}" required autocomplete="new-password">
                <label for="inputPassword">{{ __('Password') }}</label>

                @error('password')
                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-label-group">
                <input id="inputConfirmPassword" type="password" class="form-control" name="password_confirmation"
                       placeholder="{{ __('Confirm password') }}" required autocomplete="new-password">
                <label for="inputConfirmPassword">{{ __('Confirm password') }}</label>
            </div>

            <button class="btn btn-lg btn-primary btn-block text-uppercase"
                    type="submit">{{ __('Reset password') }}</button>
            <div class="text-center">
                <a class="small" href="{{ route('password.request') }}">
                    {{ __('Request a new reset link') }}
                </a>
            </div>
        </form>
    @endcomponent
@endsection
