@extends('layouts.staff')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('staff.dashboard')
        ],
        [
            'text' => 'Category',
            'url' => route('staff.category.list')
        ],
        [
            'text' => 'Create Category'
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

    <form action="{{ route('staff.category.create') }}" method="POST" enctype="multipart/form-data">
    @csrf
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h2 class="mb-0">{{ __('Create Category') }}</h2>
            </div>
            <div class="card-body">
                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Name') }}</b></label>
                    <div class="search-input position-relative">
                        <input type="text" class="form-control w-100 @error('name') is-invalid @enderror" name="name">
                        @error('name')
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
@endsection
