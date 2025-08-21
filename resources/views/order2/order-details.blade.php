@extends('layouts.app',[
'class' => '',
'folderActive' => 'order-management-2',
'elementActive' => 'list-orders'
])

@section('styles')
    <link rel="stylesheet" href="/assets/js/plugins/fancybox/jquery.fancybox.css" type="text/css" media="screen" />
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

        .action-btn {
            cursor: pointer !important;
            user-select: none;
        }

        .action-btn:hover {
            color: blue;
        }

        .table > thead > tr > th {
            text-wrap: nowrap !important;
        }

        .table > tbody > tr > td {
            vertical-align: top;
        }

        .action-btn + .action-btn {
            margin-left: 8px;
        }

        .mw-80px {
            width: 80px !important;
            max-width: 80px;
        }

        .mw-88px {
            width: 88px !important;
            max-width: 88px;
        }

        .mw-112px {
            width: 112px !important;
            max-width: 112px;
        }

        .mw-200px {
            width: 200px !important;
            max-width: 200px;
        }
        .form-control:disabled,
        .form-control[readonly] {
            cursor: default;
            background-color: #fff !important;
        }
        .btn-icon {
            cursor: pointer;
            width: 40px;
            height: 40px;
            padding: 8px;
        }
        .btn-icon svg {
            fill: #51bcda;
            width: 100%;
            height: 100%;
        }
        .btn-icon:hover svg {
            fill: #2ba9cd !important;
        }
    </style>
@endsection

