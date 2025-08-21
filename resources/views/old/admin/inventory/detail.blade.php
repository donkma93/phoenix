@extends('layouts.admin')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('admin.dashboard')
        ],
        [
            'text' => 'Inventory',
            'url' => route('admin.inventory.list')
        ],
        [
            'text' => $inventory['id']
        ]
    ]
])
@endsection

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('Inventory detail') }}</h2>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Product') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $inventory->product->name }}
                        </div>
                    </div>
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Sku') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $inventory->sku }}
                        </div>
                    </div>
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Store') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $inventory->storeFulfill->name ?? '' }}
                        </div>
                    </div>
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Image') }}</b></label>
                        @if(!isset($inventory->product->image_url))
                            {{ __('No image') }}
                        @endif
                    </div>
                    @if(isset($inventory->product->image_url))
                        <div class="form-group search-form-group">
                            <img  width="300" height="300" src="{{ asset($inventory->product->image_url) }}" alt="Product image" class="img-fluid">
                        </div>
                    @endif
                </div>

                <div class="col-md-6">
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Incoming') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $inventory->incoming }}
                        </div>
                    </div>
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Available') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $inventory->available }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('Histories') }}</h2>
        </div>
        
        @if (count($histories) == 0)
            <div class="text-center">No data.</div>
        @else
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="admin-inventory-history-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>{{ __('Start') }}</th>
                                <th>{{ __('Time(Hour)') }}</th>
                                <th>{{ __('Previous Incoming') }}</th>
                                <th>{{ __('Previous Available') }}</th>
                                <th>{{ __('Incoming') }}</th>
                                <th>{{ __('Available') }}</th>
                                <th>{{ __('Email') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($histories as $key=>$history)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $history->start_at }}</td>
                                <td>{{ $history->hour ?? 0 }}</td>
                                <td>{{ $history->previous_incoming ?? 0 }}</td>
                                <td>{{ $history->previous_available ?? 0 }}</td>
                                <td>{{ $history->incoming ?? 0 }}</td>
                                <td>{{ $history->available ?? 0 }}</td>
                                <td>{{ $history->user->email }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
