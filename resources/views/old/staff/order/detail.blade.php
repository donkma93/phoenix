@extends('layouts.staff')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('staff.dashboard')
        ],
        [
            'text' => 'Order',
            'url' => route('staff.orders.list')
        ],
        [
            'text' => $order['id']
        ]
    ]
])
@endsection

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('Order  detail') }}</h2>
            @if(!in_array($order->status, [App\Models\Order::STATUS_DONE, App\Models\Order::STATUS_CANCEL])  )
                <div>
                    <div class="btn btn-danger" onclick="updateStatus({{ $order->id }}, 3)">
                        {{ __('Cancel Order') }}
                    </div>
                    <div class="btn btn-success" onclick="updateStatus({{ $order->id }}, {{ $order->status }})">
                        @if($order->status == App\Models\Order::STATUS_NEW)
                            {{ __('Start Order') }}
                        @elseif($order->status == App\Models\Order::STATUS_INPROGRESS)
                            {{ __('Done') }}
                        @endif
                    </div>
                </div>
            @endif
        </div>
        <div class="card-body">

            {{-- Status --}}
            <form method="POST" action="{{ route('staff.orders.updateOrder') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $order->id }}" />

                <div class="row">
                    <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                        <div class="form-group row">
                            <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Email') }}</b></label>
                            <div class="col-8 col-sm-9 form-control border-0">
                                {{ $order->user->email }}
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                        <div class="form-group row">
                            <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Order Number') }}</b></label>
                            <div class="col-8 col-sm-9 form-control border-0">
                                {{ $order->order_number ?? '' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                        <div class="form-group row">
                            <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Order Status') }}</b></label>
                            <div class="col-8 col-sm-9 form-control border-0">
                                {{ ucfirst(App\Models\Order::$statusName[$order->status]) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    @if($order->status === App\Models\Order::STATUS_INPROGRESS)
                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                                <div class="form-group row">
                                    <label class="col-4 col-sm-3 col-form-label"><b>{{ __('State') }}</b></label>
                                    <div class="col-8 col-sm-9">
                                        <select name="state" class="form-control @error('state') is-invalid @enderror">
                                            @foreach (App\Models\Order::$stateName as $key => $state)
                                                <option value="{{ $key }}"
                                                @if($order['state'] == $key)
                                                    selected
                                                @endif>{{ ucfirst($state) }}</option>
                                            @endforeach
                                        </select>
                                        @error('state')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                                <div class="form-group row">
                                    <label class="col-4 col-sm-3 col-form-label"><b>{{ __('State note') }}</b></label>
                                    <div class="col-8 col-sm-9">
                                        <input type="text" class="form-control @error('state_note') is-invalid @enderror" name="state_note" value="{{ isset($order->state_note) ? $order->state_note : '' }}">
                                        @error('state_note')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                                <div class="form-group row">
                                    <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Payment Status') }}</b></label>
                                    <div class="col-8 col-sm-9 ">
                                        <select class="form-control @error('payment') is-invalid @enderror" name="payment">
                                            @foreach (App\Models\Order::$paymentName as $key => $status)
                                                <option value="{{ $key }}"
                                                @if($order['payment'] == $key)
                                                    selected
                                                @endif>{{ ucfirst($status) }}</option>
                                            @endforeach
                                        </select>
                                        @error('payment')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                                <div class="form-group row">
                                    <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Fulfillment Status') }}</b></label>
                                    <div class="col-8 col-sm-9">
                                        <select name="fulfill_name" class="form-control @error('fulfill_name') is-invalid @enderror">
                                            @foreach (App\Models\Order::$fulfillName as $key => $status)
                                                <option value="{{ $key }}"
                                                @if($order['fulfillment'] == $key)
                                                    selected
                                                @endif>{{ ucfirst($status) }}</option>
                                            @endforeach
                                        </select>
                                        @error('fulfill_name')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                                <div class="form-group row">
                                    <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Picking Status') }}</b></label>
                                    <div class="col-8 col-sm-9">
                                        <select name="picking_status" class="form-control @error('picking_status') is-invalid @enderror">
                                            @foreach (App\Models\Order::$pickingName as $key => $status)
                                                <option value="{{ $key }}"
                                                @if($order['picking_status'] == $key)
                                                    selected
                                                @endif>{{ ucfirst($status) }}</option>
                                            @endforeach
                                        </select>
                                        @error('picking_status')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                                <div class="form-group row">
                                    <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Tracking Number') }}</b></label>
                                    <div class="col-8 col-sm-9 ">
                                        <input type="text" class="form-control @error('tracking_number') is-invalid @enderror" name="tracking_number" value="{{ $order->orderTransaction->tracking_number ?? '' }}">
                                        @error('tracking_number')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                                <div class="form-group row">
                                    <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Tracking URL') }}</b></label>
                                    <div class="col-8 col-sm-9 ">
                                        <input type="text" class="form-control @error('tracking_url_provider') is-invalid @enderror" name="tracking_url_provider" value="{{ $order->orderTransaction->tracking_url_provider ?? '' }}">
                                        @error('tracking_url_provider')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                                <div class="form-group row">
                                    <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Ship Provider') }}</b></label>
                                    <div class="col-8 col-sm-9 form-control border-0">
                                        {{ ucfirst($order->orderTransaction->orderRate->provider ?? '') }}
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                                <div class="form-group row">
                                    <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Ship Rate') }}</b></label>
                                    <div class="col-8 col-sm-9">
                                        <input type="text" class="form-control @error('ship_rate') is-invalid @enderror" name="ship_rate" value="{{ isset($order->orderTransaction->orderRate) ? $order->orderTransaction->orderRate->getDisplayRate() : '' }}">
                                        @error('ship_rate')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div> --}}
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                                <input type="submit" class="btn btn-success" value="Update">
                            </div>
                        </div>
                    @else
                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                                <div class="form-group row">
                                    <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Payment Status') }}</b></label>
                                    <div class="col-8 col-sm-9 form-control border-0">
                                        {{ ucfirst(App\Models\Order::$paymentName[$order->payment]) }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                                <div class="form-group row">
                                    <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Fulfillment Status') }}</b></label>
                                    <div class="col-8 col-sm-9 form-control border-0">
                                        {{ ucfirst(App\Models\Order::$fulfillName[$order->fulfillment]) }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                                <div class="form-group row">
                                    <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Tracking Number') }}</b></label>
                                    <div class="col-8 col-sm-9 form-control border-0">
                                        {{ $order->orderTransaction->tracking_number ?? '' }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                                <div class="form-group row">
                                    <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Tracking URL') }}</b></label>
                                    <div class="col-8 col-sm-9 form-control border-0">
                                        {{ $order->orderTransaction->tracking_url_provider ?? '' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                                <div class="form-group row">
                                    <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Ship Provider') }}</b></label>
                                    <div class="col-8 col-sm-9 form-control border-0">
                                        {{ ucfirst($order->orderTransaction->orderRate->provider ?? '') }}
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                                <div class="form-group row">
                                    <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Ship Rate') }}</b></label>
                                    <div class="col-8 col-sm-9 form-control border-0">
                                        {{ isset($order->orderTransaction->orderRate) ? $order->orderTransaction->orderRate->getDisplayRate() : '' }}
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                    @endif
                </div>
            </form>

            <hr>

            {{-- Customer and Item --}}
            <h3 class="amt-32">
                <b>{{ __('Default Item Infomation') }}</b>
            </h3>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Item Name') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->item_name) }}
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Item Quantity') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->item_quantity) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Item Price') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->item_price) }}
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Item Compare At Price') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->item_compare_at_price) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Item SKU') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->item_sku) }}
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Item Requires Shipping') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->item_requires_shipping) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Item Taxable') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->item_taxable) }}
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Item Fulfillment Status') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->item_fulfillment_status) }}
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            {{-- Customer and Item --}}
            <h3 class="amt-32">
                <b>{{ __('Address Infomation') }}</b>
            </h3>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Sender Name') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->addressFrom->name ?? $order->orderTransaction->shipping_name ?? '') }}
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Receiver Name') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->addressTo->name ?? $order->shipping_name ?? '') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Sender Street') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->addressFrom->street1 ?? $order->orderTransaction->shipping_street ?? '') }}
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Receiver Street') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->addressTo->street1 ?? $order->shipping_street ?? '') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Sender Address 1') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->addressFrom->street2 ?? $order->orderTransaction->shipping_address1 ?? '') }}
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Receiver Address 1') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->addressTo->street2 ?? $order->shipping_address1 ?? '') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Sender Address 2') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->addressFrom->street3 ?? $order->orderTransaction->shipping_address2 ?? '') }}
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Receiver Address 2') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->addressTo->street3 ?? $order->shipping_address2 ?? '') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Sender Company') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->addressFrom->company ?? $order->orderTransaction->shipping_company ?? '') }}
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Receiver Company') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->addressTo->company ?? $order->shipping_company ?? '') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Sender City') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->addressFrom->city ?? $order->orderTransaction->shipping_city ?? '') }}
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Receiver City') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->addressTo->city ??  $order->shipping_city ?? '') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Sender Zip') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->addressFrom->zip ?? $order->orderTransaction->shipping_zip ?? '') }}
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Receiver Zip') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->addressTo->zip ?? $order->shipping_zip ?? '') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Sender Province') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->addressFrom->state ?? $order->orderTransaction->shipping_province ?? '') }}
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Receiver Province') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->addressTo->state ?? $order->shipping_province ?? '') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Sender Country') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->addressFrom->country ?? $order->orderTransaction->shipping_country ?? '') }}
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Receiver Country') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->addressTo->country ?? $order->shipping_country ?? '') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Sender Phone') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->addressFrom->phone ?? $order->orderTransaction->shipping_phone ?? '') }}
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Receiver Phone') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->addressTo->phone ?? $order->shipping_phone ?? '') }}
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            {{-- Product --}}
            <h3 class="amt-32">
                <b>{{ __('Product Infomation') }}</b>
            </h3>

            @foreach ($order->orderProducts as $index => $orderProduct)
                <div class="ap-24">
                    <div class="row amx-n16 amb-8">
                        <b>{{ $orderProduct->product->name }}</b>
                    </div>

                    <div class="row amx-n16 amb-8">
                        <div class="rq-pkg-field apx-16">
                            <b>{{ __('Product Status:') }}</b>
                        </div>
                        <div class="col apx-16">{{ ucfirst(App\Models\Product::$statusName[$orderProduct->product->status]) }}</div>
                    </div>

                    <div class="row amx-n16 amb-8">
                        <div class="rq-pkg-field apx-16">
                            <b>{{ __('Category:') }}</b>
                        </div>
                        <div class="col apx-16">{{ $orderProduct->product->category->name ?? '' }}</div>
                    </div>

                    <div class="row amx-n16 amb-8">
                        <div class="rq-pkg-field apx-16">
                            <b>{{ __('Quantity:') }}</b>
                        </div>
                        <div class="col apx-16">{{ $orderProduct->quantity }}</div>
                    </div>

                    <div class="row amx-n16 amb-8">
                        <div class="rq-pkg-field apx-16">
                            <b>{{ __('Product Fulfillment Fee:') }}</b>
                        </div>
                        <div class="col apx-16">{{ $orderProduct->product->fulfillment_fee }}</div>
                    </div>

                    <div class="row amx-n16 amb-8">
                        <div class="rq-pkg-field apx-16">
                            <b>{{ __('Extra Pick Fee:') }}</b>
                        </div>
                        <div class="col apx-16">{{ $orderProduct->product->extra_pick_fee }}</div>
                    </div>

                    <div class="row amx-n16 amb-8">
                        <div class="rq-pkg-field apx-16">
                            <b>{{ __('Total Fee:') }}</b>
                        </div>
                        <div class="col apx-16">{{ $orderProduct->total_fee }}</div>
                    </div>

                    <div class="row amx-n16 amb-8">
                        <div class="rq-pkg-field apx-16">
                            <b>{{ __('Image') }}</b>
                        </div>
                        @if(!isset($orderProduct->product->image_url))
                            <div class="col apx-16">{{ __('No image') }}</div>
                        @else
                            <div class="col apx-16">
                                <img id="image-upload" width="300" height="300" src="{{ asset($orderProduct->product->image_url) }}" alt="Product image" class="img-fluid">
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            <hr>

            {{-- Package --}}
            <h3 class="amt-32">
                <b>{{ __('Package Infomation') }}</b>
            </h3>

            @php
                $sizeType = $order->orderPackage->size_type ? ucfirst(App\Models\OrderPackage::$sizeName[$order->orderPackage->size_type]) : '';
                $weightType = $order->orderPackage->weight_type ? ucfirst(App\Models\OrderPackage::$weightName[$order->orderPackage->weight_type]) : '';

                $length = $order->orderPackage->length ? $order->orderPackage->length . ' ' . $sizeType : '';
                $width = $order->orderPackage->width ? $order->orderPackage->width . ' ' . $sizeType : '';
                $height = $order->orderPackage->height ? $order->orderPackage->height . ' ' . $sizeType : '';
                $weight = $order->orderPackage->weight ? $order->orderPackage->weight . ' ' . $weightType : '';
            @endphp

            <div class="ap-24">
                @if($order->status !== App\Models\Order::STATUS_INPROGRESS)
                    <div class="row amx-n16 amb-8">
                        <div class="rq-pkg-field apx-16">
                            <b>{{ __('Length:') }}</b>
                        </div>
                        <div class="col apx-16">{{ $length }}</div>
                    </div>

                    <div class="row amx-n16 amb-8">
                        <div class="rq-pkg-field apx-16">
                            <b>{{ __('Width:') }}</b>
                        </div>
                        <div class="col apx-16">{{ $width }}</div>
                    </div>

                    <div class="row amx-n16 amb-8">
                        <div class="rq-pkg-field apx-16">
                            <b>{{ __('Height:') }}</b>
                        </div>
                        <div class="col apx-16">{{ $height }}</div>
                    </div>

                    <div class="row amx-n16 amb-8">
                        <div class="rq-pkg-field apx-16">
                            <b>{{ __('Weight:') }}</b>
                        </div>
                        <div class="col apx-16">{{ $weight }}</div>
                    </div>
                @else
                    <form method="POST" action="{{ route('staff.orders.updatePackage') }}" enctype="multipart/form-data">
                    @csrf
                        <input type="hidden" name="id" value="{{ $order->id }}" />
                        <div class="row amx-n16 amb-8">
                            <div class="rq-pkg-field apx-16">
                                <b>{{ __('Length:') }}</b>
                            </div>
                            <div class="col apx-16">
                                <input type="text" name="length" class="form-control @error('length') is-invalid @enderror" value="{{ $order->orderPackage->length }}">
                                @error('length')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row amx-n16 amb-8">
                            <div class="rq-pkg-field apx-16">
                                <b>{{ __('Width:') }}</b>
                            </div>
                            <div class="col apx-16">
                                <input type="text" name="width" class="form-control @error('width') is-invalid @enderror" value="{{ $order->orderPackage->width }}">
                                @error('width')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row amx-n16 amb-8">
                            <div class="rq-pkg-field apx-16">
                                <b>{{ __('Height:') }}</b>
                            </div>
                            <div class="col apx-16">
                                <input type="text" name="height" class="form-control @error('height') is-invalid @enderror" value="{{ $order->orderPackage->height }}">
                                @error('height')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row amx-n16 amb-8">
                            <div class="rq-pkg-field apx-16">
                                <b>{{ __('Weight:') }}</b>
                            </div>
                            <div class="col apx-16">
                                <input type="text" name="weight" class="form-control @error('weight') is-invalid @enderror" value="{{ $order->orderPackage->weight }}">
                                @error('weight')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                                <input type="submit" class="btn btn-success" value="Update package">
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function updateStatus(id, status) {
        $.ajax({
            type: 'POST',
            url: "{{ route('staff.orders.updateStatus') }}",
            data: {
                id,
                status,
                _token: '{{csrf_token()}}'
            },
            success:function() {
                window.location.reload();
            },
            error: function() {
                alert('Something wrong! Please contact admin for more information!')
            }
        });
    }
</script>
@endsection
