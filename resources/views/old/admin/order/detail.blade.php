@extends('layouts.admin')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('admin.dashboard')
        ],
        [
            'text' => 'Order',
            'url' => route('admin.orders.list')
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

                <div class="row">
                    <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                        <div class="form-group row">
                            <label class="col-4 col-sm-3 col-form-label"><b>{{ __('State') }}</b></label>
                            <div class="col-8 col-sm-9 form-control border-0">
                                {{ ucfirst(App\Models\Order::$stateName[$order->state]) }}
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                        <div class="form-group row">
                            <label class="col-4 col-sm-3 col-form-label"><b>{{ __('State note') }}</b></label>
                            <div class="col-8 col-sm-9 form-control border-0">
                                {{ $order->state_note }}
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                        <div class="form-group row">
                            <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Payment Status') }}</b></label>
                            <div class="col-8 col-sm-9 form-control border-0">
                                {{ ucfirst(App\Models\Order::$paymentName[$order->payment]) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
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
                            <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Picking Status') }}</b></label>
                            <div class="col-8 col-sm-9 form-control border-0">
                                {{ ucfirst(App\Models\Order::$pickingName[$order->picking_status]) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                        <div class="form-group row">
                            <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Ship Rate') }}</b></label>
                            <div class="col-8 col-sm-9 form-control border-0">
                                {{ isset($order->orderTransaction->orderRate) ? $order->orderTransaction->orderRate->getDisplayRate() ?? round($order->ship_rate, 2) : '' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                        <div class="form-group row">
                            <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Tracking') }}</b></label>
                            <div class="col-8 col-sm-9 form-control border-0">
                                {{ $order->tracking }}
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                        <div class="form-group row">
                            <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Date') }}</b></label>
                            <div class="col-8 col-sm-9 form-control border-0">
                                {{ $order->date ?? $order->created_at }}
                            </div>
                        </div>
                    </div>
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
            </div>
        </div>
    </div>
</div>
@endsection
