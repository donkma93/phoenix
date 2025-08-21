@extends('layouts.user')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('dashboard')
        ],
        [
            'text' => 'Inventory',
            'url' => route('inventories.list')
        ],
        [
            'text' => $inventory['id']
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
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('Inventory detail') }}</h2>
            <button type="button" class="btn btn-info amb-16" data-toggle="modal" data-target="#modal">Config remind</button>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('inventories.updateIncomming') }}" class="form-horizontal" role="form">
                @csrf
                <input type="hidden" name="id" value="{{ $inventory->id }}" />
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Product') }}</b></label>
                            <div class="search-input col-form-label">
                                {{ $inventory->product->name }}
                            </div>
                        </div>
                        {{-- <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Sku') }}</b></label>
                            <div class="search-input col-form-label">
                                {{ $inventory->sku }}
                            </div>
                        </div> --}}
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Incoming Quantity') }}</b></label>
                            <div class="search-input">
                                <input type="number" class="form-control w-100 @error('incoming') is-invalid @enderror" id="incoming"
                                    name="incoming" value="{{ $inventory->incoming ?? 0 }}" autocomplete="off" min="0"/>
                                @error('incoming')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group search-form-group">
                            <div class="search-label d-none d-sm-block"></div>
                            <div class="search-input text-center text-sm-left">
                                <input class="btn btn-success" type="submit" value="{{ __('Update') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('Histories') }}</h2>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-align-middle table-bordered table-striped table-sm" id="user-inventory-history-table">
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
    </div>
</div>
<!-- Modal -->
<div id="modal" class="modal fade bd-example-scan-lg modal-fullsize" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reorder remind</h5>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('inventories.updateRemind') }}" class="form-horizontal" role="form">
                @csrf
                    <input type="hidden" name="id" value="{{ $inventory->id }}">
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Remind when') }}</b></label>
                        <div class="search-input position-relative">
                            <input id="min" type="number" class="form-control w-100 @error('min') is-invalid @enderror" name="min" autocomplete="off" value="{{ $inventory->min }}">
                            @error('min')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Is remind') }}</b></label>
                        <div class="col-form-label search-label">
                            <input type="checkbox" name="is_remind" id="is_remind" {{ old('is_remind') || $inventory->is_remind ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <div class="search-label d-none d-sm-block"></div>
                        <div class="search-input text-center text-sm-left">
                            <input class="btn btn-success" type="submit" value="{{ __('Update') }}">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
