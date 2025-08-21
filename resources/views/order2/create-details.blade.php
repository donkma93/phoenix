@extends('layouts.app',[
'class' => '',
'folderActive' => 'order-management-2',
'elementActive' => 'list-orders'
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
    </style>
@endsection

@section('content')
    <div class="content">
        @include('layouts.partials.message')

        <div class="fade-in">
            <div class="card px-4 py-2">
                <div class="card-header">
                    <h2 class="mb-0">{{ __('Add Order Details') }}</h2>
                </div>
                <div class="card-body">
                    <div>
                        <form method="POST" action="" class="form-horizontal prevent-double-click" role="form"
                              enctype="multipart/form-data" id="form-add-details">
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
                                                    <span class="count_bill_details">1</span>
                                                </div>
                                                <div class="col-6 col-xl-3 apx-4 amb-8 mt-3 mt-xl-0">
                                                    <b>{{ __('Total bill weight:') }}</b>
                                                    <span class="total_bill_weight">0</span>
                                                    <span>kg</span>
                                                </div>
                                            </div>
                                            <div class="row amx-n4">
                                                <div class="col-6 col-xl-3 apx-4 amb-8 mt-3">
                                                    <b>{{ __('Created at:') }}</b>
                                                    <span class="">{{ date('d-m-Y H:i', strtotime($order->created_at)) }}</span>
                                                </div>
                                                <div class="col-6 col-xl-3 apx-4 amb-8 mt-3">
                                                    <b>{{ __('User create:') }}</b>
                                                    <span>{{ $order->user_create_email }}</span>
                                                </div>
                                                <div class="col-6 col-xl-3 apx-4 amb-8 mt-3">
                                                    <b>{{ __('User edit:') }}</b>
                                                    <span>{{ $order->user_edit_email }}</span>
                                                </div>
                                                {{--<div class="col-6 col-xl-3 apx-4 amb-8 mt-3">
                                                    <b>{{ __('Surcharge:') }}</b>
                                                    <span class="">{{ isset($order->surcharge) ? json_decode($order->surcharge)->name . ' / ' . json_decode($order->surcharge)->fee . ' USD' : 'N/A' }}</span>
                                                </div>--}}
                                                <div class="col-6 col-xl-3 apx-4 amb-8 mt-3">
                                                    <b>{{ __('Total value:') }}</b>
                                                    <span class="total_value">{{ isset($order->surcharge) ? json_decode($order->surcharge)->fee : 0 }}</span>
                                                    <span>USD</span>
                                                </div>
                                            </div>
                                            <div class="row amx-n4">
                                                <div class="col-12 col-xl-3 apx-4 amb-8 mt-3">
                                                    <b>{{ __('Air waybill No.') }}</b>
                                                    <input
                                                        type="text" class="form-control"
                                                        name="air_waybill"
                                                        value="{{ old('air_waybill') ?? '' }}"
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
                                                    <textarea class="form-control"
                                                              name="note_total">{{ old('note_total') ?? '' }}</textarea>
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
                                                                type="text" class="form-control"
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
                                                                type="number" class="form-control"
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
                                <div class="table-responsive" style="overflow-x: auto;">
                                    <div class="d-flex justify-content-end">
                                        <span style="font-weight: 600;">Unit: cm / kg</span>
                                    </div>
                                    <table class="table" id="datatable">
                                        <thead class="text-primary">
                                        <tr>
                                            {{--<th class="disabled-sorting">{{ __('Mã bill chi tiết') }}</th>--}}
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
                                            <th class="disabled-sorting" style="min-width: 180px;">{{ __('Image') }}</th>
                                            <th class="disabled-sorting" style="min-width: 120px;">{{ __('Ghi chú') }}</th>
                                            <th class="disabled-sorting text-center">{{ __('Actions') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody class="body-details">
                                        <tr class="line-detail" data-index="0">
                                            {{--<td class="mw-200px">
                                                <input class="form-control bill_code_detail" type="text"
                                                       name="details[0][bill_code_detail]">
                                                <p style="text-wrap: nowrap; font-size: 90%; color: red; display: none;"
                                                   class="mt-1 error-message">Bill cannot be duplicated</p>
                                            </td>--}}
                                            <td class="mw-112px">
                                                <input class="form-control" required type="text"
                                                       name="details[0][type_of_commodity]">
                                            </td>
                                            <td class="mw-112px">
                                                <input class="form-control price-input quantity" required type="number"
                                                       min="1" step="1" name="details[0][quantity]">
                                            </td>
                                            <td class="mw-112px">
                                                <input class="form-control price-input unit_price" required type="number"
                                                       min="0" step="0.01" name="details[0][unit_price]">
                                            </td>
                                            <td class="mw-112px">
                                                <input class="form-control subtotal" readonly required type="number"
                                                       min="0" step="0.01" name="details[0][subtotal]">
                                            </td>
                                            <td class="mw-112px">
                                                <input class="form-control size-input length" required type="number"
                                                       min="0.1" step="0.1" name="details[0][length]">
                                            </td>
                                            <td class="mw-112px">
                                                <input class="form-control size-input width" required type="number"
                                                       min="0.1" step="0.1" name="details[0][width]">
                                            </td>
                                            <td class="mw-112px">
                                                <input class="form-control size-input height" required type="number"
                                                       min="0.1" step="0.1" name="details[0][height]">
                                            </td>
                                            <td class="mw-112px">
                                                <input class="form-control weight-input pack_bill_weight" required
                                                       type="number" min="0.01" step="0.01"
                                                       name="details[0][pack_bill_weight]">
                                            </td>
                                            <td class="mw-112px">
                                                <input class="form-control weight-input actual_weight" required
                                                       type="number" min="0.01" step="0.01"
                                                       name="details[0][actual_weight]">
                                            </td>
                                            <td style="max-width: 124px;">
                                                <input class="form-control bulky_weight" required readonly type="number"
                                                       min="0.01" step="0.01" tabindex="-1"
                                                       name="details[0][bulky_weight]">
                                            </td>
                                            <td style="max-width: 124px;">
                                                <input class="form-control billable_weight" required readonly
                                                       type="number" min="0.01" step="0.01" tabindex="-1"
                                                       name="details[0][billable_weight]">
                                            </td>
                                            <td class="mw-200px">
                                                <input class="form-control" type="file" name="details[0][image]">
                                            </td>
                                            <td>
                                                <input class="form-control" type="text" name="details[0][note_detail]">
                                            </td>
                                            <td class="text-center mw-88px"
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
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="search-form-group mt-4">
                                <div class="search-input text-center text-sm-left">
                                    <input class="btn btn-lg btn-info btn-round" id="btn-submit-add-details"
                                           type="submit" value="{{ __('Add Order') }}">
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
        $(document).ready(function () {
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

            $('.pack_bill_weight').on('change', function () {
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

        function copyLine(ele) {
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

                /*if ($(this).closest('.bill_code_detail').length == 1) {
                    $(this).val('');
                    $(this).next('.error-message').hide();
                }*/
            })

            eleClone.appendTo('.body-details');

            countBillDetails();
            calTotalBillWeight();
            calculateTotalValue();
        }

        function deleteLine(ele) {
            if ($('tr.line-detail').length > 1) {
                let eleToCopy = $(ele).closest('tr.line-detail');
                eleToCopy.remove();

                countBillDetails();
                calTotalBillWeight();
                calculateTotalValue();
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
            calculateTotalValue();
        }

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
