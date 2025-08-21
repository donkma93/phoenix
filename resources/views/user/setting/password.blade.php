@extends('layouts.user')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Home',
            'url' => route('home', ['locale' => app()->getLocale()])
        ],
        [
            'text' => 'Profile',
            'url' => route('setting.profile.index')
        ],
        [
            'text' => 'Change password'
        ]
    ]
])
@endsection

@if(session('success'))
@section('flash')
@include('layouts.partials.flash', [
    'messages' => [
        [
            'content' => session('success')
        ]
    ]
])
@endsection
@endif

@if(session('fail'))
@section('flash')
@include('layouts.partials.flash', [
    'messages' => [
       [
        'content' => session('fail'),
        'type' => 'error'
       ]
    ]
])
@endsection
@endif

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-header">
            <h2 class="mb-0">{{ __('Change password') }}</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('setting.password.update') }}" class="form-horizontal" role="form" enctype="multipart/form-data">
                @method('put')
                @csrf

                <div class="form-group search-form-group">
                    <label for="current_password" class="col-form-label search-label">
                        <b>{{ __('Current Password') }}</b>
                    </label>
                    <div class="search-input">
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" name="current_password" id="current_password">
                        @error('current_password')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="password" class="col-form-label search-label">
                        <b>{{ __('New password') }}</b>
                    </label>
                    <div class="search-input">
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="password_confirmation" class="col-form-label search-label">
                        <b>{{ __('Confirm Password') }}</b>
                    </label>
                    <div class="search-input">
                        <input id="password_confirmation" type="password" class="form-control w-100 @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required>
                    </div>
                </div>

                <div class="search-form-group">
                    <div class="search-label d-none d-sm-block"></div>
                    <div class="search-input text-center text-sm-left">
                        <input class="btn btn-primary" type="submit" value="{{ __('Update Password') }}">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
