@extends('layouts.auth')

@section('content')
<div class="container">
    <div class="d-flex justify-content-center">
        <div class="auth-wrapper">
            <div class="card">
                <h2 class="card-header">{!! __('welcome.login') !!}</h2>
                <div class="card-body font-16">
                    <form method="POST" action="{{ route('login', ['locale' => app()->getLocale()]) }}">
                        @csrf
                        <div class="form-group">
                            <label for="email">{{ __('Email') }}</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">{!! __('welcome.password') !!}</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="form-group d-flex flex-wrap align-items-center justify-content-between">
                            <div class="form-check mr-3">
                                <input class="form-check-input amt-6" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    {!! __('welcome.remember-me') !!}
                                </label>
                            </div>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request', ['locale' => app()->getLocale()]) }}" class="atext-blue-600">
                                    {!! __('welcome.forgot-password') !!}
                                </a>
                            @endif
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary text-white w-100 apy-8 font-16">
                                {!! __('welcome.login') !!}
                            </button>
                            <div class="amt-4">
                                {!! __('welcome.dont-have-account') !!}
                                <a href="{{ route('register', ['locale' => app()->getLocale()]) }}" class="atext-blue-600">
                                    {!! __('welcome.register-now') !!}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
