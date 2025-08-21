@extends('layouts.staff')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('staff.dashboard')
        ],
        [
            'text' => 'Setting'
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
        <form action="{{ route('staff.setting.updateProfile') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">{{ __('User profile') }}</h2>
                </div>
                <div class="row card-body">
                    <div class="col-md-5">
                        <div class="form-group search-form-group">
                            <div id="img-preview" class="img-preview shadow rounded-circle show" data-size="250px" data-init="
                            @if(isset($userInfo['avatar']))
                            {{
                                asset($userInfo['avatar'])
                            }}
                            @else
                            {{
                                asset('images/default.jpg')
                            }}
                            @endif
                            "></div>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-secondary btn-sm" type="button" id="ava-btn">
                                {{ __('Change avatar') }}
                            </button>
                            <input id="avatar" accept="image/*" hidden type="file" class="img-picker @error('avatar') is-invalid @enderror" name="avatar" data-target="#img-preview">
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="form-group search-form-group">
                            <label for="first_name" class="col-form-label search-label"><b>{{ __('First name') }}</b></label>
                            <div class="search-input">
                                <input id="first_name" type="text" class="form-control w-100 @error('first_name') is-invalid @enderror" name="first_name" value="{{ $userInfo['first_name'] }}">
                                @error('first_name')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group search-form-group">
                            <label for="last_name" class="col-form-label search-label"><b>{{ __('Last name') }}</b></label>
                            <div class="search-input">
                                <input id="last_name" type="text" class="form-control w-100 @error('last_name') is-invalid @enderror" name="last_name" value="{{ $userInfo['last_name'] }}">
                                @error('last_name')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Gender') }}</b></label>
                            <div class="search-input search-radio">
                                <div class="form-check d-inline-flex mr-3">
                                    <input class="form-check-input" type="radio" name="gender" id="gender-male"
                                    @if($userInfo['gender'] == 0)
                                        checked
                                    @endif
                                    value="0">
                                    <label class="form-check-label" for="gender-male">{{__('Male')}}</label>
                                </div>
                                <div class="form-check d-inline-flex ">
                                    <input class="form-check-input" type="radio" name="gender" id="gender-female"
                                    @if($userInfo['gender'] == 1)
                                        checked
                                    @endif value="1">
                                    <label class="form-check-label" for="gender-female">{{__('Female')}}</a></label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label for="birthday" class="col-form-label search-label"><b>{{ __('Birthday') }}</b></label>
                            <div class="search-input">
                                <input id="birthday" type="text" class="form-control w-100 date-picker @error('birthday') is-invalid @enderror" name="birthday" value="{{ date('d-m-Y', strtotime($userInfo['birthday'])) }}">
                                @error('birthday')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label for="phone" class="col-form-label search-label"><b>{{ __('Phone Number') }}</b></label>
                            <div class="search-input">
                                <input id="phone" type="text" class="form-control w-100 @error('phone') is-invalid @enderror" name="phone" value="{{ $userInfo['phone'] }}">
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
            </div>
        </form>
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
