@extends('layouts.user')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Home',
            'url' => route('home', ['locale' => app()->getLocale()])
        ],
        [
            'text' => 'Profile'
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

@error('avatar')
@section('flash')
@include('layouts.partials.flash', [
    'messages' => [
        [
            'content' => $message,
            'type' => 'error'
        ]
    ]
])
@endsection
@enderror


@section('content')
<div class="fade-in amy-16">
    <div class="card">
        <div class="card-header">
            <h2 class="mb-0">{{ __('User Profile') }}</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('setting.profile.update') }}" class="form-horizontal" role="form" enctype="multipart/form-data">
                @method('put')
                @csrf

                <div class="row">
                    <div class="col-md-5">
                        <div id="img-preview" class="img-preview rounded-circle shadow show" data-size="250px"
                            data-init="{{ isset($profile['avatar']) ?  asset($profile['avatar']) : asset('images/default.jpg') }}
                        "></div>

                        <div class="text-center amb-24 amt-8">
                            <button class="btn btn-secondary btn-sm" type="button" id="ava-btn">
                                {{ __('Change avatar') }}
                            </button>
                            <input id="avatar" hidden type="file" accept="image/*" class="img-picker @error('avatar') is-invalid @enderror" name="avatar" data-target="#img-preview" />
                        </div>
                    </div>

                    <div class="col-md-7">
                        <div class="form-group search-form-group">
                            <label for="first_name" class="search-label col-form-label"><b>{{ __('First name') }}</b></label>
                            <div class="search-input">
                                <input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror"
                                    name="first_name" value="{{ $profile['first_name'] ?? '' }}">
                                @error('first_name')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group search-form-group">
                            <label for="last_name" class="search-label col-form-label"><b>{{ __('Last name') }}</b></label>
                            <div class="search-input">
                                <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror"
                                    name="last_name" value="{{ $profile['last_name'] ?? '' }}">
                                @error('last_name')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="search-label col-form-label"><b>{{ __('Gender') }}</b></label>
                            <div class="search-input search-radio">
                                @foreach (App\Models\UserProfile::$genderName as $value => $name )
                                    <div class="form-check d-inline-flex mr-3">
                                        <input class="form-check-input" type="radio" name="gender" id="{{ 'gender-' . $name }}"
                                            @if(isset($profile['gender']) && $profile['gender'] == $value)
                                                checked
                                            @endif
                                            value="{{ $value }}"
                                        >
                                        <label class="form-check-label" for="{{ 'gender-' . $name }}">{{ __(ucfirst($name)) }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label for="birthday" class="search-label col-form-label"><b>{{ __('Birthday') }}</b></label>
                            <div class="search-input">
                                <input id="birthday" type="text" class="form-control date-picker @error('birthday') is-invalid @enderror" name="birthday"
                                    value="{{ isset($profile['birthday']) ? $profile['birthday']->format('Y-m-d') : '' }}"
                                >
                                @error('birthday')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label for="phone" class="search-label col-form-label"><b>{{ __('Phone number') }}</b></label>
                            <div class="search-input">
                                <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ $profile['phone'] ?? '' }}">
                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label for="address" class="search-label col-form-label"><b>{{ __('Address') }}</b></label>
                            <div class="search-input">
                            <textarea class="form-control" name="address" rows="3" >{{ $address['building'] ?? '' }} </textarea>
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label for="post_code" class="search-label col-form-label"><b>{{ __('Post code') }}</b></label>
                            <div class="search-input">
                                <input id="post_code" type="text" class="form-control @error('post_code') is-invalid @enderror" name="post_code" value="{{ $address['post_code'] ?? '' }}">
                                @error('post_code')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="search-form-group">
                            <div class="search-label d-none d-sm-block"></div>
                            <div class="search-input text-center text-sm-left">
                                <input class="btn btn-primary" type="submit" value="{{ __('Update profile') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#ava-btn').click(function() {
            $('#avatar').click()
        })
    })
</script>
@endsection
