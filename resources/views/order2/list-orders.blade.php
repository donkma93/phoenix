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
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h2 class="mb-0">{{ __('List Orders') }}</h2>
                    <a class="btn btn-lg btn-info btn-round"
                       href="{{ route('staff.order2.create') }}">{{ __('Create Order') }}</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive p-0">
                        <table class="table" id="datatable">
                            <thead class="text-primary">
                            <tr>
                                <th class="disabled-sorting">{{ __('ID') }}</th>
                                <th class="disabled-sorting">{{ __('Shipper name') }}</th>
                                <th class="disabled-sorting">{{ __('Shipper phone') }}</th>
                                <th class="disabled-sorting">{{ __('Shipper address') }}</th>
                                <th class="disabled-sorting">{{ __('Receiver name') }}</th>
                                <th class="disabled-sorting">{{ __('Receiver phone') }}</th>
                                <th class="disabled-sorting">{{ __('Receiver address') }}</th>
                                <th class="disabled-sorting">{{ __('Air waybill') }}</th>
                                <th class="disabled-sorting">{{ __('Type') }}</th>
                                <th class="disabled-sorting">{{ __('Service') }}</th>
                                <th class="disabled-sorting">{{ __('Note') }}</th>
                                <th class="disabled-sorting text-center">{{ __('Actions') }}</th>
                            </tr>
                            </thead>
                            <tbody class="body-details">
                            @if(count($orders) > 0)
                                @foreach($orders as $order)
                                    <tr>
                                        <td>
                                            {{ $order->order_id }}
                                        </td>
                                        <td class="mw-200px">
                                            {{ $order->shipper_name }}
                                            {{ $order->shipper_company ? '(' . $order->shipper_company . ')' : '' }}
                                        </td>
                                        <td class="mw-112px">
                                            {{ $order->shipper_phone }}
                                        </td>
                                        <td class="mw-112px">
                                            {{ $order->shipper_address . ', ' . $order->shipper_province . ', ' . $order->shipper_country }}
                                        </td>
                                        <td class="mw-112px">
                                            {{ $order->receiver_name }}
                                            {{ $order->receiver_company ? '(' . $order->receiver_company . ')' : '' }}
                                        </td>
                                        <td class="mw-112px">
                                            {{ $order->receiver_phone }}
                                        </td>
                                        <td class="mw-112px">
                                            {{ $order->receiver_address . ', ' . $order->receiver_province . ', ' . $order->receiver_country }}
                                        </td>
                                        <td class="mw-112px">
                                            {{ $order->air_waybill ? $order->air_waybill : '' }}
                                        </td>
                                        <td style="max-width: 124px;">
                                            {{ $order->commodity }}
                                        </td>
                                        <td style="max-width: 124px;">
                                            {{ $order->service_of_order }}
                                        </td>
                                        <td>
                                            {{ $order->note_total ? $order->note_total : '' }}
                                        </td>
                                        <td class="text-center mw-88px"
                                            style="text-wrap: nowrap; vertical-align: middle;">
                                            @if($order->count_details > 0)
                                                <a href="{{ route('staff.order2.details', ['id' => $order->order_id]) }}"
                                                   title="Details" class="action-btn"><i
                                                        class="nc-icon nc-align-left-2"></i></a>
                                            @else
                                                <a href="{{ route('staff.order2.addDetails', ['id' => $order->order_id]) }}"
                                                   title="Add details" class="action-btn"><i
                                                        class="nc-icon nc-simple-add"></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="11" class="text-center">No data.</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
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
            });


            /*$('#btn-submit-add-details').click(function (e) {
                e.preventDefault();

                if (true) {
                    $('#form-add-details').submit();
                }
            });*/

            // Datatable
            $('#datatable').DataTable({
                "pagingType": "full_numbers",
                "lengthMenu": [
                    // [10, 25, 50, -1],
                    // [10, 25, 50, "All"]
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
            });
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
