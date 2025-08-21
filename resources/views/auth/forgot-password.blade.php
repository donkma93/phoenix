@extends('layouts.app', [
    'class' => 'register-page',
    'backgroundImagePath' => 'img/bg/jan-sendereks.jpg',
    'folderActive' => '',
    'elementActive' => ''
])

@section('styles')
    <style>
        .alert .close~span {
             max-width: unset !important;
        }
    </style>
@endsection

@section('content')
    <div class="content mx-5">
        @if (session('success'))
            <div class="row justify-content-end">
                <div class="col-md-4">
                    <div class="alert alert-success alert-dismissible fade show">
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
                                    <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" autofocus>
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                    @enderror
                                </div>

                                <div class="form-group mb-0 d-flex">
                                    <button type="submit" class="btn btn-primary mx-auto">
                                        {{ __('auth.fp-button') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
