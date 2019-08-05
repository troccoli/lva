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
            <div class="jumbotron">
                <h1 class="display-3">{{ __('Hello there!') }}</h1>
                <p class="lead">{{ __("Welcome to the London Volleyball Association's website.") }}</p>
                @guest
                    <p>@lang('If you are a League Administrator please <a href=":url">login</a>.</p>', ['url' => route('login')])</p>
                @endguest
            </div>
        </div>
    </div>
</div>
@endsection
