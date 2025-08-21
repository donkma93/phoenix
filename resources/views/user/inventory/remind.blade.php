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
            <h2 class="mb-0">{{ __('Config reorder remind') }}</h2>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('inventories.remind') }}" class="form-horizontal" role="form">
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

        @if (count($inventories) == 0)
            <div class="text-center">No data.</div>
        @else
            <form method="POST" action="{{ route('inventories.updateListRemind') }}" class="form-horizontal" role="form">
            @csrf
                <div class="card-footer">
                    <div class="table-responsive">
                        <table class="table table-align-middle table-bordered table-striped table-sm" id="inventory-remind-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('Number for remind') }}</th>
                                    <th>
                                        <label for="select_all_order"
                                               class="d-flex mb-0 justify-content-center align-items-center"
                                               style="padding: 4px 0px;">
                                            {{ __('Remind') }}

                                            {{--<input id="select_all_order" class="" type="checkbox">--}}
                                        </label>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inventories as $inventory)
                                <input type="hidden" name="inventories[{{$loop->iteration}}][id]"  value="{{ $inventory->id }}" />
                                <tr>
                                    <td>{{ ($inventories->currentPage() - 1) * $inventories->perPage() + $loop->iteration }}</td>
                                    <td>{{ $inventory->product->name }}</td>
                                    <td><input type="number" name="inventories[{{$loop->iteration}}][min]"  value="{{ $inventory->min }}" class="form-control w-100" /></td>
                                    <td style="text-align: center;">
                                        <input type="checkbox" name="inventories[{{$loop->iteration}}][is_remind]" id="is_remind" class="select_item_order" {{ old('is_remind') || $inventory->is_remind ? 'checked' : '' }}>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center justify-content-md-end amt-16">
                        {{ $inventories->appends(request()->all())->links() }}
                    </div>

                    <div class="search-form-group">
                        <input class="btn btn-block btn-success" type="submit" value="{{ __('Save') }}">
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>
</div>
@endsection
