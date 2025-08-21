@extends('layouts.staff')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('staff.dashboard')
        ],
        [
            'text' => 'Partner',
            'url' => route('admin.partner.list')
        ],
        [
            'text' => 'Create Partner'
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

    <form action="{{ route('admin.partner.create') }}" method="POST" enctype="multipart/form-data">
    @csrf
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h2 class="mb-0">{{ __('Create Partner') }}</h2>
            </div>
            <div class="card-body">

                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Partner Code') }}</b></label>
                    <div class="search-input">
                        <input id="partner_code" type="text" class="form-control w-100 @error('partner_code') is-invalid 
                        @enderror" name="partner_code">
                        @error('partner_code')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Partner Name') }}</b></label>
                    <div class="search-input">
                        <input id="partner_name" type="text" class="form-control w-100 @error('partner_name') is-invalid @enderror" name="partner_name">
                        @error('partner_name')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Phone') }}</b></label>
                    <div class="search-input">
                        <input id="phone" type="text" class="form-control w-100 @error('phone') is-invalid
                         @enderror" name="phone">
                        @error('phone')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                 <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Address') }}</b></label>
                    <div class="search-input">
                        <input id="address" type="text" class="form-control w-100 @error('address') is-invalid
                         @enderror" name="address">
                        @error('address')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="search-form-group">
                    <div class="search-label d-none d-sm-block"></div>
                    <div class="search-input text-center text-sm-left">
                        <input class="btn btn-primary" type="submit" value="{{ __('Create') }}">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal -->
<div id="scan-modal" class="modal fade bd-example-scan-lg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <video id="video" style="border: 1px solid gray; width: 100%; height: 100%"></video>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
  <script>
  </script>
@endsection
