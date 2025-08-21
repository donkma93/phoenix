@extends('layouts.app',[
'class' => '',
'folderActive' => 'order-management-2',
'elementActive' => 'orders-report'
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

        .form-horizontal .col-form-label {
            padding: 10px 5px 0 15px;
            text-align: right;
            margin-bottom: 0;
            font-size: .8571em;
            line-height: 1.5;
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

        .table-responsive {
            overflow: unset;
        }

        .min-w-160 {
            min-width: 160px !important;
        }

        .btn + .btn {
            margin-left: 20px !important;
        }

        table .btn + .btn {
            margin-left: 0 !important;
            margin-top: 8px !important;
        }
    </style>
@endsection

@section('content')
    <div class="content">
        @include('layouts.partials.message')

        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="card ">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="card-title">{{ __('Report') }}</h4>
                        <a class="btn btn-round btn-success"
                           href="{{ route('staff.order2.create') }}">{{ __('Create Order') }}</a>
                    </div>

                    <div class="card-body ">
                        <form method="get" action="" class="form-horizontal" id="order_list_search_form">
                            <div class="row justify-content-center">
                                <div class="col-xl-4 col-12">
                                    <div class="row">
                                        <label class="col-sm-4 col-form-label">From date</label>
                                        <div class="col-sm-8">
                                            <div class="form-group">
                                                <input type="text" class="form-control datepicker" id="date_from"
                                                       value="{{ request('date_from') ?? date('Y-m-d', strtotime('-1 month')) }}"
                                                       name="date_from" placeholder="YYYY-MM-DD"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-12">
                                    <div class="row">
                                        <label class="col-sm-4 col-form-label">To date</label>
                                        <div class="col-sm-8">
                                            <div class="form-group">
                                                <input type="text" class="form-control datepicker" id="date_to"
                                                       value="{{ request('date_to') ?? date('Y-m-d') }}"
                                                       name="date_to" placeholder="YYYY-MM-DD"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-12 col-12">
                                    <div class="d-flex justify-content-center my-3">
                                        <button type="submit" class="btn btn-info btn-round min-w-160 m-0">Search
                                        </button>
                                        {{--<a id="btnExport" class="btn btn-info btn-round m-0 min-w-160"
                                           href="{{ route('staff.staffOrderExport', ['datefrom' => 0, 'dateto' => 0]) }}">Export</a>--}}
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="fade-in">
                    <div class="card px-4 py-2">
                        <div class="card-body">
                            <div class="table-responsive p-0" style="overflow-x: auto;">
                                <div class="d-flex justify-content-end">
                                    <span style="font-weight: 600;">Unit: cm / g</span>
                                </div>
                                <table class="table" id="datatable">
                                    <thead class="text-primary">
                                    <tr>
                                        <th class="disabled-sorting">{{ __('Id') }}</th>
                                        <th class="disabled-sorting">{{ __('Date created') }}</th>
                                        <th class="disabled-sorting">{{ __('Shipper name') }}</th>
                                        <th class="disabled-sorting">{{ __('Receiver name') }}</th>
                                        <th class="disabled-sorting">{{ __('Air waybill') }}</th>
                                        <th class="disabled-sorting">{{ __('Pack bill') }}</th>
                                        <th class="disabled-sorting">{{ __('Destination') }}</th>
                                        <th class="disabled-sorting">{{ __('Type') }}</th>
                                        <th class="disabled-sorting">{{ __('Service') }}</th>
                                        <th class="disabled-sorting">{{ __('Item name') }}</th>
                                        <th class="disabled-sorting">{{ __('Quantity') }}</th>
                                        <th class="disabled-sorting">{{ __('Unit pr.') }}</th>
                                        <th class="disabled-sorting">{{ __('Subtotal') }}</th>
                                        <th class="disabled-sorting">{{ __('Total wt.') }}</th>
                                        <th class="disabled-sorting">{{ __('Pack wt.') }}</th>
                                        <th class="disabled-sorting">{{ __('Image') }}</th>
                                        <th class="disabled-sorting">{{ __('Actual wt.') }}</th>
                                        <th class="disabled-sorting">{{ __('Length') }}</th>
                                        <th class="disabled-sorting">{{ __('Width') }}</th>
                                        <th class="disabled-sorting">{{ __('Height') }}</th>
                                        <th class="disabled-sorting">{{ __('Bulky wt.') }}</th>
                                        <th class="disabled-sorting">{{ __('Billable wt.') }}</th>
                                        <th class="disabled-sorting">{{ __('Pack note') }}</th>
                                        <th class="disabled-sorting text-center">{{ __('Actions') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody class="body-details">
                                    @if(count($orders) > 0)
                                        @foreach($orders as $order)
                                            <tr>
                                                <td>
                                                    {{ $order->order_detail_id }}
                                                </td>
                                                <td>
                                                    {{ date("d-m-Y", strtotime($order->created_at)) }}
                                                </td>
                                                <td>
                                                    {{ $order->shipper_name }}
                                                    {{ $order->shipper_company ? '(' . $order->shipper_company . ')' : '' }}
                                                </td>
                                                <td>
                                                    {{ $order->receiver_name }}
                                                    {{ $order->receiver_company ? '(' . $order->receiver_company . ')' : '' }}
                                                </td>
                                                <td>
                                                    {{ $order->air_waybill ? $order->air_waybill : '' }}
                                                </td>
                                                <td>
                                                    {{ $order->bill_code_detail }}
                                                </td>
                                                <td>
                                                    {{ ucwords($order->receiver_country) }}
                                                </td>
                                                <td>
                                                    {{ $order->commodity }}
                                                </td>
                                                <td>
                                                    {{ ucwords($order->service_of_order) }}
                                                </td>
                                                <td>
                                                    {{ ucfirst($order->type_of_commodity) }}
                                                </td>
                                                <td style="text-align: center;">
                                                    {{ $order->quantity }}
                                                </td>
                                                <td style="text-align: center;">
                                                    {{ $order->unit_price }}
                                                </td>
                                                <td style="text-align: center;">
                                                    {{ isset($order->unit_price) && isset($order->quantity) ? round($order->quantity * $order->unit_price, 2) . ' USD' : '' }}
                                                </td>
                                                <td style="text-align: center;">
                                                    {{ $order->total_bill_weight }}
                                                </td>
                                                <td style="text-align: center;">
                                                    {{ $order->pack_bill_weight }}
                                                </td>
                                                <td style="text-align: center; vertical-align: middle;">
                                                    @if($order->img_url)
                                                        <a href="{{ url($order->img_url) }}" target="_blank" class="fancybox show-image">
                                                            <i class="nc-icon nc-album-2" style="font-size: 24px;"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                                <td style="text-align: center;">
                                                    {{ $order->actual_weight }}
                                                </td>
                                                <td style="text-align: center;">
                                                    {{ number_format($order->length, 1) }}
                                                </td>
                                                <td style="text-align: center;">
                                                    {{ number_format($order->width, 1) }}
                                                </td>
                                                <td style="text-align: center;">
                                                    {{ number_format($order->height, 1) }}
                                                </td>
                                                <td style="text-align: center;">
                                                    {{ $order->bulky_weight }}
                                                </td>
                                                <td style="text-align: center;">
                                                    {{ $order->billable_weight }}
                                                </td>
                                                <td>
                                                    {{ ucfirst($order->note_detail ?? '') }}
                                                </td>
                                                <td class="text-center"
                                                    style="text-wrap: nowrap; vertical-align: middle;">
                                                    <a href="{{ route('staff.order2.details', ['id' => $order->order_id]) }}"
                                                       title="Details" class="action-btn"><i
                                                            class="nc-icon nc-align-left-2"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="21" class="text-center">No data.</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                {{ $orders->appends(request()->all())->links() }}
                            </div>
                        </div>
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
            // Fancybox
            $('.fancybox').fancybox();


            /* datepicker */
            // format date, default is MM/DD/YYYY
            demo.date_format = 'YYYY-MM-DD';

            // initialise Datetimepicker and Sliders
            demo.initDateTimePicker();

            if ($('.slider').length != 0) {
                demo.initSliders();
            }

            //
            /*$('.size-input').on('input blur', function (e) {
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

            $('.bill_code_detail').on('mouseout blur', function (e) {
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
            });

            $('.pack_bill_weight').on('change blur', function () {
                calTotalBillWeight();
            });*/


            /*$('#btn-submit-add-details').click(function (e) {
                e.preventDefault();

                if (true) {
                    $('#form-add-details').submit();
                }
            });*/


            // Datatable
            /*$('#datatable').DataTable({
                "pagingType": "full_numbers",
                "lengthMenu": [
                    [10, 15, 25, 40],
                    [10, 15, 25, 40]
                ],
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search records",
                },
                "aaSorting": [],
                // "ordering": false,
            });*/
        });

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
        }

        function countBillDetails() {
            $('.count_bill_details').text($('.bill_code_detail').length);
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
