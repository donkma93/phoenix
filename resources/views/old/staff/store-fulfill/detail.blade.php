@extends('layouts.staff')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('staff.dashboard')
        ],
        [
            'text' => 'Store Fulfill',
            'url' => route('staff.storeFulfill.list')
        ],
        [
            'text' => 'Detail'
        ]
    ]
])
@endsection

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('Store detail') }}</h2>
        </div>
        <div class="card-body">
            <div class="form-group search-form-group">
                <label for="first_name" class="col-form-label search-label"><b>{{ __('Name') }}</b> </label>
                <div class="col-form-label">
                    {{ $store['name'] }}
                </div>
            </div>

            <div class="form-group search-form-group">
                <label for="last_name" class="col-form-label search-label"><b>{{ __('Code') }}</b></label>
                <div class="col-form-label">
                    {{ $store['code']}}
                </div>
            </div>
            
            <div class="form-group search-form-group">
                <label for="last_name" class="col-form-label search-label"><b>{{ __('Status') }}</b></label>
                <div class="col-form-label">
                    @if(isset($store['deleted_at']))
                        {{ __('Deleted') }}
                    @else
                        {{ __('In use') }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
