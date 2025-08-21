@extends('layouts.app', [
    'class' => '',
    'folderActive' => '',
    'elementActive' => 'users',
])

@section('styles')
    <style>
        .px-30 {
            padding-left: 30px !important;
            padding-right: 30px !important;
        }

        .search-form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .col-form-label {
            padding-top: calc(0.375rem + 1px);
            padding-bottom: calc(0.375rem + 1px);
            margin-bottom: 0;
            font-size: inherit;
            line-height: 1.5;
        }

        .search-form-group .search-label {
            min-width: 160px;
            text-align: left;
        }

        .search-form-group .search-input {
            flex: 1;
        }

        .form-check .form-check-label {
            padding-left: 0;
        }

        @media (min-width: 576px) {
            .search-form-group {
                flex-direction: row;
            }

            .search-form-group .search-input {
                max-width: 360px;
            }
        }
    </style>
@endsection

@section('content')
<div class="content">
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
<div class="fade-in">
    <form action="{{ route('staff.user.createUser') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card">
            <div class="card-header">
                <h2 class="mb-0 px-30">{{ __('Create User') }}</h2>
            </div>
            <div class="card-body">
                <div class="px-30">
                    <div class="form-group search-form-group">
                        <label for="email" class="col-form-label search-label">{{ __('Email') }} </label>
                        <div class="search-input">
                            <input id="email" type="text" class="form-control w-100 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}">
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
                            <input id="password" type="password" class="form-control w-100 @error('password') is-invalid @enderror" name="password" required autoComplete="new-password"
                                   value="{{ old('password') }}"
                            >
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
                            <input id="password_confirmation" type="password" class="form-control w-100 @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required
                                   value="{{ old('password_confirmation') }}"
                            >
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label for="partner_code" class="col-form-label search-label">{{ __('Partner Code') }} </label>
                        <div class="search-input">
                            <input id="partner_code" type="text" class="form-control w-100" name="partner_code" required
                                   value="{{ old('partner_code') }}"
                            >
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label for="type" class="col-form-label search-label">{{ __('Role') }}</label>
                        <div class="search-input">
                            <select id="type" name="role" class="form-control w-100">
                                @foreach (App\Models\User::$roleName as $key => $role)
                                    @if($key != App\Models\User::ROLE_ADMIN)
                                        <option value="{{ $key }}"
                                                @if ($key == old('role'))
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
                            <input class="btn btn-round btn-primary" type="submit" value="{{ __('Create User') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
</div>
@endsection
