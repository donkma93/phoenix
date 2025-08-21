@extends('layouts.app', [
'class' => '',
'folderActive' => '',
'elementActive' => 'inventory',
])

@section('styles')
<style>
    .table-responsive {
        overflow: unset;
    }
    .min-w-160 {
        min-width: 160px;
    }
    .card .card-footer {
        border-top: 1px solid #d8dbe0 !important;
    }
    .card .card-header {
        border-bottom: 1px solid #d8dbe0 !important;
    }
    .form-group {
        margin-bottom: 1rem;
    }
    .search-form-group {
        display: flex;
        flex-direction: column;
    }
    .search-form-group .search-input {
        flex: 1;
    }

    @media (min-width: 576px) {
        .search-form-group {
            flex-direction: row;
        }
        .search-form-group .search-input {
            max-width: 360px;
        }
    }
</style>
@endsection

@section('content')
<div class="content">
<div class="fade-in">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Inventory list</h2>
            <a class="btn btn-success" href="{{ route('inventories.remind') }}">
                {{ __('Config remind') }}
            </a>
                 <div class="search-input text-center text-sm-left">
                        <input type="button" class="btn btn-primary mb-3" value="{{ __('Print') }}" id="submit_form_print">
                    </div>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('inventories.list') }}" class="form-horizontal" role="form">
                <div class="form-group search-form-group">
                    <label class="col-form-label search-label min-w-160 text-left"><b>{{ __('Product') }}</b></label>
                    <div class="search-input">
                        <input type="text" class="form-control" name="product" id="product-input" list="dropdown-product"  value="@if (isset($oldInput['product'])){{$oldInput['product']}}@endif" autocomplete="off" />
                    </div>
                </div>

                <div class="search-form-group">
                    <div class="search-label d-none d-sm-block min-w-160"></div>
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
                    <form action="{{route('inventory.print')}}" method="get" id="form_print" target="_blank">
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-inventory-list-table">
                        <thead>
                            <tr class="text-primary">
                                <th>
                                    <label for="select_all_order"
                                           class="d-flex mb-0 justify-content-center align-items-center"
                                           style="padding: 4px 0px;">
                                        <input id="select_all_order" class="" type="checkbox"
                                               name="" value="">
                                    </label>
                                </th>
                                <th style="text-align: center;">#</th>
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
                                 <td style="text-align: center;">
                                     <input id="{{ $inventory->sku }}" class="form-group select_item_order" type="checkbox"
                                            name="label_list[]" value="{{ $inventory->sku . ';' . $inventory->product->name }}">
                                 </td>
                                <td style="text-align: center;">{{ ($inventories->currentPage() - 1) * $inventories->perPage() + $loop->iteration }}</td>
                                <td>{{ $inventory->product->name }}</td>
                                <td>{{ $inventory->sku }}</td>
                                <td>{{ $inventory->storeFulfill->name ?? '' }}</td>
                                <td>{{ $inventory->incoming }}</td>
                                <td>{{ $inventory->available }}</td>
                                {{-- <td style="text-align: center">{{ $inventory->incoming }}</td> --}}
                                <td style="text-align: center;">
                                    <a class="btn btn-info" href="{{ route('inventories.show', ['id' => $inventory->id]) }}">Detail</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </form>
                </div>
                <div class="d-flex justify-content-center justify-content-md-end amt-16">
                    {{ $inventories->appends(request()->all())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
  <script type="text/javascript">
    let products = @php echo json_encode($products) @endphp;

    /*filterInput(document.getElementById("product-input"), products, 'dropdown-product');*/
    createSuggestBlock(document.getElementById("product-input"), products, 'dropdown-product');

    ///
    $('#submit_form_print').click(function () {
        $('#form_print').submit();
    })
  </script>
@endpush
