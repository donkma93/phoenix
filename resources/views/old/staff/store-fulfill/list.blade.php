@extends('layouts.staff')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('staff.dashboard')
        ],
        [
            'text' => 'Store Fulfill'
        ]
    ]
])
@endsection

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('Store Fulfill list') }}</h2>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('staff.storeFulfill.list') }}" class="form-horizontal" role="form">
                <div class="form-group search-form-group">
                    <label for="name" class="col-form-label search-label"><b>{{ __('Name') }}</b></label>
                    <div class="search-input">
                        <input type="input" id="store-input"  list="dropdown-store" class="form-control w-100" name="name" value="@if (isset($oldInput['name'])){{$oldInput['name']}}@endif" autocomplete="off" />
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="code" class="col-form-label search-label"><b>{{ __('Code') }}</b></label>
                    <div class="search-input">
                        <input type="input" name="code" class="form-control w-100" value="@if (isset($oldInput['code'])){{$oldInput['code']}}@endif" />
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="is_delete" class="col-form-label search-label"><b>{{ __('Show Deleted') }}</b></label>
                    <div class="search-input search-radio">
                        <div class="form-check form-check-inline amr-20">
                            <input class="form-check-input" id="all-verify" type="radio" value="0" name="onlyDeleted"
                            @if(isset($oldInput['onlyDeleted']) && $oldInput['onlyDeleted'] == 0)
                                checked
                            @endif
                            >
                            <label class="form-check-label" for="all-member">All</label>
                        </div>
                        <div class="form-check form-check-inline amr-20">
                            <input class="form-check-input" id="verify-only" type="radio" value="1" name="onlyDeleted"
                            @if(isset($oldInput['onlyDeleted']) && $oldInput['onlyDeleted'] == 1)
                                checked
                            @endif>
                            <label class="form-check-label" for="only-deleted">Only deleted</label>
                        </div>
                        <div class="form-check form-check-inline amr-20">
                            <input class="form-check-input" id="not-verify" type="radio" value="" name="onlyDeleted"
                            @if(!isset($oldInput['onlyDeleted']))
                                checked
                            @endif>
                            <label class="form-check-label" for="not-delete">Not deleted</label>
                        </div>
                    </div>
                </div>

                <div class="search-form-group">
                    <div class="search-label d-none d-sm-block"></div>
                    <div class="search-input text-center text-sm-left">
                        <input class="btn btn-primary" type="submit" value="{{ __('Search') }}">
                    </div>
                </div>
            </form>
        </div>
        <div class="card-footer">
            @if (count($stores) == 0)
                <div class="text-center">{{ __('No data.') }}</div>
            @else
                <div class="table-responsive">
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-store-list-table">
                        <thead>
                            <tr>
                                <th>{{ __('No') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stores as $store)
                            <tr>
                                <td>{{ ($stores->currentPage() - 1) * $stores->perPage() + $loop->iteration }}</td>
                                <td>{{ $store['name'] }}</td>
                                <td>{{ $store['code'] }}</td>
                                <td>@if(isset($store['deleted_at']))
                                        {{ __('Deleted') }}
                                    @else
                                        {{ __('In use') }}
                                    @endif
                                </td>
                                <td>
                                    <a class="btn btn-info" href="{{ route('staff.storeFulfill.detail', ['id' => $store->id]) }}">Detail</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center justify-content-md-end amt-16">
                    {{ $stores->links('components.pagination') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
  <script>
    let stores = @php echo json_encode($storeNames) @endphp;
    filterInput(document.getElementById("store-input"), stores, 'dropdown-store');
  </script>
@endsection

