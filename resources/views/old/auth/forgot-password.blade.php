@extends('layouts.auth')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="auth-wrapper">
            <div class="card">
                <h2 class="card-header">{{ __('auth.fp-title') }}</h2>
                <div class="card-body">
                    <div class="mb-4">
                        {{ __('auth.fp-content') }}
                    </div>

                    <form method="POST" action="{{ route('password.email', ['locale' => app()->getLocale()]) }}">
                        @csrf

                        <div class="form-group">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary w-100">
                                {{ __('auth.fp-button') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
