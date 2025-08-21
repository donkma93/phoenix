@extends('layouts.app',[
'class' => '',
'folderActive' => 'order-management-2',
'elementActive' => 'create-order'
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

        .suggestion-wrapper {
            position: relative;
        }

        .suggestion {
            margin-top: 4px;
            position: absolute;
            z-index: 1;
            left: 15px;
            right: 15px;
            padding: 8px 20px 8px 12px;
            border: 1px solid #9a9a9a;
            border-radius: 4px;
            background-color: #ece7e7;
            box-shadow: 4px 4px 12px #a8a8a8;
        }

        .suggestion .icon-close {
            position: absolute;
            top: 0;
            right: 0;
            cursor: pointer;
            padding: 4px;
        }

        .suggestion .icon-close:hover i {
            font-weight: 600 !important;
        }

        .suggestion:before {
            position: absolute;
            left: 0;
            right: 0;
            top: -6px;
            content: '';
            display: block;
            height: 8px;
            background-color: transparent;
        }

        .suggestion ul {
            margin: 0;
            padding: 0;
            list-style-type: none;
        }

        .suggestion li {
            padding: 4px;
            cursor: pointer;
            border-radius: 4px;
            overflow: hidden;
        }

        .suggestion li:hover {
            background-color: #c0eac7;
        }

        /*CSS for select2*/
        .select2-container {
            width: 100% !important;
        }

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

@section('content')
    <div class="content">
        @include('layouts.partials.message')
        <div class="fade-in">
            <div class="card px-4 py-2">
                <div class="card-header">
                    <h2 class="mb-0">{{ __('Create New Order') }}</h2>
                </div>
                <div class="card-body">
                    <div>
                        <form method="POST" action="{{ route('staff.order2.store') }}"
                              class="form-horizontal prevent-double-click" role="form">
                            @csrf
                            <div class="row">
                                <div class="col-12 col-xl-6">
                                    <div>
                                        <div
                                                class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                                            <h4 class="amb-4 mt-0 text-uppercase">{{ __('Shipper') }}</h4>
                                        </div>

                                        <div class="amb-8 apy-8 addition-form mt-3">
                                            <div class="row amx-n4">
                                                <div class="col-12 apx-4 amb-8 suggestion-wrapper">
                                                    <b>{{ __('Company name') }}</b>
                                                    <input
                                                            type="text" class="form-control suggestion_shipper_input"
                                                            name="shipper_company"
                                                            id="shipper_company"
                                                            value="{{ old('shipper_company') ?? '' }}"
                                                            autocomplete="off"
                                                    />
                                                    @if ($errors->has('shipper_company'))
                                                        <div class="mt-1">
                                                            <p class="text-danger mb-0">
                                                                {{ $errors->first('shipper_company') }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                    <div class="suggestion suggestion_shipper" style="display: none;">
                                                        <span class="icon-close"><i
                                                                    class="nc-icon nc-simple-remove"></i></span>
                                                        <ul>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="amb-8 apy-8 addition-form mt-3">
                                            <div class="row amx-n4">
                                                <div class="col-12 col-xl-6 apx-4 amb-8">
                                                    <b>{{ __('Contact name *') }}</b>
                                                    <input
                                                            type="text" class="form-control"
                                                            name="shipper_name"
                                                            id="shipper_name"
                                                            value="{{ old('shipper_name') ?? '' }}"
                                                    />
                                                    @if ($errors->has('shipper_name'))
                                                        <div class="mt-1">
                                                            <p class="text-danger mb-0">
                                                                {{ $errors->first('shipper_name') }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="col-12 col-xl-6 apx-4 amb-8 mt-3 mt-xl-0">
                                                    <b>{{ __('Phone/Fax No. *') }}</b>
                                                    <input
                                                            type="text" class="form-control"
                                                            name="shipper_phone"
                                                            id="shipper_phone"
                                                            value="{{ old('shipper_phone') ?? '' }}"
                                                    />
                                                    @if ($errors->has('shipper_phone'))
                                                        <div class="mt-1">
                                                            <p class="text-danger mb-0">
                                                                {{ $errors->first('shipper_phone') }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="amb-8 apy-8 addition-form mt-3">
                                            <div class="row amx-n4">
                                                <div class="col-12 col-xl-6 apx-4 amb-8">
                                                    <b>{{ __('Country *') }}</b>
                                                    <select name="shipper_country" id="shipper_country"
                                                            class="form-control">
                                                        <option value="">Select Country</option>
                                                        @foreach($countries as $country)
                                                            <option data-id="{{ $country->id }}"
                                                                    value="{{ strtolower($country->name) }}"
                                                                    {{ old('shipper_country') !== null && old('shipper_country') == strtolower($country->name) ? 'selected' : '' }}
                                                            >
                                                                {{ $country->name . ' [' . $country->code . ']' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('shipper_country'))
                                                        <div class="mt-1">
                                                            <p class="text-danger mb-0">
                                                                {{ $errors->first('shipper_country') }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="col-12 col-xl-6 apx-4 amb-8 mt-3 mt-xl-0">
                                                    <b>{{ __('State/Province *') }}</b>
                                                    <select name="shipper_province" id="shipper_province"
                                                            class="form-control">
                                                        <option value="">Select State/Province</option>
                                                        @foreach($states as $state)
                                                            <option data-id="{{ $state->id }}"
                                                                    value="{{ strtolower($state->name) }}"
                                                                    {{ old('shipper_province') !== null && old('shipper_province') == strtolower($state->name) ? 'selected' : '' }}
                                                            >
                                                                {{ $state->name . ' [' . $state->state_code . ']' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('shipper_province'))
                                                        <div class="mt-1">
                                                            <p class="text-danger mb-0">
                                                                {{ $errors->first('shipper_province') }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="amb-8 apy-8 addition-form mt-3">
                                            <div class="row amx-n4">
                                                <div class="col-12 apx-4 amb-8">
                                                    <b>{{ __('Shipping Address *') }}</b>
                                                    <input
                                                            type="text" class="form-control"
                                                            name="shipper_address"
                                                            id="shipper_address"
                                                            value="{{ old('shipper_address') ?? '' }}"
                                                    />
                                                    @if ($errors->has('shipper_address'))
                                                        <div class="mt-1">
                                                            <p class="text-danger mb-0">
                                                                {{ $errors->first('shipper_address') }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="amb-8 apy-8 addition-form mt-3">
                                            <div class="row amx-n4">
                                                <div class="col-12 apx-4 amb-8">
                                                    <b>{{ __('Pickup Address') }}</b>
                                                    <input
                                                        type="text" class="form-control"
                                                        name="pickup_address"
                                                        id="pickup_address"
                                                        value="{{ old('pickup_address') ?? '' }}"
                                                    />
                                                    @if ($errors->has('pickup_address'))
                                                        <div class="mt-1">
                                                            <p class="text-danger mb-0">
                                                                {{ $errors->first('pickup_address') }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-xl-6">
                                    <div>
                                        <div
                                                class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                                            <h4 class="amb-4 mt-3 mt-xl-0 text-uppercase">{{ __('Receiver') }}</h4>
                                        </div>

                                        <div class="amb-8 apy-8 addition-form mt-3">
                                            <div class="row amx-n4">
                                                <div class="col-12 apx-4 amb-8 suggestion-wrapper">
                                                    <b>{{ __('Company name') }}</b>
                                                    <input
                                                            type="text" class="form-control suggestion_receiver_input"
                                                            name="receiver_company"
                                                            id="receiver_company"
                                                            value="{{ old('receiver_company') ?? '' }}"
                                                            autocomplete="off"
                                                    />
                                                    @if ($errors->has('receiver_company'))
                                                        <div class="mt-1">
                                                            <p class="text-danger mb-0">
                                                                {{ $errors->first('receiver_company') }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                    <div class="suggestion suggestion_receiver" style="display: none;">
                                                        <span class="icon-close"><i
                                                                    class="nc-icon nc-simple-remove"></i></span>
                                                        <ul>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="amb-8 apy-8 addition-form mt-3">
                                            <div class="row amx-n4">
                                                <div class="col-12 col-xl-6 apx-4 amb-8">
                                                    <b>{{ __('Contact name *') }}</b>
                                                    <input
                                                            type="text" class="form-control"
                                                            name="receiver_name"
                                                            id="receiver_name"
                                                            value="{{ old('receiver_name') ?? '' }}"
                                                    />
                                                    @if ($errors->has('receiver_name'))
                                                        <div class="mt-1">
                                                            <p class="text-danger mb-0">
                                                                {{ $errors->first('receiver_name') }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="col-12 col-xl-6 apx-4 amb-8 mt-3 mt-xl-0">
                                                    <b>{{ __('Phone/Fax No. *') }}</b>
                                                    <input
                                                            type="text" class="form-control"
                                                            name="receiver_phone"
                                                            id="receiver_phone"
                                                            value="{{ old('receiver_phone') ?? '' }}"
                                                    />
                                                    @if ($errors->has('receiver_phone'))
                                                        <div class="mt-1">
                                                            <p class="text-danger mb-0">
                                                                {{ $errors->first('receiver_phone') }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="amb-8 apy-8 addition-form mt-3">
                                            <div class="row amx-n4">
                                                <div class="col-12 col-xl-6 apx-4 amb-8">
                                                    <b>{{ __('Country *') }}</b>
                                                    <select name="receiver_country" id="receiver_country"
                                                            class="form-control">
                                                        <option value="">Select Country</option>
                                                        @foreach($countries as $country)
                                                            <option data-id="{{ $country->id }}"
                                                                    value="{{ strtolower($country->name) }}"
                                                                    {{ old('receiver_country') !== null && old('receiver_country') == strtolower($country->name) ? 'selected' : '' }}
                                                            >
                                                                {{ $country->name . ' [' . $country->code . ']' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('receiver_country'))
                                                        <div class="mt-1">
                                                            <p class="text-danger mb-0">
                                                                {{ $errors->first('receiver_country') }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="col-12 col-xl-6 apx-4 amb-8 mt-3 mt-xl-0">
                                                    <b>{{ __('State/Province *') }}</b>
                                                    <select name="receiver_province" id="receiver_province"
                                                            class="form-control">
                                                        <option value="">Select State/Province</option>
                                                        @foreach($states as $state)
                                                            <option data-id="{{ $state->id }}"
                                                                    value="{{ strtolower($state->name) }}"
                                                                    {{ old('receiver_province') !== null && old('receiver_province') == strtolower($state->name) ? 'selected' : '' }}
                                                            >
                                                                {{ $state->name . ' [' . $state->state_code . ']' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('receiver_province'))
                                                        <div class="mt-1">
                                                            <p class="text-danger mb-0">
                                                                {{ $errors->first('receiver_province') }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="amb-8 apy-8 addition-form mt-3">
                                            <div class="row amx-n4">
                                                <div class="col-12 apx-4 amb-8">
                                                    <b>{{ __('Address *') }}</b>
                                                    <input
                                                            type="text" class="form-control"
                                                            name="receiver_address"
                                                            id="receiver_address"
                                                            value="{{ old('receiver_address') ?? '' }}"
                                                    />
                                                    @if ($errors->has('receiver_address'))
                                                        <div class="mt-1">
                                                            <p class="text-danger mb-0">
                                                                {{ $errors->first('receiver_address') }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div>
                                        <div
                                                class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                                            <h4 class="amb-4 mt-3 text-uppercase">{{ __('Order information') }}</h4>
                                        </div>

                                        <div class="amb-8 apy-8 addition-form mt-3">
                                            <div class="row amx-n4">
                                                <div class="col-6 col-xl-3 apx-4 amb-8">
                                                    <b>{{ __('Type *') }}</b>
                                                    <select name="commodity" id="commodity" class="form-control">
                                                        <option value="">Select Type</option>
                                                        <option
                                                                {{ old('commodity') !== null && old('commodity') == 'HH' ? 'selected' : '' }} value="HH">
                                                            HH
                                                        </option>
                                                        <option
                                                                {{ old('commodity') !== null && old('commodity') == 'TH' ? 'selected' : '' }} value="TH">
                                                            TH
                                                        </option>
                                                    </select>
                                                    @if ($errors->has('commodity'))
                                                        <div class="mt-1">
                                                            <p class="text-danger mb-0">
                                                                {{ $errors->first('commodity') }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="col-6 col-xl-3 apx-4 amb-8">
                                                    <b>{{ __('Service *') }}</b>
                                                    <select name="service_of_order" id="service_of_order"
                                                            class="form-control">
                                                        <option value="">Select Service</option>
                                                        <option
                                                                {{ old('service_of_order') !== null && old('service_of_order') == 'expedited' ? 'selected' : '' }} value="expedited">
                                                            Expedited
                                                        </option>
                                                        <option
                                                                {{ old('service_of_order') !== null && old('service_of_order') == 'saver' ? 'selected' : '' }} value="saver">
                                                            Saver
                                                        </option>
                                                    </select>
                                                    @if ($errors->has('service_of_order'))
                                                        <div class="mt-1">
                                                            <p class="text-danger mb-0">
                                                                {{ $errors->first('service_of_order') }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="col-12 col-xl-6 apx-4 amb-8 mt-3 mt-xl-0">
                                                    <div class="row amx-n4">
                                                        <div class="col-6">
                                                            <b>{{ __('Surcharge name') }}</b>
                                                            <input
                                                                type="text" class="form-control"
                                                                name="surcharge_name"
                                                                id="surcharge_name"
                                                                value="{{ old('surcharge_name') ?? '' }}"
                                                            />
                                                            @if ($errors->has('surcharge_name'))
                                                                <div class="mt-1">
                                                                    <p class="text-danger mb-0">
                                                                        {{ $errors->first('surcharge_name') }}
                                                                    </p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="col-6">
                                                            <b>{{ __('Fee (USD)') }}</b>
                                                            <input
                                                                type="number" class="form-control"
                                                                min="0"
                                                                step="0.01"
                                                                name="surcharge_fee"
                                                                id="surcharge_fee"
                                                                value="{{ old('surcharge_fee') ?? '' }}"
                                                            />
                                                            @if ($errors->has('surcharge_fee'))
                                                                <div class="mt-1">
                                                                    <p class="text-danger mb-0">
                                                                        {{ $errors->first('surcharge_fee') }}
                                                                    </p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="search-form-group mt-4">
                                <div class="search-input text-center text-sm-left">
                                    <input class="btn btn-lg btn-info btn-round" type="submit"
                                           value="{{ __('Create Order') }}">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let sugges_shipper = [];
        $(document).ready(function () {
            let oldShipper = '{{ old('shipper_province') }}';
            let oldReceiver = '{{ old('receiver_province') }}';

            // Xử lý select country, state
            $('select#shipper_country').select2();
            $('select#shipper_province').select2();

            if ($('#shipper_country').val()) {
                let id = $('option:selected', $('#shipper_country')).data("id");
                if (!!id) {
                    getStatesByCountryId(id, 'select#shipper_province', oldShipper);
                }
            }

            $('#shipper_country').on('change', function () {
                let id = $('option:selected', this).data("id");
                if (!!id) {
                    getStatesByCountryId(id, 'select#shipper_province', oldShipper);
                }
            })

            $('select#receiver_country').select2();
            $('select#receiver_province').select2();

            if ($('#receiver_country').val()) {
                let id = $('option:selected', $('#receiver_country')).data("id");
                if (!!id) {
                    getStatesByCountryId(id, 'select#receiver_province', oldReceiver);
                }
            }

            $('#receiver_country').on('change', function () {
                let id = $('option:selected', this).data("id");
                if (!!id) {
                    getStatesByCountryId(id, 'select#receiver_province', oldReceiver);
                }
            })

            /*$('.suggestion_receiver_input').on('blur', function () {
                $('.suggestion_receiver').hide();
                $('.suggestion_receiver ul').html('');
            });

            $('.suggestion_shipper_input').on('blur', function (e) {
                $('.suggestion_shipper').hide();
                $('.suggestion_shipper ul').html('');
            });*/

            $('.suggestion_shipper_input').on('input focus', function (e) {
                let keyword = e.target.value.trim();
                if (!!keyword) {
                    $.ajax({
                        url: '/staff/get-suggestion-shipper/' + keyword,
                        type: 'GET',
                        dataType: 'json',
                        success: function (res) {
                            if (res.length > 0) {
                                sugges_shipper = res;
                                let html = '';
                                res.forEach(function (rus) {
                                    html += `<li class="suggestion_item" data-id="${rus.shipper_id}" onclick="selectSuggesShipper(this)">${rus.shipper_name}, ${rus.shipper_phone}, ${rus.shipper_address}, ${rus.shipper_province}, ${rus.shipper_country}, ${rus.shipper_company}</li>`;
                                });

                                $('.suggestion_shipper').show();
                                $('.suggestion_shipper ul').html(html);
                            }
                        },
                        error: function (err) {
                            console.log(err);
                        }
                    });
                } else {
                    $('.suggestion_shipper').hide();
                    $('.suggestion_shipper ul').html('');
                }
            });

            $('.suggestion_receiver_input').on('input focus', function (e) {
                let keyword = e.target.value.trim();
                if (!!keyword) {
                    $.ajax({
                        url: '/staff/get-suggestion-receiver/' + keyword,
                        type: 'GET',
                        dataType: 'json',
                        success: function (res) {
                            if (res.length > 0) {
                                sugges_shipper = res;
                                let html = '';
                                res.forEach(function (rus) {
                                    html += `<li class="suggestion_item" data-id="${rus.receiver_id}" onclick="selectSuggesReceiver(this)">${rus.receiver_name}, ${rus.receiver_phone}, ${rus.receiver_address}, ${rus.receiver_province}, ${rus.receiver_country}, ${rus.receiver_company}</li>`;
                                });

                                $('.suggestion_receiver').show();
                                $('.suggestion_receiver ul').html(html);
                            }
                        },
                        error: function (err) {
                            console.log(err);
                        }
                    });
                } else {
                    $('.suggestion_receiver').hide();
                    $('.suggestion_receiver ul').html('');
                }
            });

            $('.icon-close').on('click', function () {
                $(this).closest('.suggestion').find('ul').html('');
                $(this).closest('.suggestion').hide();
            });
        });

        function selectSuggesReceiver(ele) {
            let id = $(ele).data('id');
            let receiver = sugges_shipper.find(function (current) {
                return current.receiver_id == id;
            });

            $('#receiver_company').val(receiver.receiver_company);
            $('#receiver_address').val(receiver.receiver_address);
            $('#receiver_name').val(receiver.receiver_name);
            $('#receiver_phone').val(receiver.receiver_phone);
            $('#receiver_country').val(receiver.receiver_country);
            $('#receiver_province').val(receiver.receiver_province);

            $('select#receiver_country').select2();

            if ($('#receiver_country').val()) {
                let id = $('option:selected', $('#receiver_country')).data("id");
                if (!!id) {
                    getStatesByCountryId(id, 'select#receiver_province', receiver.receiver_province);
                }
            }

            $('.suggestion_receiver').hide();
            $('.suggestion_receiver ul').html('');
        }

        function selectSuggesShipper(ele) {
            let id = $(ele).data('id');
            let shipper = sugges_shipper.find(function (current) {
                return current.shipper_id == id;
            });

            $('#shipper_company').val(shipper.shipper_company);
            $('#shipper_address').val(shipper.shipper_address);
            $('#pickup_address').val(shipper.pickup_address);
            $('#shipper_name').val(shipper.shipper_name);
            $('#shipper_phone').val(shipper.shipper_phone);
            $('#shipper_country').val(shipper.shipper_country);
            $('#shipper_province').val(shipper.shipper_province);

            $('select#shipper_country').select2();

            if ($('#shipper_country').val()) {
                let id = $('option:selected', $('#shipper_country')).data("id");
                if (!!id) {
                    getStatesByCountryId(id, 'select#shipper_province', shipper.shipper_province);
                }
            }

            $('.suggestion_shipper').hide();
            $('.suggestion_shipper ul').html('');
        }

        function getStatesByCountryId(id, selector_target, val_selected) {
            $.ajax({
                url: '{{ route('getStatesByCountryId') }}',
                method: 'post',
                data: {
                    id: id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function (res) {
                    if (res.length > 0) {
                        let html = '<option value="">Select State/Province</option>';
                        res.forEach(function (e) {
                            let isSelected = e.name == val_selected ? 'selected' : '';
                            html += '<option ' + isSelected + ' data-id="' + e.id + '" value="' + e.name + '">' + e.name + ' [' + e.state_code.toUpperCase() + ']' + '</option>'
                        })

                        $(selector_target).html(html);
                    }
                },
                error: function (err) {
                    console.log(err)
                }
            })
        }
    </script>
@endpush
