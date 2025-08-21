@extends('layouts.staff')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('staff.dashboard')
        ],
        [
            'text' => 'Inventory',
            'url' => route('staff.inventory.list')
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
            <!-- <form method="POST" action="{{ route('staff.inventory.updateAvailable') }}" class="form-horizontal" role="form">
                @csrf
                <input type="hidden" name="sku" value="{{ $inventory->sku }}"/>
                <input type="submit" class="btn btn-info" value="Update Available"/>
            </form> -->
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('staff.inventory.update') }}" class="form-horizontal" role="form">
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
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Sku') }}</b></label>
                            <div class="search-input col-form-label">
                                {{ $inventory->sku }}
                            </div>
                        </div>
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Store') }}</b></label>
                            <div class="search-input">
                                <input type="text" class="form-control w-100 @error('store') is-invalid @enderror" id="store-name-input" list="dropdown-store-name" name="store" value="{{ $inventory->storeFulfill->name ?? '' }}" autocomplete="off"/>
                                @error('store')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
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
                            <label class="col-form-label search-label"><b>{{ __('Available') }}</b></label>
                            <div class="search-input">
                                <input type="number" class="form-control w-100 @error('available') is-invalid @enderror" id="available"
                                    name="available" value="{{  $inventory->available ?? 0 }}" autocomplete="off" min="0"/>
                                @error('incoming')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Working day') }}</b></label>
                            <div class="search-input">
                                <input type="input" class="form-control w-100 @error('start_at') is-invalid @enderror" id="start_at"
                                    name="start_at" value="{{ date('Y-m-d H:i:s') }}" autocomplete="off"/>
                                @error('start_at')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Working Hour') }}</b></label>
                            <div class="search-input">
                                <input type="number" class="form-control w-100 @error('hour') is-invalid @enderror" id="hour"
                                    name="hour" value="0" autocomplete="off" min="0"/>
                                @error('hour')
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
        
        @if (count($histories) == 0)
            <div class="text-center">No data.</div>
        @else
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-inventory-history-table">
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
                                <th></th>
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
                                <td>
                                    <button type="button" class="btn btn-info apx-8" data-toggle="modal" data-target="#group-modal" 
                                    onclick="edit({{ $history->id }}, '{{ $history->start_at }}', {{ $history->hour }}, {{ $history->incoming }}, {{ $history->available }})">Edit</button> 
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
<!-- Modal -->
<div id="group-modal" class="modal" tabindex="-2" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Edit
                </h5>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('staff.inventory.updateHistory') }}" class="form-horizontal" role="form">
                    @csrf
                    <input type="hidden" id="history-id" name="id" value="" />
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Start') }}</b></label>
                        <div class="search-input">
                            <input type="text" class="form-control" name="start" id="history-start"  value=""  />
                        </div>
                    </div>
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Time(Hour)') }}</b></label>
                        <div class="search-input position-relative">
                            <input type="text" id="history-hour" class="form-control w-100" name="hour" value="" />
                        </div>
                    </div>
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Incoming') }}</b></label>
                        <div class="search-input">
                            <input type="text" class="form-control" name="incoming" id="history-incoming" value="" />
                        </div>
                    </div>
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Available') }}</b></label>
                        <div class="search-input">
                            <input type="text" class="form-control" name="available" id="history-available" value="" />
                        </div>
                    </div>
                    <div class="search-form-group">
                        <div class="search-label d-none d-sm-block"></div>
                        <div class="search-input text-center text-sm-left">
                            <input class="btn btn-primary" type="submit" value="{{ __('Update') }}">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
  <script type="text/javascript">
    let stores = @php echo json_encode($stores) @endphp;

    filterInput(document.getElementById("store-name-input"), stores, 'dropdown-store-name');

    function edit(id, start, hour, incoming, available) {
        $('#history-id').val(id)
        $('#history-start').val(start)
        $('#history-hour').val(hour)
        $('#history-incoming').val(incoming)
        $('#history-available').val(available)
    }
  </script>
@endsection