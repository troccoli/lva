@extends('layouts.app')

@section('content')
    <div class="container login-page">
        <div class="row">
            <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
                <div class="card card-signin my-5">
                    <div class="card-body">
                        <h5 class="card-title text-center">{{ __('Register') }}</h5>
                        <form class="form-signin">
                            <div class="form-label-group">
                                <input type="text" id="inputName" class="form-control  @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="{{ __('Name') }}" required autofocus autocomplete="name">
                                <label for="inputName">Name</label>

                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-label-group">
                                <input type="email" id="inputEmail" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="{{ __('Email address') }}" required autofocus autocomplete="email">
                                <label for="inputEmail">{{ __('Email address') }}</label>

                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-label-group">
                                <input id="inputPassword" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="{{ __('Password') }}" required autocomplete="new-password">
                                <label for="inputPassword">{{ __('Password') }}</label>

                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-label-group">
                                <input id="inputConfirmPassword" type="password" class="form-control" name="password_confirmation" placeholder="{{ __('Password') }}" required autocomplete="new-password">
                                <label for="inputConfirmPassword">{{ __('Confirm password') }}</label>
                            </div>

                            <button class="btn btn-lg btn-primary btn-block text-uppercase" type="submit">{{ __('Register') }}</button>
                            <a class="d-block text-center mt-2 small" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
