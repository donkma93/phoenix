@extends('layouts.app', [
    'class' => 'register-page',
    'backgroundImagePath' => 'img/bg/jan-sendereks.jpg',
    'folderActive' => '',
    'elementActive' => ''
])

@section('styles')
    <style>

    </style>
@endsection

@section('content')
    <div class="content mx-5">
        @if (session('success'))
            <div class="row justify-content-end">
                <div class="col-md-4">
                    <div class="alert auto-hide alert-success alert-dismissible fade show">
                        <button type="button" aria-hidden="true" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="nc-icon nc-simple-remove"></i>
                        </button>
                        <span>
                            <b> Success - </b>
                            {{ session('success') }}
                        </span>
                    </div>
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="row justify-content-end">
                <div class="col-md-4">
                    <div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" aria-hidden="true" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="nc-icon nc-simple-remove"></i>
                        </button>
                        <span>
                            <b> Error - </b>
                            {{ session('error') }}
                        </span>
                    </div>
                </div>
            </div>
        @endif
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">{{ __('Reset password') }}</div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('password.update', ['locale' => app()->getLocale()]) }}">
                                @csrf

                                <!-- Password Reset Token -->
                                <input type="hidden" name="token" value="{{ $token }}">

                                <div class="form-group row">
                                    <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Email') }}</label>
                                    <div class="col-md-6">
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $email) }}" required autocomplete="email" autofocus>
                                        @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>
                                    <div class="col-md-6">
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                                        @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="password_confirmation" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>
                                    <div class="col-md-6">
                                        <input id="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required>
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <div class="col-md-8 offset-md-4">
                                        <button id="submit-btn" type="submit" class="btn btn-primary" disabled>
                                            {{ __('Reset Password') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var email = $('#email')
            var password = $('#password')
            var confirm = $('#password_confirmation')

            function checkValidate() {
                if( $(email).val().length &&  $(password).val().length &&  $(confirm).val() == $(password).val()) {
                    $('#submit-btn').attr('disabled', false)
                } else {
                    $('#submit-btn').attr('disabled', true)
                }
            }
            checkValidate()
            email.on('keyup', checkValidate)
            password.on('keyup', checkValidate)
            confirm.on('keyup', checkValidate)
        })
    </script>
@endpush
