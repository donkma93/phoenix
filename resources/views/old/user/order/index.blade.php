@extends('layouts.user')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('dashboard')
        ],
        [
            'text' => 'Order',
            'url' => route('orders.index')
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
@php
    $fields = [__('No'), __('Order Number'), __('Recipient Information'), __('Product Information'), __('Package Information'), __('Order Status'), __('Payment Status'), __('Fulfillment Status'), __('Tracking'), __('Files'),  ''];
@endphp

<div class="fade-in">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('Order list') }}</h2>
            <a class="btn btn-success" href="{{ route('orders.create') }}">
                {{ __('New Order') }}
            </a>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('orders.index') }}" class="form-horizontal" role="form">
                {{-- <div class="form-group search-form-group">
                    <label for="name" class="col-form-label search-label"><b>{{ __('Name') }}</b></label>
                    <div class="search-input">
                        <input type="input" class="form-control w-100" name="name" value="@if (isset($oldInput['name'])){{$oldInput['name']}}@endif" />
                    </div>
                </div> --}}

                <div class="form-group search-form-group">
                    <label for="status" class="col-form-label search-label"><b>{{ __('Type') }}</b></label>
                    <div class="search-input">
                        <select id="status" name="status" class="form-control w-100">
                            <option selected></option>
                            @foreach (App\Models\Order::$statusName as $value => $status)
                                <option value="{{ $value }}"
                                    @if (isset($oldInput['status']) && $oldInput['status'] == $value)
                                        selected="selected"
                                    @endif
                                >{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="payment" class="col-form-label search-label"><b>{{ __('Payment Status') }}</b></label>
                    <div class="search-input">
                        <select id="payment" name="payment" class="form-control w-100">
                            <option selected></option>
                            @foreach (App\Models\Order::$paymentName as $value => $status)
                                <option value="{{ $value }}"
                                    @if (isset($oldInput['payment']) && $oldInput['payment'] == $value)
                                        selected="selected"
                                    @endif
                                >{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="fulfillment" class="col-form-label search-label"><b>{{ __('Fulfillment Status') }}</b></label>
                    <div class="search-input">
                        <select id="fulfillment" name="fulfillment" class="form-control w-100">
                            <option selected></option>
                            @foreach (App\Models\Order::$fulfillName as $value => $status)
                                <option value="{{ $value }}"
                                    @if (isset($oldInput['fulfillment']) && $oldInput['fulfillment'] == $value)
                                        selected="selected"
                                    @endif
                                >{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
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
            @if (count($orders))
                <div class="table-responsive">
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="user-package-table">
                        <thead>
                            <tr>
                                @foreach ($fields as $field)
                                    <th>{{ $field }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td>{{ ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration }}</td>
                                    <td>
                                        {{ $order->order_number ?? '' }}
                                    </td>
                                    <td>
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
                                    </td>
                                    <td style="text-align: left">
                                        @php
                                            $sizeType = $order->orderPackage->size_type ? ucfirst(App\Models\OrderPackage::$sizeName[$order->orderPackage->size_type]) : '';
                                            $weightType = $order->orderPackage->weight_type ? ucfirst(App\Models\OrderPackage::$weightName[$order->orderPackage->weight_type]) : '';

                                            $length = $order->orderPackage->length ? $order->orderPackage->length . ' ' . $sizeType : '';
                                            $width = $order->orderPackage->width ? $order->orderPackage->width . ' ' . $sizeType : '';
                                            $height = $order->orderPackage->height ? $order->orderPackage->height . ' ' . $sizeType : '';
                                            $weight = $order->orderPackage->weight ? $order->orderPackage->weight . ' ' . $weightType : '';
                                        @endphp
                                        <div><b>Length: </b>{{ $length }}</div>
                                        <div><b>Width: </b>{{ $width }}</div>
                                        <div><b>Height: </b>{{ $height }}</div>
                                        <div><b>Weight: </b>{{ $weight }}</div>
                                    </td>
                                    <td style="text-align: left">
                                        @foreach ($order->orderProducts as $index => $orderProduct)
                                            @if($index)
                                                <hr>
                                            @endif
                                            <div><b>Name: </b>{{ $orderProduct->product->name }}</div>
                                            <div><b>Quantity: </b>{{ $orderProduct->quantity }}</div>
                                            @if(isset($orderProduct->product->image_url))
                                                <div>
                                                    <img id="image-upload" width="100" src="{{ asset($orderProduct->product->image_url) }}" alt="Product image" class="img-fluid">
                                                </div>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td style="text-align: center">{{ ucfirst(App\Models\Order::$statusName[$order->status]) }}</td>
                                    <td style="text-align: center">{{ ucfirst(App\Models\Order::$paymentName[$order->payment]) }}</td>
                                    <td style="text-align: center">{{ ucfirst(App\Models\Order::$fulfillName[$order->fulfillment]) }}</td>
                                    {{-- <td style="text-align: center">{{ isset($order->orderTransaction->orderRate) ? $order->orderTransaction->orderRate->getDisplayRate() : '' }} {{ $order->orderTransaction->orderRate->currency ?? '' }}</td> --}}
                                    <td style="text-align: left">
                                        <div>{{ isset($order->orderTransaction->orderRate->provider) ?  $order->orderTransaction->orderRate->provider . ' Tracking' :  '' }}</div>
                                        <div>{{ $order->orderTransaction->tracking_number ?? '' }}</div>
                                    </td>
                                    <td>

                                    @if (is_null($order->file_urls))
                                             <div>No file</div>
                                        @else
                                          <a href="{{ route('orders.show', ['id' => $order->id ]) }}">
                                             <div
                                           style="font-weight: bold; color:#0015ef"
                                           >{{count(explode(',', $order->file_urls))}} files</div>
                                        </a>
                                         
                                    @endif
                                    </td>
                                    <td>
                                        <a class="btn btn-block btn-info" href="{{ route('orders.show', ['id' => $order->id ]) }}">
                                            {{ __('Detail') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center justify-content-md-end amt-16">
                    {{ $orders->appends(request()->all())->links('components.pagination') }}
                </div>
            @else
                <div class="text-center">{{ __('No data.') }}</div>
            @endif
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-body" id="preview-barcode">
        </div>
    </div>
    </div>
</div>
@endsection

@section('scripts')
<script>

</script>
@endsection
