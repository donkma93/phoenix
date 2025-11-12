@extends('layouts.app', [
    'class' => '',
    'folderActive' => 'order_management',
    'elementActive' => 'orders',
])

@section('styles')
    <style>
        .card .card-header {
            padding: 0.75rem 1.25rem;
            margin-bottom: 0;
            border-bottom: 1px solid;
            background-color: #fff;
            border-color: #d8dbe0;
        }

        .search-form-group {
            display: flex;
            align-items: center;
        }

        .search-form-group .search-label {
            min-width: 160px;
        }

        .form-horizontal .col-form-label {
            padding-top: calc(.375rem + 1px);
            padding-bottom: calc(.375rem + 1px);
            padding-left: 0;
            padding-right: 0;
            text-align: left;
            margin-bottom: 0;
            font-size: inherit;
            line-height: 1.5;
        }

        .pointer {
            cursor: pointer !important;
        }

        i.pointer {
            padding: 8px;
        }

        .form-control {
            height: calc(1.5em + 1rem + 5px) !important;
            padding: 0.625rem 0.75rem !important;
        }

        .min-w-160 {
            min-width: 160px !important;
        }

        .fileinput .thumbnail {
            max-width: unset;
            margin-bottom: 0 !important;
            box-shadow: unset;
        }

        @media (min-width: 1200px) {
            .modal-dialog {
                max-width: 800px;
            }
        }

        /*CSS for select2*/
        .select2-container--default .select2-selection--single {
            height: calc(1.5em + 1rem + 5px) !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: unset !important;
            padding-top: 0.625rem !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: unset !important;
            top: 1.25rem !important;
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

@php
    $errorData = session('errorData')['request'] ?? null;
@endphp

@section('content')
    <div class="content">
        @if (session('success'))
        <div class="row justify-content-end">
            <div class="col-md-4">
                <div class="alert alert-success alert-dismissible fade show">
                    <button type="button" aria-hidden="true" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="nc-icon nc-simple-remove"></i>
                    </button>
                    <span>
                            <b> Success - </b>
                            {{ session('success') }}
                        </span>
                </div>
            </div>
        </div>
        @endif
        @if (session('error'))
        <div class="row justify-content-end">
            <div class="col-md-4">
                <div class="alert alert-danger alert-dismissible fade show">
                    <button type="button" aria-hidden="true" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="nc-icon nc-simple-remove"></i>
                    </button>
                    <span>
                            <b> Error - </b>
                            {{ session('error') }}
                        </span>
                </div>
            </div>
        </div>
        @endif
        <div class="fade-in">
            <div class="card px-4 py-2">
                <div class="card-header">
                    <h2 class="mb-0">{{ __('Create Order Label') }}</h2>
                </div>

                {{-- Manual --}}
                <div class="card-body" id="manual">
                    <div>
                        <form method="POST" action="{{ route('staff.orders.labels.store', ['orderId' => $order['id']]) }}"
                            class="form-horizontal" role="form" id="create_label_form">
                            @csrf

                            <div>
                                <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                                    <h4 class="btn btn-primary amb-4 ">{{ __('Receiver Information') }}</h4>
                                </div>

                                <div class="amb-8 apy-8 addition-form mt-3">
                                    <div class="row amx-n1">
                                        <div class="col-12 col-md-5 col-xl-4 apx-1 ">
                                            <input type="hidden" class="form-control" name="receiver_name"
                                                value="{{ $order['addressTo']['name'] ?? '' }}" />
                                            <input type="hidden" class="form-control" name="item_name"
                                                value="{{ $order->item_name ?? ($order->orderProducts[0]->product->name ?? '') }}" />
                                            <input type="hidden" class="form-control" name="order_id"
                                                value="{{ $order->id ?? '' }}" />
                                            <input type="hidden" class="form-control" name="order_code"
                                                   value="{{ $order->order_code ?? '' }}" />
                                            <b>{{ __('Receiver Name') }}</b>
                                            <p class="m-0">
                                                {{ $order['addressTo']['name'] ?? '' }}
                                            </p>
                                        </div>
                                        <div class="col-12 col-md-5 col-xl-4 apx-1 ">
                                            <input type="hidden" class="form-control" name="receiver_company"
                                                value="{{ $order['addressTo']['company'] ?? '' }}" />
                                            <b>{{ __('Receiver Company') }}</b>
                                            <p class="m-0">
                                                {{ $order['addressTo']['company'] ?? '' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="amb-8 apy-8 addition-form mt-3">
                                    <div class="row amx-n4">
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                            <input type="hidden" class="form-control" name="receiver_country"
                                                value="{{ $order['addressTo']['country'] ?? '' }}" />
                                            <b>{{ __('Receiver Country') }}</b>
                                            <p class="m-0">
                                                {{ $order['addressTo']['country'] ?? '' }}
                                            </p>
                                        </div>
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                            <input type="hidden" class="form-control" name="receiver_street"
                                                value="{{ $order['addressTo']['street1'] ?? '' }}" />
                                            <b>{{ __('Receiver Street') }}</b>
                                            <p class="m-0">
                                                {{ $order['addressTo']['street1'] ?? '' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="amb-8 apy-8 addition-form mt-3">
                                    <div class="row amx-n4">
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                            <input type="hidden" class="form-control" name="receiver_address1"
                                                value="{{ $order['addressTo']['street2'] ?? '' }}" />
                                            <b>{{ __('Receiver Address 1') }}</b>
                                            <p class="m-0">
                                                {{ $order['addressTo']['street2'] ?? '' }}
                                            </p>
                                        </div>
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                            <input type="hidden" class="form-control" name="receiver_address2"
                                                value="{{ $order['addressTo']['street3'] ?? '' }}" />
                                            <b>{{ __('Receiver Address 2') }}</b>
                                            <p class="m-0">
                                                {{ $order['addressTo']['street3'] ?? '' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="amb-8 apy-8 addition-form mt-3">
                                    <div class="row amx-n4">
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                            <input type="hidden" class="form-control" name="receiver_province"
                                                value="{{ $order['addressTo']['state'] ?? '' }}" />
                                            <b>{{ __('Receiver Province') }}</b>
                                            <p class="m-0">
                                                {{ $order['addressTo']['state'] ?? '' }}
                                            </p>
                                        </div>
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                            <input type="hidden" class="form-control" name="receiver_city"
                                                value="{{ $order['addressTo']['city'] ?? '' }}" />
                                            <b>{{ __('Receiver City') }}</b>
                                            <p class="m-0">
                                                {{ $order['addressTo']['city'] ?? '' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="amb-8 apy-8 addition-form mt-3">
                                    <div class="row amx-n4">
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                            <input type="hidden" class="form-control" name="receiver_zip"
                                                value="{{ $order['addressTo']['zip'] ?? '' }}" />
                                            <b>{{ __('Receiver postal code / zip') }}</b>
                                            <p class="m-0">
                                                {{ $order['addressTo']['zip'] ?? '' }}
                                            </p>
                                        </div>
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                            <input type="hidden" class="form-control" name="receiver_phone"
                                                value="{{ $order['addressTo']['phone'] ?? '' }}" />
                                            <b>{{ __('Receiver Phone') }}</b>
                                            <p class="m-0">
                                                {{ $order['addressTo']['phone'] ?? '' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <br>

                            {{-- Sender --}}
                            <div id="content">
                                <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                                    <h4 class="amb-4 btn btn-primary">{{ __('Sender Information') }}</h4>
                                </div>

                                <div class="amb-8 apy-8 addition-form mt-3">
                                    <div class="row amx-n4">
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8 form-group">
                                            <b>{{ __('Sender Name *') }}</b>
                                            <input type="text" class="form-control" name="shipping_name"
                                                id="shipping_name"
                                                value="{{ $errorData['shipping_name'] ?? (old('shipping_name') ?? '') }}" />
                                            <span class="form_message"></span>
                                        </div>
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8 form-group">
                                            <b>{{ __('Sender Company') }}</b>
                                            <input type="text" class="form-control" name="shipping_company"
                                                id="shipping_company"
                                                value="{{ $errorData['shipping_company'] ?? (old('shipping_company') ?? '') }}" />
                                            <span class="form_message"></span>
                                        </div>
                                    </div>
                                    <div class="row amx-n4 amb-20 mt-1">
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

                                <div class="amb-8 apy-8 addition-form mt-3">
                                    <div class="row amx-n4">
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8 form-group">
                                            <b>{{ __('Sender Country *') }}</b>
                                            {{--<input type="text" class="form-control" name="shipping_country"
                                                id="shipping_country" placeholder="Example: 'US' or 'DE'"
                                                value="{{ $errorData['shipping_country'] ?? (old('shipping_country') ?? '') }}" />--}}

                                            <select name="shipping_country" id="shipping_country" class="form-control">
                                                <option value="">Select Country</option>
                                                @foreach($countries as $country)
                                                    <option data-id="{{ $country->id }}" value="{{ strtoupper($country->code) }}"
                                                        {{ old('shipping_country') !== null && strtoupper(old('shipping_country')) == strtoupper($country->code) ? 'selected' : '' }}
                                                    >
                                                        {{ $country->name . ' [' . $country->code . ']' }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            <span class="form_message"></span>
                                        </div>
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8 form-group">
                                            <b>{{ __('Sender Street *') }}</b>
                                            <input type="text" class="form-control" name="shipping_street"
                                                id="shipping_street"
                                                value="{{ $errorData['shipping_street'] ?? (old('shipping_street') ?? '') }}" />
                                            <span class="form_message"></span>
                                        </div>
                                    </div>
                                    <div class="row amx-n4 amb-20 mt-1">
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                            @if ($errors->has('shipping_country'))
                                                <div class="">
                                                    <p class="text-danger mb-0">
                                                        {{ $errors->first('shipping_country') }}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                            @if ($errors->has('shipping_street'))
                                                <div class="">
                                                    <p class="text-danger mb-0">
                                                        {{ $errors->first('shipping_street') }}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="amb-8 apy-8 addition-form mt-3">
                                    <div class="row amx-n4">
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8 form-group">
                                            <b>{{ __('Sender Address 1') }}</b>
                                            <input type="text" class="form-control" name="shipping_address1"
                                                id="shipping_address1"
                                                value="{{ $errorData['shipping_address1'] ?? (old('shipping_address1') ?? '') }}" />
                                            <span class="form_message"></span>
                                        </div>
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8 form-group">
                                            <b>{{ __('Sender Address 2') }}</b>
                                            <input type="text" class="form-control" name="shipping_address2"
                                                id="shipping_address2"
                                                value="{{ $errorData['shipping_address2'] ?? (old('shipping_address2') ?? '') }}" />
                                            <span class="form_message"></span>
                                        </div>
                                    </div>
                                    <div class="row amx-n4 amb-20 mt-1">
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                            @if ($errors->has('shipping_address1'))
                                                <div class="">
                                                    <p class="text-danger mb-0">
                                                        {{ $errors->first('shipping_address1') }}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                            @if ($errors->has('shipping_address2'))
                                                <div class="">
                                                    <p class="text-danger mb-0">
                                                        {{ $errors->first('shipping_address2') }}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="amb-8 apy-8 addition-form mt-3">
                                    <div class="row amx-n4">
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8 form-group">
                                            <b>{{ __('Sender Province *') }}</b>
                                            {{--<input type="text" class="form-control" name="shipping_province"
                                                id="shipping_province" placeholder="Example: 'CA' or 'NY'"
                                                value="{{ $errorData['shipping_province'] ?? (old('shipping_province') ?? '') }}" />--}}

                                            <select name="shipping_province" id="shipping_province" class="form-control">
                                                <option value="">Select State/Province</option>
                                                @foreach($states as $state)
                                                    <option data-id="{{ $state->id }}" value="{{ strtoupper($state->state_code) }}"
                                                        {{ old('shipping_province') !== null && strtoupper(old('shipping_province')) == strtoupper($state->state_code) ? 'selected' : '' }}
                                                    >
                                                        {{ $state->name . ' [' . $state->state_code . ']' }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            <span class="form_message"></span>
                                        </div>
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8 form-group">
                                            <b>{{ __('Sender City *') }}</b>
                                            <input type="text" class="form-control" name="shipping_city"
                                                id="shipping_city" placeholder="Example: 'SAN DIEGO'"
                                                value="{{ $errorData['shipping_city'] ?? (old('shipping_city') ?? '') }}" />

                                            {{--<select name="shipping_city" id="shipping_city" class="form-control">
                                                <option value="">Select City</option>
                                                @foreach($cities as $city)
                                                    <option data-id="{{ $city->id }}" value="{{ strtoupper($city->name) }}"
                                                        {{ old('shipping_city') !== null && strtoupper(old('shipping_city')) == strtoupper($city->name) ? 'selected' : '' }}
                                                    >
                                                        {{ $city->name }}
                                                    </option>
                                                @endforeach
                                            </select>--}}

                                            <span class="form_message"></span>
                                        </div>
                                    </div>
                                    <div class="row amx-n4 amb-20 mt-1">
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

                                <div class="amb-8 apy-8 addition-form mt-3">
                                    <div class="row amx-n4">
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8 form-group">
                                            <b>{{ __('Sender postal code / zip *') }}</b>
                                            <input type="text" class="form-control" name="shipping_zip"
                                                id="shipping_zip" value="{{ old('shipping_zip') ?? '' }}" />
                                            <span class="form_message"></span>
                                        </div>
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8 form-group">
                                            <b>{{ __('Sender Phone') }}</b>
                                            <input type="text" class="form-control" name="shipping_phone"
                                                id="shipping_phone"
                                                value="{{ $errorData['shipping_phone'] ?? (old('shipping_phone') ?? '') }}" />
                                            <span class="form_message"></span>
                                        </div>
                                    </div>
                                    <div class="row amx-n4 amb-20 mt-1">
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



                                    {{--
                                    <div style="display:block;">
                                        <hr>
                                        <div class="row amx-n4">
                                            <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8 form-group">
                                                <b>{{ __('Tracking Number *') }}</b>
                                                <input type="text" class="form-control" name="tracking_number"
                                                    id="tracking_number" value="{{ old('tracking_number') ?? '' }}" />
                                                <span class="form_message"></span>
                                            </div>
                                            <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8 form-group">
                                                <b>{{ __('Label Url *') }}</b>
                                                <input type="text" class="form-control" name="label_url"
                                                    id="label_url" value="{{ old('label_url') ?? '' }}" />
                                                <span class="form_message"></span>
                                            </div>
                                        </div>
                                        <div class="row amx-n4">
                                            <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8 form-group">
                                                <b>{{ __('Shipping Carrier *') }}</b>
                                                <input type="text" class="form-control" name="shipping_carrier"
                                                    id="shipping_carrier" value="{{ old('shipping_carrier') ?? '' }}" />
                                                <span class="form_message"></span>
                                            </div>
                                            <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8 form-group">
                                                <b>{{ __('Shipping Provider *') }}</b>
                                                <input type="text" class="form-control" name="shipping_provider"
                                                    id="shipping_provider"
                                                    value="{{ old('shipping_provider') ?? '' }}" />
                                                <span class="form_message"></span>
                                            </div>
                                        </div>
                                    </div>
                                    --}}



                                </div>

                                @if (session('errorData') !== null)
                                    <div class="amb-8 apy-8 addition-form mt-3">
                                        <div class="row amx-n4 amb-20 mt-1">
                                            @foreach (session('errorData')['errorMsg'] as $index => $error)
                                                <div class="col-10 col-xl-8 apx-4">
                                                    <p class="text-danger mb-0">
                                                        {{ $error }}
                                                    </p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <br>


                            {{-- Order Package --}}
                            <div id="content">
                                <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                                    <h4 class="amb-4 btn btn-primary">{{ __('Order Package Information') }}</h4>
                                </div>

                                <div class="amb-8 apy-8 addition-form mt-3">
                                    <div class="row amx-n4">
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8 form-group">
                                            <b>{{ __('Package Length') }}</b>
                                            <input type="text" class="form-control" name="package_length"
                                                id="package_length"
                                                value="{{ old('package_length', $order->orderPackage->length ?? '') }}" />
                                            <span class="form_message"></span>
                                        </div>
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8 form-group">
                                            <b>{{ __('Package Width') }}</b>
                                            <input type="text" class="form-control" name="package_width"
                                                id="package_width"
                                                value="{{ old('package_width', $order->orderPackage->width ?? '') }}" />
                                            <span class="form_message"></span>
                                        </div>
                                    </div>

                                    <div class="row amx-n4 amb-20 mt-1">
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

                                <div class="amb-8 apy-8 addition-form mt-3">
                                    <div class="row amx-n4">
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8 form-group">
                                            <b>{{ __('Package Height') }}</b>
                                            <input type="text" class="form-control" name="package_height"
                                                id="package_height"
                                                value="{{ old('package_height', $order->orderPackage->height ?? '') }}" />
                                            <span class="form_message"></span>
                                        </div>
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                            <b>{{ __('Size Type') }}</b>
                                            <select id="size_type" name="size_type" class="form-control w-75">
                                                @foreach (App\Models\OrderPackage::$sizeName as $value => $name)
                                                    <option value="{{ $value }}"
                                                        @if (old('size_type', $order->orderPackage->size_type) === $value) selected="selected" @endif>
                                                        {{ ucfirst($name) }}</option>
                                                @endforeach
                                            </select>
                                            {{--
                                            @if ($errors->has('size_type'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('size_type') }}
                                                </p>
                                            @endif
                                             --}}
                                        </div>
                                    </div>
                                    <div class="row amx-n4 amb-20 mt-1">
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

                                <div class="amb-8 apy-8 addition-form mt-3">
                                    <div class="row amx-n4">
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8 form-group">
                                            <b>{{ __('Package Weight') }}</b>
                                            <input type="text" class="form-control" name="package_weight"
                                                id="package_weight"
                                                value="{{ old('package_weight', $order->orderPackage->weight ?? '') }}" />
                                            <span class="form_message"></span>
                                        </div>
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                            <b>{{ __('Weight Type') }}</b>
                                            <select id="weight_type" name="weight_type" class="form-control w-75">
                                                @foreach (App\Models\OrderPackage::$weightName as $value => $name)
                                                    <option value="{{ $value }}"
                                                        @if (old('weight_type', $order->orderPackage->weight_type) === $value) selected="selected" @endif>
                                                        {{ ucfirst($name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row amx-n4 amb-20 mt-1">
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


                            <div class="">
                                <div class="row">
                                    <div class="col-12 col-md-6 form-group">
                                        <input type="hidden" class="form-control" name="tracking_number"
                                            id="tracking_number" value="{{ old('tracking_number') ?? '' }}" />
                                    </div>
                                    <div class="col-12 col-md-6 form-group">
                                        <input type="hidden" class="form-control label_path" name="label_url"
                                            id="label_url" value="{{ old('label_url') ?? '' }}" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6 form-group">
                                        <input type="hidden" class="form-control" name="shipping_carrier"
                                            id="shipping_carrier" value="{{ old('shipping_carrier') ?? '' }}" />
                                    </div>
                                    <div class="col-12 col-md-6 form-group">
                                        <input type="hidden" class="form-control" name="shipping_provider"
                                            id="shipping_provider" value="{{ old('shipping_provider') ?? '' }}" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6 form-group">
                                        <input type="hidden" class="form-control" name="amount" id="amount"
                                            value="{{ old('amount') ?? '' }}" />
                                    </div>
                                    <div class="col-12 col-md-6 form-group">
                                        {{-- <input type="file" accept="application/pdf" hidden id="order_files"
                                            name="order_files" class="btn-primary form-control"> --}}
                                    </div>
                                </div>
                            </div>



                            <div class="search-form-group">
                                <!--<div class="search-label d-none d-sm-block"></div>-->
                                <div class="text-center text-sm-left">
                                    <input class="btn btn-info btn-round create_label_normal" type="button"
                                        value="{{ __('Create Label') }}">
                                </div>
                                <!-- <div class="text-center text-sm-left ml-2">
                                    <input class="btn btn-success btn-round create_label_g7" type="button"
                                        value="{{ __('Buy labels via g7') }}">
                                </div> -->
                                <div class="text-center text-sm-left ml-2">
                                    <input class="btn btn-primary btn-round create_label_myib" type="button"
                                        value="{{ __('Buy labels via Myib') }}">
                                </div>
                                <div class="text-center text-sm-left ml-2">
                                    <input class="btn btn-warning btn-round create_label_other exc_validate"
                                        type="button" data-toggle="modal" data-target="#noticeModal"
                                        value="{{ __('Mua labels ngoài') }}">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- notice modal -->
    <div class="modal fade" id="noticeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-notice">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <i class="nc-icon nc-simple-remove"></i>
                    </button>
                    <h5 class="modal-title" id="myModalLabel">Additional Information
                    </h5>
                </div>
                <div class="modal-body">
                    <div class="add_info">
                        <div class="row">
                            <div class="col-12 col-md-6 form-group">
                                <b>{{ __('Tracking Number *') }}</b>
                                <input type="text" class="form-control input_required" name=""
                                    data-name="tracking_number" id="tracking_number"
                                    value="{{ old('tracking_number') ?? '' }}" />
                                <span class="form_message"></span>
                            </div>
                            <div class="col-12 col-md-6 form-group">
                                <b>{{ __('Shipping Carrier Name *') }}</b>
                                <input type="text" class="form-control input_required" name=""
                                       data-name="shipping_carrier" id="shipping_carrier"
                                       value="{{ old('shipping_carrier') ?? '' }}" />
                                <span class="form_message"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6 form-group">
                                <b>{{ __('Amount (VND) *') }}</b>
                                <input type="number" class="form-control input_required" name="" min="0"
                                       step="1" data-name="amount" value="0" id="amount"
                                       value="{{ old('amount') ?? '' }}" />
                                <span class="form_message"></span>
                            </div>
                            <div class="col-12 col-md-6 form-group">
                                <b>{{ __('Label Url *') }}</b>
                                <input type="text" class="form-control input_required label_path label_path_disable"
                                    name="" data-name="label_url" id="label_url"
                                    value="{{ old('label_url') ?? '' }}" />
                                <span class="form_message"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6 form-group">
                                <div>
                                    <b>{{ __('File') }}</b>
                                </div>
                                <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                                    <div class="fileinput-preview fileinput-exists thumbnail"></div>
                                    <div>
                                        <span class="btn btn-sm btn-rose btn-round btn-file">
                                            <span class="fileinput-new" onclick="uploadOrderFiles()">Select image</span>
                                            <span class="fileinput-exists">Change</span>
                                            <input type="file" accept="application/pdf" id="order_files"
                                                name="order_files" class="btn-primary form-control">
                                        </span>
                                        <a href="#pablo" class="btn btn-sm btn-info btn-round fileinput-exists"
                                            data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-info btn-round min-w-160 submit_create_label_other">Create
                        Label</button>
                </div>
            </div>
        </div>
    </div>
    <!-- end notice modal -->
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.create_label_g7').on('click', function() {
                let is_confirm = confirm('Are you sure you want to create a label?');

                if (is_confirm) {
                    let $this = $(this);
                    $this.prop('disabled', true);
                    setTimeout(function () {
                        $this.prop('disabled', false);
                    }, 10000)

                    let url = "{{ route('staff.orders.labels.create.g7') }}";
                    $('#create_label_form').prop('action', url);
                    $('#create_label_form').submit();
                }
            })

            $('.create_label_myib').on('click', function() {
                let isValidate = true;

                // Validate required fields similar to create_label_other
                input_add_info.forEach(function(input) {
                    if (input.value.trim() === '') {
                        isValidate = false;
                        input.parentElement.classList.add('invalid');
                        input.parentElement.querySelector('.form_message').innerText =
                            'Vui lòng nhập trường này!';
                    }
                })

                // Validate form fields
                let requiredFields = [
                    '#shipping_name',
                    '#shipping_country',
                    '#shipping_street',
                    '#shipping_province',
                    '#shipping_city',
                    '#shipping_zip',
                    '#package_height',
                    '#package_length',
                    '#package_width',
                    '#package_weight'
                ];

                requiredFields.forEach(function(fieldSelector) {
                    let field = $(fieldSelector);
                    if (!field.length || field.val().trim() === '' || field.val() == 0) {
                        isValidate = false;
                        field.closest('.form-group').addClass('invalid');
                        let errorMsg = field.closest('.form-group').find('.form_message');
                        if (errorMsg.length) {
                            errorMsg.text('Vui lòng nhập trường này!');
                        }
                    }
                });

                // Validate size_type and weight_type
                let sizeType = $('#size_type');
                if (!sizeType.length || !sizeType.val() || sizeType.val().trim() === '') {
                    isValidate = false;
                    sizeType.closest('.form-group').addClass('invalid');
                    let errorMsg = sizeType.closest('.form-group').find('.form_message');
                    if (errorMsg.length) {
                        errorMsg.text('Vui lòng chọn loại kích thước!');
                    }
                }

                let weightType = $('#weight_type');
                if (!weightType.length || !weightType.val() || weightType.val().trim() === '') {
                    isValidate = false;
                    weightType.closest('.form-group').addClass('invalid');
                    let errorMsg = weightType.closest('.form-group').find('.form_message');
                    if (errorMsg.length) {
                        errorMsg.text('Vui lòng chọn loại trọng lượng!');
                    }
                }

                if (isValidate) {
                    let is_confirm = confirm('Are you sure you want to create a label?');

                    if (is_confirm) {
                        let $this = $(this);
                        $this.prop('disabled', true);
                        setTimeout(function () {
                            $this.prop('disabled', false);
                        }, 10000)

                        let url = "{{ route('staff.orders.labels.create.myib') }}";
                        $('#create_label_form').prop('action', url);
                        $('#create_label_form').submit();
                    }
                } else {
                    // Scroll to first invalid field
                    let firstInvalid = $('.form-group.invalid').first();
                    if (firstInvalid.length) {
                        $('html, body').animate({
                            scrollTop: firstInvalid.offset().top - 100
                        }, 500);
                    }
                }
            })

            $('.create_label_normal').on('click', function() {
                let url = "{{ route('staff.orders.labels.store', ['orderId' => $order['id']]) }}";
                $('#create_label_form').prop('action', url);
                $('#create_label_form').submit();
            })




            let input_add_info = document.querySelectorAll('.add_info .input_required');
            input_add_info.forEach(function(input) {
                input.onblur = function() {
                    if (this.value.trim() === '') {
                        this.parentElement.classList.add('invalid');
                        this.parentElement.querySelector('.form_message').innerText =
                            'Vui lòng nhập trường này!';
                    }
                }

                input.oninput = function() {
                    let selector = '#' + $(this).data('name');
                    $(selector).prop('value', $(this).val())
                    this.parentElement.classList.remove('invalid');
                    this.parentElement.querySelector('.form_message').innerText =
                        '';
                }
            })

            $('.submit_create_label_other').on('click', function() {
                let isValidate = true;

                input_add_info.forEach(function(input) {
                    if (input.value.trim() === '') {
                        isValidate = false;
                        input.parentElement.classList.add('invalid');
                        input.parentElement.querySelector('.form_message').innerText =
                            'Vui lòng nhập trường này!';
                    }
                })

                if (isValidate) {
                    let is_confirm = confirm('Are you sure you want to create a label?');

                    if (is_confirm) {
                        let $this = $(this);
                        $this.prop('disabled', true);
                        setTimeout(function () {
                            $this.prop('disabled', false);
                        }, 10000)

                        let url = "{{ route('staff.orders.labels.create.other') }}";
                        $('#create_label_form').prop('action', url);
                        $('#create_label_form').submit();
                    }
                }
            })
        });
    </script>

    <script>
        // Upload file pdf
        function uploadOrderFiles() {
            $('#order_files').click();
        }

        $(document).ready(function() {
            // Xử lý select country, state, city
            $('select#shipping_country').select2();
            $('select#shipping_province').select2();
            $('select#shipping_city').select2();

            /*if ($('#shipping_country').val()) {
                let id = $('option:selected',$('#shipping_country')).data("id");
                if (!!id) {
                    getStatesByCountryId(id);
                }
            }*/

            $('#shipping_country').on('change', function () {
                let id = $('option:selected',this).data("id");
                if (!!id) {
                    getStatesByCountryId(id);
                }
            })

            $('#shipping_province').on('change', function () {
                let id = $('option:selected',this).data("id");
                if (!!id) {
                    getCitiesByStateId(id);
                }
            })

            function getCitiesByStateId(id) {
                $.ajax({
                    url: '{{ route('getCitiesByStateId') }}',
                    method: 'post',
                    data: {
                        id: id,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function (res) {
                        console.log(res)
                        if (res.length > 0) {
                            let html = '';
                            res.forEach(function (e) {
                                html += '<option data-id="' + e.id + '" value="' + e.name + '">' + e.name + '</option>'
                            })

                            $('select#shipping_city').html(html);
                        }
                    },
                    error: function (err) {
                        console.log(err)
                    }
                })
            }

            function getStatesByCountryId(id) {
                $.ajax({
                    url: '{{ route('getStatesByCountryId') }}',
                    method: 'post',
                    data: {
                        id: id,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function (res) {
                        console.log(res)
                        if (res.length > 0) {
                            let html = '';
                            res.forEach(function (e) {
                                html += '<option data-id="' + e.id + '" value="' + e.state_code.toUpperCase() + '">' + e.name + ' [' + e.state_code.toUpperCase() + ']' + '</option>'
                            })

                            $('select#shipping_province').html(html);
                        }
                    },
                    error: function (err) {
                        console.log(err)
                    }
                })
            }


            //
            $("#order_files").change(function(e) {
                if (!!this.files[0]) {
                    $('.submit_create_label_other').prop('disabled', true);
                }
                // upload file
                var fd = new FormData();
                var order_id = "{{ $order->id }}";
                console.log(this.files[0]);
                // Append data
                fd.append('file', this.files[0]);
                fd.append('order_id', "{{ $order->id }}");
                fd.append('_token', '{{ csrf_token() }}');

                // Hide alert
                $('#responseMsg').hide();

                // AJAX request
                $.ajax({
                    url: "{{ route('staff.orders.uploadFiles') }}",
                    method: 'post',
                    data: fd,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                        // window.location.reload();
                        // alert("Tải lên file thành công!");
                        let label_path = response.filepath;
                        console.log(response);
                        $('.label_path').each(function() {
                            $(this).val(label_path);
                            if ($(this).hasClass('label_path_disable')) {
                                if (!!label_path) {
                                    $(this).prop('disabled', true);
                                } else {
                                    $(this).prop('disabled', false);
                                }
                            }
                        })
                        $('.submit_create_label_other').prop('disabled', false);
                    },
                    error: function(response) {
                        // alert("Có lỗi up file lên!");
                        console.log("error: " + JSON.stringify(response));
                    }
                });
                // end
            });
        })
    </script>

    <script>
        // Validate the form #form_1
        Validator({
            form: '#create_label_form',
            formGroupSelector: '.form-group',
            errorSelector: '.form_message',
            rules: [
                Validator.isRequired('#shipping_name'),

                Validator.isRequired('#shipping_country'),

                Validator.isRequired('#shipping_street'),

                Validator.maxLength('#shipping_street', 35),

                Validator.maxLength('#shipping_address1', 35),

                Validator.maxLength('#shipping_address2', 35),

                Validator.isRequired('#shipping_province'),

                Validator.isRequired('#shipping_city'),

                Validator.isRequired('#shipping_zip'),

                Validator.isRequired('#package_height'),

                Validator.isRequired('#package_length'),

                Validator.isRequired('#package_width'),

                Validator.isRequired('#package_weight'),
            ],
        });
    </script>
@endpush
