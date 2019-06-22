@extends('layouts.app')

@section('content')
    @component('components.forms.auth')
        @slot('title')
            {{ __('Forgotten password') }}
        @endslot

        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="form-label-group">
                <input type="email" id="inputEmail" class="form-control @error('email') is-invalid @enderror"
                       name="email" value="{{ old('email') }}" placeholder="{{ __('Email address') }}" required
                       autofocus autocomplete="email">
                <label for="inputEmail">{{ __('Email address') }}</label>

                @error('email')
                <span class="invalid-feedback" role="alert" dusk="email-error">{{ $message }}</span>
                @enderror
            </div>
            <button class="btn btn-lg btn-primary btn-block text-uppercase"
                    type="submit">{{ __('Send password reset link') }}</button>
        </form>
    @endcomponent
@endsection
