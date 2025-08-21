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
            'url' => route('staff.orders.list')
        ],
        [
            'text' => $order['id']
        ],
        [
            'text' => 'Create Label'
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

@php
    $errorData = session('errorData')['request'] ?? null;
@endphp

@section('content')
    <div class="fade-in">
        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">{{ __('Create Order Label') }}</h2>
            </div>

            {{-- Manual --}}
            <div class="card-body" id="manual">
                <div>
                    <form method="POST" action="{{ route('staff.orders.labels.store', ['orderId' => $order['id']]) }}" class="form-horizontal" role="form">
                        @csrf

                        <div>
                            <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                                <h3 class="amb-4">{{ __('Receiver Information') }}</h3>
                            </div>

                            <div class="amb-8 apy-8 addition-form">
                                <div class="row amx-n4">
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Receiver Name') }}</b>
                                        <div class="form-control">
                                            {{ $order['addressTo']['name'] ?? '' }}
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Receiver Company') }}</b>
                                        <div class="form-control">
                                            {{ $order['addressTo']['company'] ?? '' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="amb-8 apy-8 addition-form">
                                <div class="row amx-n4">
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Receiver Country') }}</b>
                                        <div class="form-control">
                                            {{ $order['addressTo']['country'] ?? '' }}
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Receiver Street') }}</b>
                                        <div class="form-control">
                                            {{ $order['addressTo']['street1'] ?? '' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="amb-8 apy-8 addition-form">
                                <div class="row amx-n4">
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Receiver Address 1') }}</b>
                                        <div class="form-control">
                                            {{ $order['addressTo']['street2'] ?? '' }}
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Receiver Address 2') }}</b>
                                        <div class="form-control">
                                            {{ $order['addressTo']['street3'] ?? '' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="amb-8 apy-8 addition-form">
                                <div class="row amx-n4">
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Receiver Province') }}</b>
                                        <div class="form-control">
                                            {{ $order['addressTo']['state'] ?? '' }}
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Receiver City') }}</b>
                                        <div class="form-control">
                                            {{ $order['addressTo']['city'] ?? '' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="amb-8 apy-8 addition-form">
                                <div class="row amx-n4">
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Receiver postal code / zip') }}</b>
                                        <div class="form-control">
                                            {{ $order['addressTo']['zip'] ?? '' }}
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Receiver Phone') }}</b>
                                        <div class="form-control">
                                            {{ $order['addressTo']['phone'] ?? '' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        {{-- Sender --}}
                        <div id="content">
                            <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                                <h3 class="amb-4">{{ __('Sender Information') }}</h3>
                            </div>

                            <div class="amb-8 apy-8 addition-form">
                                <div class="row amx-n4">
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Sender Name *') }}</b>
                                        <input
                                            type="text" class="form-control"
                                            name="shipping_name"
                                            value="{{ $errorData['shipping_name'] ?? old('shipping_name') ?? '' }}"
                                        />
                                    </div>
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Sender Company') }}</b>
                                        <input
                                            type="text" class="form-control"
                                            name="shipping_company"
                                            value="{{ $errorData['shipping_company'] ?? old('shipping_company') ?? '' }}"
                                        />
                                    </div>
                                </div>
                                <div class="row amx-n4 amb-20">
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        @if ($errors->has('shipping_name'))
                                            <div class="col-10 col-xl-8 apx-4">
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('shipping_name') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        @if ($errors->has('shipping_company'))
                                            <div class="col-10 col-xl-8 apx-4">
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('shipping_company') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="amb-8 apy-8 addition-form">
                                <div class="row amx-n4">
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Sender Country *') }}</b>
                                        <input
                                            type="text" class="form-control"
                                            name="shipping_country"
                                            placeholder="Example: 'US' or 'DE'"
                                            value="{{ $errorData['shipping_country'] ?? old('shipping_country') ?? '' }}"
                                        />
                                    </div>
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Sender Street *') }}</b>
                                        <input
                                            type="text" class="form-control"
                                            name="shipping_street"
                                            value="{{ $errorData['shipping_street'] ?? old('shipping_street') ?? '' }}"
                                        />
                                    </div>
                                </div>
                                <div class="row amx-n4 amb-20">
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        @if ($errors->has('shipping_country'))
                                            <div class="col-10 col-xl-8 apx-4">
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('shipping_country') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        @if ($errors->has('shipping_street'))
                                            <div class="col-10 col-xl-8 apx-4">
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('shipping_street') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="amb-8 apy-8 addition-form">
                                <div class="row amx-n4">
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Sender Address 1') }}</b>
                                        <input
                                            type="text" class="form-control"
                                            name="shipping_address1"
                                            value="{{ $errorData['shipping_address1'] ?? old('shipping_address1') ?? '' }}"
                                        />
                                    </div>
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Sender Address 2') }}</b>
                                        <input
                                            type="text" class="form-control"
                                            name="shipping_address2"
                                            value="{{ $errorData['shipping_address2'] ?? old('shipping_address2') ?? '' }}"
                                        />
                                    </div>
                                </div>
                                <div class="row amx-n4 amb-20">
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        @if ($errors->has('shipping_address1'))
                                            <div class="col-10 col-xl-8 apx-4">
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('shipping_address1') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        @if ($errors->has('shipping_address2'))
                                            <div class="col-10 col-xl-8 apx-4">
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('shipping_address2') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="amb-8 apy-8 addition-form">
                                <div class="row amx-n4">
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Sender Province *') }}</b>
                                        <input
                                            type="text" class="form-control"
                                            name="shipping_province"
                                            placeholder="Example: 'CA' or 'NY'"
                                            value="{{ $errorData['shipping_province'] ?? old('shipping_province') ?? '' }}"
                                        />
                                    </div>
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Sender City *') }}</b>
                                        <input
                                            type="text" class="form-control"
                                            name="shipping_city"
                                            placeholder="Example: 'SAN DIEGO'"
                                            value="{{ $errorData['shipping_city'] ?? old('shipping_city') ?? '' }}"
                                        />
                                    </div>
                                </div>
                                <div class="row amx-n4 amb-20">
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        @if ($errors->has('shipping_province'))
                                            <div class="col-10 col-xl-8 apx-4">
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('shipping_province') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        @if ($errors->has('shipping_city'))
                                            <div class="col-10 col-xl-8 apx-4">
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('shipping_city') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="amb-8 apy-8 addition-form">
                                <div class="row amx-n4">
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Sender postal code / zip *') }}</b>
                                        <input
                                            type="text" class="form-control"
                                            name="shipping_zip"
                                            value="{{ old('shipping_zip') ?? '' }}"
                                        />
                                    </div>
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Sender Phone') }}</b>
                                        <input
                                            type="text" class="form-control"
                                            name="shipping_phone"
                                            value="{{ $errorData['shipping_phone'] ?? old('shipping_phone') ?? '' }}"
                                        />
                                    </div>
                                </div>
                                <div class="row amx-n4 amb-20">
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        @if ($errors->has('shipping_zip'))
                                            <div class="col-10 col-xl-8 apx-4">
                                                <p class="text-danger mb-0">
                                                    {{ $errorData['shipping_zip'] ?? $errors->first('shipping_zip') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        @if ($errors->has('shipping_phone'))
                                            <div class="col-10 col-xl-8 apx-4">
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('shipping_phone') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if (session('errorData') !== null)
                                <div class="amb-8 apy-8 addition-form">
                                    <div class="row amx-n4 amb-20">
                                        @foreach (session('errorData')['errorMsg'] as $index => $error)
                                            <p class="text-danger mb-0">
                                                {{ $error }}
                                            </p>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <hr>

                        {{-- Order Package --}}
                        <div id="content">
                            <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                                <h3 class="amb-4">{{ __('Order Package Information') }}</h3>
                            </div>

                            <div class="amb-8 apy-8 addition-form">
                                <div class="row amx-n4">
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Package Length') }}</b>
                                        <input
                                            type="text" class="form-control"
                                            name="package_length"
                                            value="{{ old('package_length', $order->orderPackage->length ?? '') }}"
                                        />
                                    </div>
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Package Width') }}</b>
                                        <input
                                            type="text" class="form-control"
                                            name="package_width"
                                            value="{{ old('package_width', $order->orderPackage->width ?? '') }}"
                                        />
                                    </div>
                                </div>
                                <div class="row amx-n4 amb-20">
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        @if ($errors->has('package_length'))
                                            <div class="col-10 col-xl-8 apx-4">
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('package_length') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        @if ($errors->has('package_width'))
                                            <div class="col-10 col-xl-8 apx-4">
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('package_width') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="amb-8 apy-8 addition-form">
                                <div class="row amx-n4">
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Package Height') }}</b>
                                        <input
                                            type="text" class="form-control"
                                            name="package_height"
                                            value="{{ old('package_height', $order->orderPackage->height ?? '') }}"
                                        />
                                    </div>
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Size Type') }}</b>
                                        <select id="size_type" name="size_type" class="form-control w-75">
                                            @foreach (App\Models\OrderPackage::$sizeName as $value => $name)
                                                <option value="{{ $value }}"
                                                    @if (old('size_type', $order->orderPackage->size_type) === $value)
                                                        selected="selected"
                                                    @endif
                                                >{{ ucfirst($name) }}</option>
                                            @endforeach
                                        </select>
                                        {{-- @if ($errors->has('size_type'))
                                            <p class="text-danger mb-0">
                                                {{ $errors->first('size_type') }}
                                            </p>
                                        @endif --}}
                                    </div>
                                </div>
                                <div class="row amx-n4 amb-20">
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        @if ($errors->has('package_height'))
                                            <div class="col-10 col-xl-8 apx-4">
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('package_height') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        @if ($errors->has('size_type'))
                                            <div class="col-10 col-xl-8 apx-4">
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('size_type') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="amb-8 apy-8 addition-form">
                                <div class="row amx-n4">
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Package Weight') }}</b>
                                        <input
                                            type="text" class="form-control"
                                            name="package_weight"
                                            value="{{ old('package_weight', $order->orderPackage->weight ?? '') }}"
                                        />
                                    </div>
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Weight Type') }}</b>
                                        <select id="weight_type" name="weight_type" class="form-control w-75">
                                            @foreach (App\Models\OrderPackage::$weightName as $value => $name)
                                                <option value="{{ $value }}"
                                                    @if (old('weight_type', $order->orderPackage->weight_type) === $value)
                                                        selected="selected"
                                                    @endif
                                                >{{ ucfirst($name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row amx-n4 amb-20">
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        @if ($errors->has('package_weight'))
                                            <div class="col-10 col-xl-8 apx-4">
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('package_weight') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        @if ($errors->has('weight_type'))
                                            <div class="col-10 col-xl-8 apx-4">
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('weight_type') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="search-form-group">
                            <div class="search-label d-none d-sm-block"></div>
                            <div class="search-input text-center text-sm-left">
                                <input class="btn btn-primary" type="submit" value="{{ __('Create Label') }}">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
    });
</script>
@endsection
