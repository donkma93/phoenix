@extends('layouts.app', [
    'class' => 'register-page',
    'backgroundImagePath' => 'img/bg/jan-sendereks.jpg',
    'folderActive' => '',
    'elementActive' => ''
])

@section('styles')
    <style>
        .form-check input[type=checkbox] {
            opacity: unset !important;
            position: unset !important;
            visibility: unset !important;
        }
        .form-check .form-check-label {
            padding-left: 8px !important;
        }
    </style>
@endsection

@section('content')
    <div class="content">
        <div class="container">
            <div class="d-flex justify-content-center">
                <div class="auth-wrapper">
                    <div class="card" style="width: 400px;">
                        <h2 class="card-header">{!! __('welcome.register') !!}</h2>
                        <div class="card-body">
                            <form method="POST" action="{{ route('register', ['locale' => app()->getLocale()]) }}" enctype="multipart/form-data">
                                @csrf

                                <div class="form-group">
                                    <label for="email">{{ __('Email') }} <span style="color: red;">*</span></label>
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="password">{!! __('welcome.password') !!} <span style="color: red;">*</span></label>
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="password_confirmation">{!! __('welcome.confirm-password') !!} <span style="color: red;">*</span></label>
                                    <input id="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required autocomplete="new-password">
                                    @error('password_confirmation')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="partner_code">{!! __('welcome.partner-code') !!} <span style="color: red;">*</span></label>
                                    <input id="partner_code" type="text" class="form-control @error('partner_code') is-invalid @enderror" name="partner_code" value="{{ old('partner_code') }}">
                                    @error('partner_code')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                    @enderror
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" id="agreement" type="checkbox" />
                                    <label class="form-check-label" for="agreement">{!! __('welcome.agree-to') !!}&nbsp;</label>
                                    <a href="{{ route('term', ['locale' => app()->getLocale()]) }}">{!! __('welcome.terms-of-service-title-1') !!}</a>
                                </div>

                                <div class="text-center amt-24">
                                    <button type="submit" id="submit-btn" disabled class="btn btn-primary text-white apy-8 font-16 w-100">
                                        {!! __('welcome.register') !!}
                                    </button>
                                    <a href="{{ route('login', ['locale' => app()->getLocale()]) }}" class="text-primary"> {!! __('welcome.already-registered') !!}</a>
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
            var agreement = $('#agreement')

            function checkValidate() {
                if( $(email).val().length &&  $(password).val().length &&  $(confirm).val() == $(password).val() && $(agreement).is(':checked')) {
                    $('#submit-btn').attr('disabled', false)
                } else {
                    $('#submit-btn').attr('disabled', true)
                }
            }
            checkValidate()
            email.on('keyup', checkValidate)
            password.on('keyup', checkValidate)
            confirm.on('keyup', checkValidate)
            agreement.on('change', checkValidate)
        })
    </script>
@endpush
