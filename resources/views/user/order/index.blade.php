@extends('layouts.app', [
    'class' => '',
    'folderActive' => '',
    'elementActive' => 'order',
])

@section('styles')
    <style>
        .table-responsive {
            overflow: unset;
        }

        .min-w-160 {
            min-width: 160px;
        }

        .card .card-footer {
            border-top: 1px solid #d8dbe0 !important;
        }

        .card .card-header {
            border-bottom: 1px solid #d8dbe0 !important;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .search-form-group {
            display: flex;
            flex-direction: column;
        }

        .search-form-group .search-input {
            flex: 1;
        }

        table.dataTable thead>tr>th {
            /*text-wrap: nowrap;*/
        }

        table.dataTable thead>tr>td.sorting,
        table.dataTable thead>tr>td.sorting_asc,
        table.dataTable thead>tr>td.sorting_desc,
        table.dataTable thead>tr>th.sorting,
        table.dataTable thead>tr>th.sorting_asc,
        table.dataTable thead>tr>th.sorting_desc {
            padding-left: 8px !important;
            padding-right: 8px !important;
        }

        @media (min-width: 576px) {
            .search-form-group {
                flex-direction: row;
            }

            .search-form-group .search-input {
                max-width: 360px;
            }
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

@section('content')
    @php
        $fields = [__('#'), __('Order Number'), __('Recipient Information'), __('Product Information'), __('Package Information'), __('Provider'),/* __('Partner Code'),*/ __('Created At'), __('Tracking'), ''];
    @endphp
    <div class="content">
        <div class="fade-in">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">{{ __('Order list') }}</h2>
                    <a class="btn btn-success btn-round" href="{{ route('orders.create') }}">
                        {{ __('New Order') }}
                    </a>
                </div>
                <div class="card-body">
                    <div class="wrap-form my-5 row justify-content-center">
                        <div class="col-xl-6">
                            <form method="GET" action="{{ route('orders.index') }}" class="form-horizontal"
                                role="form">
                                <div class="row">
                                    <div class="col-xl-6 col-12">
                                        <div class="row">
                                            <label class="col-sm-4 col-form-label">From date</label>
                                            <div class="col-sm-8">
                                                <div class="form-group">
                                                    <input type="text" class="form-control datepicker" id="date_from"
                                                        value="@if (isset($oldInput['date_from'])) {{ $oldInput['date_from'] }} @else {{ date('Y-m-d', strtotime('-1 month')) }} @endif"
                                                        name="date_from" placeholder="YYYY-MM-DD" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-12">
                                        <div class="row">
                                            <label class="col-sm-4 col-form-label">To date</label>
                                            <div class="col-sm-8">
                                                <div class="form-group">
                                                    <input type="text" class="form-control datepicker" id="date_to"
                                                        value="@if (isset($oldInput['date_to'])) {{ $oldInput['date_to'] }} @else {{ date('Y-m-d') }} @endif"
                                                        name="date_to" placeholder="YYYY-MM-DD" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="row justify-content-center">
                                            <input class="btn btn-info btn-round m-0 min-w-160" type="submit"
                                                value="{{ __('Search') }}">
                                            <a id="btnExport" class="btn btn-info btn-round m-0 ml-5 min-w-160"
                                                href="{{ route('userOrderExport', ['datefrom' => 0, 'dateto' => 0]) }}">Export</a>
                                        </div>
                                    </div>
                                </div>

                                {{--
                        <div class="form-group search-form-group">
                            <label for="name" class="col-form-label search-label"><b>{{ __('Name') }}</b></label>
                            <div class="search-input">
                                <input type="input" class="form-control w-100" name="name" value="@if (isset($oldInput['name'])){{$oldInput['name']}}@endif" />
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label for="status"
                                class="col-form-label search-label min-w-160 text-left"><b>{{ __('Type') }}</b></label>
                            <div class="search-input">
                                <select id="status" name="status" class="form-control w-100">
                                    <option selected></option>
                                    @foreach (App\Models\Order::$statusName as $value => $status)
                                        <option value="{{ $value }}"
                                            @if (isset($oldInput['status']) && $oldInput['status'] == $value) selected="selected" @endif>
                                            {{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label for="payment"
                                class="col-form-label search-label min-w-160 text-left"><b>{{ __('Payment Status') }}</b></label>
                            <div class="search-input">
                                <select id="payment" name="payment" class="form-control w-100">
                                    <option selected></option>
                                    @foreach (App\Models\Order::$paymentName as $value => $status)
                                        <option value="{{ $value }}"
                                            @if (isset($oldInput['payment']) && $oldInput['payment'] == $value) selected="selected" @endif>
                                            {{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label for="fulfillment"
                                class="col-form-label search-label min-w-160 text-left"><b>{{ __('Fulfillment Status') }}</b></label>
                            <div class="search-input">
                                <select id="fulfillment" name="fulfillment" class="form-control w-100">
                                    <option selected></option>
                                    @foreach (App\Models\Order::$fulfillName as $value => $status)
                                        <option value="{{ $value }}"
                                            @if (isset($oldInput['fulfillment']) && $oldInput['fulfillment'] == $value) selected="selected" @endif>
                                            {{ ucfirst($status) }}</option>
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
                        --}}
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    @if (count($orders))
                        <div class="table-responsive px-0">
                            <form target="_blank" method="get" action="{{ route('pickup.detail.print') }}" id="form_print_label_list">
                            <table class="table datatable"
                                id="user-package-table">
                                <thead>
                                    <tr class="text-primary">
                                        {{--@foreach ($fields as $field)
                                            <th>{{ $field }}</th>
                                        @endforeach--}}
                                        <th>{{ __('Order Code') }}</th>
                                        <th style="max-width: 150px;">{{ __("Customer's Order") }}</th>
                                        <th style="max-width: 150px;">{{ __("Receiver Name") }}</th>
                                        <th class="disabled-sorting" style="max-width: 200px;">{{ __('Recipient') }}</th>
                                        <th class="disabled-sorting">{{ __('Package Info') }}</th>
                                        <th style="max-width: 150px;">{{ __('Item') }}</th>
                                        <th style="max-width: 150px;">{{ __('Amount') }}</th>
                                        {{--<th style="max-width: 100px;">{{ __('Provider') }}</th>--}}
                                        <th style="max-width: 112px;">{{ __('Created At') }}</th>
                                        <th>{{ __('Tracking') }}</th>
                                        <th class="disabled-sorting" style="max-width: 100px;">
                                            <button class="btn btn-round btn-sm btn-success print-label-list"
                                                    type="submit">
                                                {{ __('Print List') }}
                                            </button>
                                        </th>
                                        <th class="disabled-sorting">
                                            <input id="select_all_order" class="" type="checkbox"
                                                   name="" value="">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            </form>
                        </div>
                        {{-- Server-side DataTable handles pagination & search smoothly for large accounts --}}
                    @else
                        <div class="text-center">{{ __('No data.') }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-body" id="preview-barcode">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form target="_blank" method="get" action="{{ route('pickup.detail.print') }}" id="form_print_label">
        <input class="label_list" type="hidden"
               name="label_list[]" value="">
    </form>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            /* datepicker */
            // format date, default is MM/DD/YYYY
            demo.date_format = 'YYYY-MM-DD';

            // initialise Datetimepicker and Sliders
            demo.initDateTimePicker();

            if ($('.slider').length != 0) {
                demo.initSliders();
            }

            /* export excel */
            let date_from = $('#date_from').val();
            let date_to = $('#date_to').val();
            let url_export = "{{ route('userOrderExport', ['datefrom' => 0, 'dateto' => 0]) }}";
            const myArray = url_export.split("/");
            myArray[myArray.length-2] = date_from;
            myArray[myArray.length-1] = date_to;
            let new_url_export = myArray.join('/');
            $('a#btnExport').attr("href", new_url_export);

            $('#date_from').on('change blur', function() {
                let date = $(this).val();
                let elink = $('a#btnExport').attr('href');
                const myArray = elink.split("/");
                let oLink = "{{ route('userOrderExport', ['datefrom' => 0, 'dateto' => 0]) }}";
                let dt = date
                let df = myArray[myArray.length - 1];
                let llink = oLink.slice(0, oLink.length - 3) + dt + "/" + df

                $('a#btnExport').attr("href", llink);
            });

            $('#date_to').on('change blur', function() {
                let date = $(this).val();
                let elink = $('a#btnExport').attr('href');
                const myArray = elink.split("/");
                let oLink = "{{ route('userOrderExport', ['datefrom' => 0, 'dateto' => 0]) }}";
                let dt = myArray[myArray.length - 2];
                let df = date;
                let llink = oLink.slice(0, oLink.length - 3) + dt + "/" + df

                $('a#btnExport').attr("href", llink);
            });


            // Server-side DataTable (prevents DOM freeze for accounts with tens of thousands of orders)
            if ($('#user-package-table').length) {
                $('#user-package-table').DataTable({
                    "pagingType": "full_numbers",
                    "lengthMenu": [
                        [50, 25, 10],
                        [50, 25, 10]
                    ],
                    responsive: true,
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search records",
                    },
                    "aaSorting": [],
                    processing: true,
                    serverSide: true,
                    searching: true,
                    searchDelay: 500,
                    ajax: {
                        url: "{{ route('orders.datatable') }}",
                        data: function (d) {
                            d.date_from = $.trim($('#date_from').val());
                            d.date_to = $.trim($('#date_to').val());
                        }
                    },
                    columns: [
                        { data: 'order_code', orderable: false },
                        { data: 'order_number', orderable: false },
                        { data: 'receiver_name', orderable: false },
                        { data: 'recipient', orderable: false },
                        { data: 'package_info', orderable: false, className: 'text-left' },
                        { data: 'item', orderable: false, className: 'text-left' },
                        { data: 'amount', orderable: false },
                        { data: 'created_at', orderable: false, className: 'text-center' },
                        { data: 'tracking', orderable: false, className: 'text-left' },
                        { data: 'actions', orderable: false, searchable: false },
                        { data: 'checkbox', orderable: false, searchable: false }
                    ]
                });
            }

            // Select all handler (delegated for server-side rows)
            $(document).on('change', '#select_all_order', function () {
                $('.select_item_order').prop('checked', $(this).is(':checked'));
            });

            // Print single label (delegated so it works across AJAX DataTable pages)
            $(document).on('click', '.print-label', function () {
                let order_code = $(this).data('order-code');
                $('input.label_list').val(order_code);
                $('form#form_print_label').submit();
            });

            $('.print-label-list').click(function (e) {
                e.preventDefault();
                let list_label = [];
                $('.select_item_order').each(function () {
                    if ($(this).prop('checked') === true) {
                        list_label.push($(this).val());
                    }
                });
                if (list_label.length === 0) {
                    alert('Please select at least one record to print!');
                } else {
                    $('form#form_print_label_list').submit();
                }
            });
        });
    </script>
@endpush
