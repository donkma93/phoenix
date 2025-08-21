@extends('layouts.auth')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="mb-4">
                        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
                    </div>
                    @if (session('status') == 'verification-link-sent')
                        <div class="mb-4 ">
                            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                        </div>
                    @endif
                    <div class="d-flex items-items-center justify-content-end">
                        <form method="GET" action="{{ route('verification.send', ['locale' => app()->getLocale()]) }}" class="mr-2">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                {{ __('Resend Verification Email') }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('logout', ['locale' => app()->getLocale()]) }}">
                            @csrf
                            <button type="submit" class="btn btn-secondary">
                                {{ __('Log out') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
