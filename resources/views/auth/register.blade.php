@extends('layouts.app')

@section('content')
    @component('components.forms.auth')
        @slot('title')
            {{ __('Register') }}
        @endslot
        <form method="POST" action="{{ route('register') }}">
            @csrf

            @textField([
            'label' => __('Name'),
            'fieldName' => 'name',
            'required' => true
            ])

            @emailField([
                'label' => __('Email address'),
                'fieldName' => 'email',
                'required' => true,
            ])

            @passwordField([
            'label' => __('Password'),
            'fieldName' => 'password',
            'required' => true,
            'withConfirmation' => true,
            ])

            @submitButton(['label' => __('Register')])
        </form>
    @endcomponent
@endsection
