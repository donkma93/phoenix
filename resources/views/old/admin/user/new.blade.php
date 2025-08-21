@extends('layouts.admin')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('admin.dashboard')
        ],
        [
            'text' => 'User',
            'url' => route('admin.user.list')
        ],
        [
            'text' => 'Create user'
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
    <form action="{{ route('admin.user.createUser') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">{{ __('Create User') }}</h2>
            </div>
            <div class="card-body">
                <div class="form-group search-form-group">
                    <label for="email" class="col-form-label search-label">{{ __('Email') }} </label>
                    <div class="search-input">
                        <input id="email" type="text" class="form-control w-100 @error('email') is-invalid @enderror" name="email">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group search-form-group">
                    <label for="password" class="col-form-label search-label">{{ __('Password') }}</label>
                    <div class="search-input">
                        <input id="password" type="password" class="form-control w-100 @error('password') is-invalid @enderror" name="password" required>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="password_confirmation" class="col-form-label search-label">{{ __('Confirm Password') }}</label>
                    <div class="search-input">
                        <input id="password_confirmation" type="password" class="form-control w-100 @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required>
                    </div>
                </div>

                 <div class="form-group search-form-group">
                    <label for="partner_code" class="col-form-label search-label">{{ __('Partner Code') }} </label>
                    <div class="search-input">
                        <input id="partner_code" type="text" class="form-control w-100" name="partner_code" required>
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="password_confirmation" class="col-form-label search-label">{{ __('Role') }}</label>
                    <div class="search-input">
                        <select id="type" name="role" class="form-control w-100">
                            @foreach (App\Models\User::$roleName as $key => $role)
                                @if($key != App\Models\User::ROLE_ADMIN)
                                    <option value="{{ $key }}"
                                        @if ($key == App\Models\User::ROLE_USER)
                                            selected="selected"
                                        @endif
                                    >{{ ucfirst($role) }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="search-form-group">
                    <div class="search-label d-none d-sm-block"></div>
                    <div class="search-input text-center text-sm-left">
                        <input class="btn btn-primary" type="submit" value="{{ __('Create User') }}">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
