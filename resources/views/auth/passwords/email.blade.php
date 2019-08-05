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

            @emailField([
                 'label' => __('Email address'),
                 'fieldName' => 'email',
                 'required' => true,
             ])

            @submitButton(['label' => __('Send password reset link')])
        </form>
    @endcomponent
@endsection
