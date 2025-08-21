@extends('layouts.app', [
'class' => '',
'folderActive' => '',
'elementActive' => 'epacket',
])

@section('styles')
<style>
    .table-responsive {
        overflow: unset;
    }
    .min-w-160 {
        min-width: 160px;
    }
    .card .card-header {
        border-bottom: 1px solid #d8dbe0;
    }
    table th {
        padding-right: 7px !important;
    }
</style>
@endsection

@if (session('success'))
    @section('flash')
        @include('layouts.partials.flash', [
            'messages' => [
                [
                    'content' => session('success'),
                ],
            ],
        ])
    @endsection
@endif

@if (session('fail'))
    @section('flash')
        @include('layouts.partials.flash', [
            'messages' => [
                [
                    'content' => session('fail'),
                    'type' => 'error',
                ],
            ],
        ])
    @endsection
@endif

@section('content')
<div class="content">
    <div class="fade-in">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h2 class="mb-0">{{ __('Create Pickup Request') }}</h2>
                      <div class="search-input text-center text-sm-left mb-6">
                        <a class="btn btn-primary mb-3" href={{ route('pickup.index') }}>{{ __('Back') }}</a>
                    </div>
            </div>

            <div class="card-body" id="manual">
                <div>

                    @if (count($orders) == 0)
                        <div class="text-center">{{ __('No data.') }}</div>
                    @else
                        <form method="post" action="{{ route('pickup.store') }}">
                            @csrf

                            <div class="table-responsive">
                                <table class="table datatable"
                                    id="staff-package-table">
                                    <thead>
                                        <tr class="text-primary">
                                            <th class="disabled-sorting">
                                                <label for="select_all_order"
                                                       class="d-flex mb-0 justify-content-center align-items-center"
                                                       style="padding: 4px 0px;">
                                                    <input id="select_all_order" class="" type="checkbox"
                                                           name="" value="">
                                                </label>
                                            </th>
                                            <th>{{ __('Order Code') }}</th>
                                            <th>{{ __("Customer's Order") }}</th>
                                            {{--<th>{{ __('Customer') }}</th>--}}
                                            <th>{{ __('Receiver Name') }}</th>
                                            {{--<th>{{ __('Info') }}</th>--}}
                                            {{--<th>{{ __('KG') }}</th>--}}
                                            <th>{{ __('Date') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($orders as $order)
                                            <tr>
                                                <td style="text-align: center;">
                                                    <input id="{{ $order->id }}" class="select_item_order" type="checkbox"
                                                        name="order_ids[]" value="{{ $order->id }}">
                                                </td>
                                                <td>
                                                    {{ $order->order_code }}
                                                </td>
                                                <td>
                                                    {{ $order->order_number }}
                                                </td>
                                                {{--<td style="">{{ $order->partner_code }}</td>--}}
                                                <td style="">{{ $order->addressTo->name }}</td>
                                                {{--<td>
                                                    <div><b>Name: </b>{{ $order->addressTo->name ?? $order->shipping_name ?? '' }}</div>
                                                    <div><b>Street: </b>{{ $order->addressTo->street1 ?? $order->shipping_street ?? '' }}</div>
                                                    <div><b>Address1: </b>{{ $order->addressTo->street2 ?? $order->shipping_address1 ?? '' }}</div>
                                                    <div><b>Address2: </b>{{ $order->addressTo->street3 ?? $order->shipping_address2 ?? '' }}</div>
                                                    <div><b>Company: </b>{{ $order->addressTo->company ?? $order->shipping_company ?? '' }}</div>
                                                    <div><b>City: </b>{{ $order->addressTo->city ??  $order->shipping_city ?? '' }}</div>
                                                    <div><b>Zip: </b>{{ $order->addressTo->zip ?? $order->shipping_zip ?? '' }}</div>
                                                    <div><b>Province: </b>{{ $order->addressTo->state ?? $order->shipping_province ?? '' }}</div>
                                                    <div><b>Country: </b>{{ $order->addressTo->country ?? $order->shipping_country ?? '' }}</div>
                                                    <div><b>Phone: </b>{{ $order->addressTo->phone ?? $order->shipping_phone ?? '' }}</div>
                                                    <div><b>WxHxD: </b>{{ $order->orderPackage->width.'x'.$order->orderPackage->height .'x'.$order->orderPackage->length}}</div>
                                                </td>--}}
                                                {{--<td style="font-size:16px;"><b>{{ isset($order->orderPackage) ? $order->orderPackage->weight : '' }}</b></td>--}}
                                                <td>{{ $order->created_at }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="search-form-group">
                                <div class="search-input text-center text-sm-left">
                                    <input class="btn btn-primary" type="submit" value="{{ __('Create') }}">
                                </div>
                            </div>
                        </form>

                        {{--<div class="d-flex justify-content-center justify-content-md-end amt-16">
                            {{ $orders->appends(request()->all())->links() }}
                        </div>--}}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Datatable
            $('.datatable').DataTable({
                "pagingType": "full_numbers",
                "lengthMenu": [
                    // [10, 25, 50, -1],
                    // [10, 25, 50, "All"]
                    [200, 150, 100, 50],
                    [200, 150, 100, 50]
                ],
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search records",
                }
            });
        })
    </script>
@endpush
