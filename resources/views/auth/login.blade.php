@extends('layouts.app')

@section('content')
    @component('components.forms.auth')
        @slot('title')
            {{ __('Login') }}
        @endslot

        <form method="post" action="{{ route('login') }}">
            @csrf

            @emailField([
                'label' => __('Email address'),
                'fieldName' => 'email',
                'required' => true,
            ])

            @passwordField([
            'label' => __('Password'),
            'fieldName' => 'password',
            'required' => true
            ])

            @checkboxField([
            'label' => __('Remember me'),
            'fieldName' => 'remember'
            ])

            @submitButton(['label' => __('Login')])
            <div class="text-center">
                <a class="small" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            </div>
        </form>
    @endcomponent
@endsection
