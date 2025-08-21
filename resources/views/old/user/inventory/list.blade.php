@extends('layouts.user')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('dashboard')
        ],
        [
            'text' => 'Inventory'
        ]
    ]
])
@endsection

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Inventory list</h2>
            <a class="btn btn-success" href="{{ route('inventories.remind') }}">
                {{ __('Config remind') }}
            </a>
                 <div class="search-input text-center text-sm-left">
                        <input class="btn btn-primary mb-3"      value="{{ __('Print') }}">
                    </div>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('inventories.list') }}" class="form-horizontal" role="form">
                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Product') }}</b></label>
                    <div class="search-input">
                        <input type="text" class="form-control" name="product" id="product-input" list="dropdown-product"  value="@if (isset($oldInput['product'])){{$oldInput['product']}}@endif" autocomplete="off" />
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
            @if (count($inventories) == 0)
                <div class="text-center">{{ __('No data.') }}</div>
            @else
                <div class="table-responsive">
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-inventory-list-table">
                        <thead>
                            <tr>
                                  <th></th>
                                <th>No</th>
                                <th>{{ __('Product') }}</th>
                                <th>{{ __('Sku') }}</th>
                                <th>{{ __('Store') }}</th>
                                <th>{{ __('Incoming') }}</th>
                                <th>{{ __('Available') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventories as $inventory)
                            <tr>
                                 <td>
                                                <input id="{{ $inventory->sku }}" class="form-group" type="checkbox"
                                                    name="label_list[]" value="{{ $inventory->sku . ';' . $inventory->product->name }}">
                                        </td>
                                <td>{{ ($inventories->currentPage() - 1) * $inventories->perPage() + $loop->iteration }}</td>
                                <td>{{ $inventory->product->name }}</td>
                                <td>{{ $inventory->sku }}</td>
                                <td>{{ $inventory->storeFulfill->name ?? '' }}</td>
                                <td>{{ $inventory->incoming }}</td>
                                <td>{{ $inventory->available }}</td>
                                {{-- <td style="text-align: center">{{ $inventory->incoming }}</td> --}}
                                <td>
                                    <a class="btn btn-info" href="{{ route('inventories.show', ['id' => $inventory->id]) }}">Detail</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center justify-content-md-end amt-16">
                    {{ $inventories->appends(request()->all())->links('components.pagination') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
  <script type="text/javascript">
    let products = @php echo json_encode($products) @endphp;

    filterInput(document.getElementById("product-input"), products, 'dropdown-product');
  </script>
@endsection
