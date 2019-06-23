@extends('layouts.app')

@section('content')
    @component('components.forms.auth')
        @slot('title')
            {{ __('Reset password') }}
        @endslot
        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            @emailField([
            'label' => __('Email address'),
            'fieldName' => 'email',
            'defaultValue' => $email,
            'required' => true,
            ])

            @passwordField([
            'label' => __('Password'),
            'fieldName' => 'password',
            'required' => true,
            'withConfirmation' => true,
            ])

            @submitButton(['label' => __('Reset password')])

            <div class="text-center">
                <a class="small" href="{{ route('password.request') }}">
                    {{ __('Request a new reset link') }}
                </a>
            </div>
        </form>
    @endcomponent
@endsection
