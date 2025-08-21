@extends('layouts.admin')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('admin.dashboard')
        ],
        [
            'text' => 'Warehouse'
        ]
    ]
])
@endsection

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('Warehouse list') }}</h2>
            <a class="btn btn-success" href="{{ route('admin.warehouse.new') }}">
                {{ __('New Warehouse') }}
            </a>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.warehouse.list') }}" class="form-horizontal" role="form">
                <div class="form-group search-form-group">
                    <label for="name" class="col-form-label search-label"><b>{{ __('Name') }}</b></label>
                    <div class="search-input">
                        <input type="input" id="warehouse-input"  list="dropdown-warehouse" class="form-control w-100" name="name" value="@if (isset($oldInput['name'])){{$oldInput['name']}}@endif" />
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
            @if (count($warehouses) == 0)
                <div class="text-center">{{ __('No data.') }}</div>
            @else
                <div class="table-responsive">
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="admin-warehouse-list-table">
                        <thead>
                            <tr>
                                <th>{{ __('No') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Address') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($warehouses as $warehouse)
                            <tr>
                                <td>{{ ($warehouses->currentPage() - 1) * $warehouses->perPage() + $loop->iteration }}</td>
                                <td>{{ $warehouse['name'] }}</td>
                                <td>{{ $warehouse['address'] }}</td>
                                <td>@if(isset($warehouse['deleted_at']))
                                        {{ __('Deleted') }}
                                    @else
                                        {{ __('In use') }}
                                    @endif
                                </td>
                                <td>
                                    <a class="btn btn-info" href="{{ route('admin.warehouse.detail', ['id' => $warehouse->id]) }}">Detail</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center justify-content-md-end amt-16">
                    {{ $warehouses->links('components.pagination') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
  <script>
    let warehouses = @php echo json_encode($warehouseList) @endphp;
    filterInput(document.getElementById("warehouse-input"), warehouses, 'dropdown-warehouse');
  </script>
@endsection

