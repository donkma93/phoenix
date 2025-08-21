@extends('layouts.admin')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('admin.dashboard')
        ],
        [
            'text' => 'Warehouse',
            'url' => route('admin.warehouse.list')
        ],
        [
            'text' => 'Create Warehouse'
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
    <form action="{{ route('admin.warehouse.create') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">{{ __('Create Warehouse') }}</h2>
            </div>
            <div class="card-body">
                <div class="form-group search-form-group">
                    <label for="name" class="col-form-label search-label"><b>{{ __('Name') }}</b></label>
                    <div class="search-input">
                        <input type="text" class="form-control w-100 @error('name') is-invalid @enderror" name="name">
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group search-form-group">
                    <label for="address" class="col-form-label search-label"><b>{{ __('Address') }}</b></label>
                    <div class="search-input">
                        <textarea class="form-control w-100 @error('address') is-invalid @enderror" name="address" rows="3" placeholder="Address.."></textarea>
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
                        <input class="btn btn-primary" type="submit" value="{{ __('Create Warehouse') }}">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