@section('content')
    <div class="content">
        @include('layouts.partials.message')

        <div class="fade-in">
            <div class="card px-4 py-2">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h2 class="mb-0">{{ __('Order Details') }}</h2>
                    <span class="btn-icon btn-edit-order" style="display: inline;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                        </svg>
                    </span>
                    <span class="btn-icon btn-cancel-edit" style="display: none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                        </svg>
                    </span>
                </div>
                <div class="card-body">
                    <div>
                        <form method="POST" action="" class="form-horizontal prevent-double-click" role="form" enctype="multipart/form-data"
                              id="form-add-details">
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
                                                <div class="col-12 apx-4 amb-8">
                                                    <b>{{ __('Company name:') }}</b>
                                                    <span>{{ $order->shipper_company }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="amb-8 apy-8 addition-form mt-3">
                                            <div class="row amx-n4">
                                                <div class="col-12 col-xl-6 apx-4 amb-8">
                                                    <b>{{ __('Contact name:') }}</b>
                                                    <span>{{ $order->shipper_name }}</span>
                                                </div>
                                                <div class="col-12 col-xl-6 apx-4 amb-8 mt-3 mt-xl-0">
                                                    <b>{{ __('Phone/Fax No.:') }}</b>
                                                    <span>{{ $order->shipper_phone }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="amb-8 apy-8 addition-form mt-3">
                                            <div class="row amx-n4">
                                                <div class="col-12 col-xl-6 apx-4 amb-8">
                                                    <b>{{ __('Country:') }}</b>
                                                    <span>{{ $order->shipper_country }}</span>
                                                </div>
                                                <div class="col-12 col-xl-6 apx-4 amb-8 mt-3 mt-xl-0">
                                                    <b>{{ __('State/Province:') }}</b>
                                                    <span>{{ $order->shipper_province }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="amb-8 apy-8 addition-form mt-3">
                                            <div class="row amx-n4">
                                                <div class="col-12 apx-4 amb-8">
                                                    <b>{{ __('Shipping Address:') }}</b>
                                                    <span>{{ $order->shipper_address }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="amb-8 apy-8 addition-form mt-3">
                                            <div class="row amx-n4">
                                                <div class="col-12 apx-4 amb-8">
                                                    <b>{{ __('Pickup Address:') }}</b>
                                                    <span>{{ $order->pickup_address }}</span>
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
                                                <div class="col-12 apx-4 amb-8">
                                                    <b>{{ __('Company name:') }}</b>
                                                    <span>{{ $order->receiver_company }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="amb-8 apy-8 addition-form mt-3">
                                            <div class="row amx-n4">
                                                <div class="col-12 col-xl-6 apx-4 amb-8">
                                                    <b>{{ __('Contact name:') }}</b>
                                                    <span>{{ $order->receiver_name }}</span>
                                                </div>
                                                <div class="col-12 col-xl-6 apx-4 amb-8 mt-3 mt-xl-0">
                                                    <b>{{ __('Phone/Fax No.:') }}</b>
                                                    <span>{{ $order->receiver_phone }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="amb-8 apy-8 addition-form mt-3">
                                            <div class="row amx-n4">
                                                <div class="col-12 col-xl-6 apx-4 amb-8">
                                                    <b>{{ __('Country:') }}</b>
                                                    <span>{{ $order->receiver_country }}</span>
                                                </div>
                                                <div class="col-12 col-xl-6 apx-4 amb-8 mt-3 mt-xl-0">
                                                    <b>{{ __('State/Province:') }}</b>
                                                    <span>{{ $order->receiver_province }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="amb-8 apy-8 addition-form mt-3">
                                            <div class="row amx-n4">
                                                <div class="col-12 apx-4 amb-8">
                                                    <b>{{ __('Address:') }}</b>
                                                    <span>{{ $order->receiver_address }}</span>
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
                                                    <b>{{ __('Type:') }}</b>
                                                    <span>{{ $order->commodity }}</span>
                                                </div>
                                                <div class="col-6 col-xl-3 apx-4 amb-8">
                                                    <b>{{ __('Service:') }}</b>
                                                    <span>{{ ucfirst($order->service_of_order) }}</span>
                                                </div>
                                                <div class="col-6 col-xl-3 apx-4 amb-8 mt-3 mt-xl-0">
                                                    <b>{{ __('Package quantity:') }}</b>
                                                    <span class="count_bill_details">{{ count($orderDetails) }}</span>
                                                </div>
                                                <div class="col-6 col-xl-3 apx-4 amb-8 mt-3 mt-xl-0">
                                                    @php
                                                        $total_bill_weight = 0;
                                                        foreach ($orderDetails as $detail) {
                                                            $total_bill_weight += $detail->pack_bill_weight;
                                                        }
                                                    @endphp
                                                    <b>{{ __('Total bill weight:') }}</b>
                                                    <span
                                                        class="total_bill_weight">{{ number_format($total_bill_weight/1000, 2) }}</span>
                                                    <span>kg</span>
                                                </div>
                                                <div class="col-6 col-xl-3 apx-4 amb-8 mt-3">
                                                    <b>{{ __('User create:') }}</b>
                                                    <span class="count_bill_details">{{ $order->user_create_email }}</span>
                                                </div>
                                                <div class="col-6 col-xl-3 apx-4 amb-8 mt-3">
                                                    <b>{{ __('Latest edit:') }}</b>
                                                    <span class="count_bill_details">{{ date('d-m-Y H:i', strtotime($order->updated_at)) }}</span>
                                                </div>
                                                <div class="col-6 col-xl-3 apx-4 amb-8 mt-3">
                                                    <b>{{ __('User edit:') }}</b>
                                                    <span class="count_bill_details">{{ $order->user_edit_email }}</span>
                                                </div>
                                                <div class="col-6 col-xl-3 apx-4 amb-8 mt-3">
                                                    <b>{{ __('Total value:') }}</b>
                                                    <span class="total_value">{{ isset($order->surcharge) ? json_decode($order->surcharge)->fee : 0 }}</span>
                                                    <span>USD</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-xl-3 apx-4 amb-8 mt-3">
                                                    <b>{{ __('Air waybill No.') }}</b>
                                                    <input
                                                        readonly="readonly"
                                                        type="text" class="form-control readonly"
                                                        name="air_waybill"
                                                        id="air_waybill"
                                                        value="{{ $order->air_waybill }}"
                                                    />
                                                    @if ($errors->has('air_waybill'))
                                                        <div class="mt-1">
                                                            <p class="text-danger mb-0">
                                                                {{ $errors->first('air_waybill') }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="col-12 col-xl-3 apx-4 amb-8 mt-3">
                                                    <b>{{ __('Note total bill') }}</b>
                                                    <textarea class="form-control readonly" readonly="readonly"
                                                              name="note_total">{{ old('note_total') ?? $order->note_total }}</textarea>
                                                    @if ($errors->has('note_total'))
                                                        <div class="mt-1">
                                                            <p class="text-danger mb-0">
                                                                {{ $errors->first('note_total') }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="col-12 col-xl-6 apx-4 amb-8 mt-3">
                                                    <div class="row amx-n4">
                                                        <div class="col-6">
                                                            <b>{{ __('Surcharge name') }}</b>
                                                            <input
                                                                type="text" class="form-control readonly"
                                                                readonly="readonly"
                                                                name="surcharge_name"
                                                                id="surcharge_name"
                                                                value="{{ old('surcharge_name') ? old('surcharge_name') : (isset($order->surcharge) ? json_decode($order->surcharge)->name : '') }}"
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
                                                                type="number" class="form-control readonly"
                                                                readonly="readonly"
                                                                min="0"
                                                                step="0.01"
                                                                name="surcharge_fee"
                                                                id="surcharge_fee"
                                                                value="{{ old('surcharge_fee') ? old('surcharge_fee') : (isset($order->surcharge) ? json_decode($order->surcharge)->fee : '') }}"
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

                            <div>
                                <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                                <div class="table-responsive pb-0">
                                    <div class="d-flex justify-content-end">
                                        <span style="font-weight: 600;">Unit: cm / kg</span>
                                    </div>
                                    <table class="table" id="datatable">
                                        <thead class="text-primary">
                                        <tr>
                                            <th class="disabled-sorting">{{ __('ID') }}</th>
                                            <th class="disabled-sorting">{{ __('Mã bill chi tiết') }}</th>
                                            <th class="disabled-sorting" style="min-width: 200px;">{{ __('Tên hàng hóa') }}</th>
                                            <th class="disabled-sorting">{{ __('Số lượng') }}</th>
                                            <th class="disabled-sorting">{{ __('Đơn giá') }}</th>
                                            <th class="disabled-sorting">{{ __('Thành tiền (USD)') }}</th>
                                            <th class="disabled-sorting">{{ __('Length') }}</th>
                                            <th class="disabled-sorting">{{ __('Width') }}</th>
                                            <th class="disabled-sorting">{{ __('Height') }}</th>
                                            <th class="disabled-sorting">{{ __('TL bill kiện') }}</th>
                                            <th class="disabled-sorting">{{ __('TL thực tế') }}</th>
                                            <th class="disabled-sorting">{{ __('TL cồng kềnh') }}</th>
                                            <th class="disabled-sorting">{{ __('TL tính cước') }}</th>
                                            <th class="disabled-sorting">{{ __('Image') }}</th>
                                            <th class="disabled-sorting" style="min-width: 120px;">{{ __('Ghi chú') }}</th>
                                            {{--<th class="disabled-sorting text-center">{{ __('Actions') }}</th>--}}
                                        </tr>
                                        </thead>
                                        <tbody class="body-details">
                                        @foreach($orderDetails as $row)
                                            <tr class="line-detail" data-index="{{ $row->id }}">
                                                <td style="vertical-align: middle;">
                                                    {{ $row->id }}
                                                </td>
                                                <td class="mw-200px">
                                                    <input class="form-control readonly bill_code_detail" type="text" readonly="readonly"
                                                           name="details[{{ $row->id }}][bill_code_detail]"
                                                           value="{{ $row->bill_code_detail }}">
                                                    <p style="text-wrap: nowrap; font-size: 90%; color: red; display: none;"
                                                       class="mt-1 error-message">Bill cannot be duplicated</p>
                                                </td>
                                                <td class="mw-112px">
                                                    <input class="form-control readonly" required type="text" readonly="readonly"
                                                           name="details[{{ $row->id }}][type_of_commodity]"
                                                           value="{{ $row->type_of_commodity }}">
                                                </td>
                                                <td class="mw-112px">
                                                    <input class="form-control price-input quantity readonly" required readonly="readonly" type="number"
                                                           min="1" step="1" name="details[{{ $row->id }}][quantity]"
                                                           value="{{ $row->quantity }}">
                                                </td>
                                                <td class="mw-112px">
                                                    <input class="form-control price-input unit_price readonly" required readonly="readonly" type="number"
                                                           min="0" step="0.01" name="details[{{ $row->id }}][unit_price]"
                                                           value="{{ $row->unit_price }}">
                                                </td>
                                                <td class="mw-112px">
                                                    <input class="form-control subtotal" readonly required type="number"
                                                           min="0" step="0.01" name="details[{{ $row->id }}][subtotal]"
                                                           value="{{ $row->quantity * $row->unit_price }}">
                                                </td>
                                                <td class="mw-112px">
                                                    <input class="form-control readonly size-input length" required type="number" readonly="readonly"
                                                           min="0.1" step="0.1" name="details[{{ $row->id }}][length]"
                                                           value="{{ $row->length }}">
                                                </td>
                                                <td class="mw-112px">
                                                    <input class="form-control readonly size-input width" required type="number" readonly="readonly"
                                                           min="0.1" step="0.1" name="details[{{ $row->id }}][width]"
                                                           value="{{ $row->width }}">
                                                </td>
                                                <td class="mw-112px">
                                                    <input class="form-control readonly size-input height" required type="number" readonly="readonly"
                                                           min="0.1" step="0.1" name="details[{{ $row->id }}][height]"
                                                           value="{{ $row->height }}">
                                                </td>
                                                <td class="mw-112px">
                                                    <input class="form-control readonly weight-input pack_bill_weight" required readonly="readonly"
                                                           type="number" min="0.01" step="0.01"
                                                           name="details[{{ $row->id }}][pack_bill_weight]"
                                                           value="{{ number_format($row->pack_bill_weight/1000, 2) }}">
                                                </td>
                                                <td class="mw-112px">
                                                    <input class="form-control readonly weight-input actual_weight" required readonly="readonly"
                                                           type="number" min="0.01" step="0.01"
                                                           name="details[{{ $row->id }}][actual_weight]"
                                                           value="{{ number_format($row->actual_weight/1000, 2) }}">
                                                </td>
                                                <td style="max-width: 124px;">
                                                    <input class="form-control bulky_weight" required readonly
                                                           type="number" min="0.01" step="0.01" tabindex="-1"
                                                           name="details[{{ $row->id }}][bulky_weight]"
                                                           value="{{ number_format($row->bulky_weight/1000, 2) }}">
                                                </td>
                                                <td style="max-width: 124px;">
                                                    <input class="form-control billable_weight" required readonly
                                                           type="number" min="0.01" step="0.01" tabindex="-1"
                                                           name="details[{{ $row->id }}][billable_weight]"
                                                           value="{{ number_format($row->billable_weight/1000, 2) }}">
                                                </td>
                                                <td style="text-align: center; vertical-align: middle;">
                                                    @if($row->img_url)
                                                        <a href="{{ url($row->img_url) }}" target="_blank" class="fancybox show-image">
                                                            <i class="nc-icon nc-album-2" style="font-size: 24px;"></i>
                                                        </a>
                                                    @endif
                                                    <input class="form-control change-image" type="file" name="details[{{ $row->id }}][image]" style="display: none;">
                                                </td>
                                                <td>
                                                    <input class="form-control readonly" type="text" readonly="readonly"
                                                           name="details[{{ $row->id }}][note_detail]"
                                                           value="{{ $row->note_detail }}">
                                                </td>
                                                {{--<td class="text-center mw-88px"
                                                    style="text-wrap: nowrap; vertical-align: middle;">
                                                    <span title="Copy line to end" onclick="copyLine(this)"
                                                          class="action-btn btn-copy"><i
                                                            class="nc-icon nc-single-copy-04"></i></span>
                                                    <span title="Add new line to end" onclick="addLine(this)"
                                                          class="action-btn btn-add"><i
                                                            class="nc-icon nc-simple-add"></i></span>
                                                    <span title="Delete line" onclick="deleteLine(this)"
                                                          class="action-btn btn-delete"><i
                                                            class="nc-icon nc-simple-delete"></i></span>
                                                </td>--}}
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="search-form-group wrapper-btn-submit" style="display: none;">
                                <div class="search-input text-center text-sm-left">
                                    <input class="btn btn-lg btn-info btn-round" id="btn-submit-add-details"
                                           type="submit" value="{{ __('Update Order') }}">
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
    <script src="{{ URL::asset('assets/js/plugins/fancybox/jquery.fancybox.pack.js') }}"></script>
    <script>
        $(document).ready(function () {
            calculateTotalValue();

            // Fancybox
            $('.fancybox').fancybox();

            //
            $('.btn-edit-order').on('click', function () {
                if (confirm('Are you sure want to edit this order?')) {
                    $('.form-control[readonly="readonly"]').attr('readonly', false);
                    $('.btn-edit-order').hide();
                    $('.btn-cancel-edit').show();
                    $('.wrapper-btn-submit').show();
                    $('.change-image').show();
                    $('.show-image').hide();
                    $('#air_waybill').focus();
                }
            });

            $('.btn-cancel-edit').on('click', function () {
                if (confirm('Are you sure want to cancel edit this order?')) {
                    $('.form-control.readonly').attr('readonly', 'readonly');
                    $('.btn-edit-order').show();
                    $('.btn-cancel-edit').hide();
                    $('.wrapper-btn-submit').hide();
                    $('.change-image').hide();
                    $('.show-image').show();
                    location.reload();
                }
            });

            //
            $('.size-input').on('input blur', function (e) {
                let rowEle = $(e.target).closest('tr.line-detail');
                let length = $(rowEle).find('.length').val() ?? 0;
                let width = $(rowEle).find('.width').val() ?? 0;
                let height = $(rowEle).find('.height').val() ?? 0;

                let bulky_weight = (length * width * height / 5000).toFixed(2); // gram
                rowEle.find('.bulky_weight').val(bulky_weight);
            });

            $('.weight-input').on('input blur', function (e) {
                let rowEle = $(e.target).closest('tr.line-detail');
                let bulky_weight = $(rowEle).find('.bulky_weight').val() ?? 0;
                let actual_weight = $(rowEle).find('.actual_weight').val() ?? 0;
                let pack_bill_weight = $(rowEle).find('.pack_bill_weight').val() ?? 0;
                let maxWeight = Math.max(bulky_weight, actual_weight, pack_bill_weight);
                rowEle.find('.billable_weight').val(maxWeight);
            });

            $('.price-input').on('input blur', function (e) {
                let rowEle = $(e.target).closest('tr.line-detail');
                let quantity = $(rowEle).find('.quantity').val() ?? 0;
                let unit_price = $(rowEle).find('.unit_price').val() ?? 0;

                let subtotal = (quantity * unit_price).toFixed(2);
                rowEle.find('.subtotal').val(subtotal);
                calculateTotalValue();
            });

            $('input#surcharge_fee').on('input blur', function () {
                calculateTotalValue();
            });

            $('.bill_code_detail').on('mouseout blur', function () {
                checkBillDuplicates();
            });

            $(document).on('mousemove', function () {
                checkBillDuplicates();
            });

            $('.pack_bill_weight').on('change blur', function () {
                calTotalBillWeight();
            });


            /*$('#btn-submit-add-details').click(function (e) {
                e.preventDefault();

                if (true) {
                    $('#form-add-details').submit();
                }
            });*/
        });

        function checkBillDuplicates() {
            let arrBills = [];
            $('.bill_code_detail').each(function () {
                arrBills.push($(this).val().trim());
            });

            //Hàm tìm các phần tử trùng trong arr
            let findDuplicates = arr => arr.filter((item, index) => arr.indexOf(item) !== index);

            let arrDuplicates = findDuplicates(arrBills); // All duplicates

            $('.bill_code_detail').each(function () {
                $(this).next('.error-message').hide();
                let val = $(this).val().trim();
                if (arrDuplicates.includes(val) && val !== '') {
                    $(this).next('.error-message').show();
                }
            });
        }

        function calculateTotalValue () {
            let allSubTotal = 0;
            $('input.subtotal').each(function () {
                allSubTotal += $(this).val() * 1;
            });

            let surcharge_fee = !!$('input#surcharge_fee').val() ? $('input#surcharge_fee').val().trim() : 0;
            $('.total_value').text((allSubTotal + parseFloat(surcharge_fee)).toFixed(2));
        }

        /*function copyLine(ele) {
            let arrIndex = [];

            $('tr.line-detail').each(function () {
                arrIndex.push($(this).attr('data-index'));
            })

            let maxIndex = Math.max(...arrIndex);
            let newIndex = maxIndex + 1;
            let eleClone = $(ele).closest('tr.line-detail').clone(true);
            let currentIndex = $(eleClone).attr('data-index');

            $(eleClone).attr('data-index', newIndex);

            $(eleClone).find('input').each(function () {
                let name = $(this).attr('name');
                name = name.replace('[' + currentIndex + ']', '[' + newIndex + ']');
                $(this).attr('name', name);

                //if ($(this).closest('.bill_code_detail').length == 1) {
                //    $(this).val('');
                //    $(this).next('.error-message').hide();
                //}
            })

            eleClone.appendTo('.body-details');

            countBillDetails();
            calTotalBillWeight();
        }

        function deleteLine(ele) {
            if ($('tr.line-detail').length > 1) {
                let eleToCopy = $(ele).closest('tr.line-detail');
                eleToCopy.remove();

                countBillDetails();
                calTotalBillWeight();
            } else {
                alert('At least one line needs to exist.');
            }
        }

        function addLine(ele) {
            let arrIndex = [];

            $('tr.line-detail').each(function () {
                arrIndex.push($(this).attr('data-index'));
            })

            let maxIndex = Math.max(...arrIndex);
            let newIndex = maxIndex + 1;
            let eleClone = $(ele).closest('tr.line-detail').clone(true);
            let currentIndex = $(eleClone).attr('data-index');

            $(eleClone).find('input').val('');
            $(eleClone).attr('data-index', newIndex);

            $(eleClone).find('input').each(function () {
                let name = $(this).attr('name');
                name = name.replace('[' + currentIndex + ']', '[' + newIndex + ']');
                $(this).attr('name', name);
            })

            eleClone.appendTo('.body-details');

            countBillDetails();
            calTotalBillWeight();
        }*/

        function countBillDetails() {
            $('.count_bill_details').text($('tr.line-detail').length);
        }

        function calTotalBillWeight() {
            let totalBillWeight = 0;
            $('.pack_bill_weight').each(function () {
                totalBillWeight += $(this).val() * 1;
            });
            $('.total_bill_weight').text(totalBillWeight.toFixed(2));
        }
    </script>
@endpush
