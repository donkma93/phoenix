@extends('layouts.app', [
    'class' => '',
    'folderActive' => 'order_management',
    'elementActive' => 'orders',
])

@section('styles')
    <style>
        .table-responsive {
            overflow: unset;
        }

        .min-w-160 {
            min-width: 160px !important;
        }

        .min-w-60 {
            min-width: 60px !important;
        }

        .list_bill_status {
            height: 100%;
        }

        .list_bill_status .item_bill_status {
            position: relative;
            color: #495057;
            cursor: pointer;
            padding: 4px 6px !important;
            border: 1px solid rgba(0, 0, 0, 0.125);
            border-radius: 16px;
        }

        .list_bill_status .item_bill_status>.label_bill_status {
            min-width: 32px;
        }

        .list_bill_status .item_bill_status>input[name=bill_status] {
            position: absolute;
            visibility: hidden;
        }

        .list_bill_status .item_bill_status.bill_selected {
            border-color: #FF0080;
            background-color: #FF0080;
            color: #fff;
        }

        .list_bill_status .item_bill_status:not(:last-child) {
            margin-right: 8px;
        }

        .list_bill_status .item_bill_status>.badge {
            border-radius: 50%;
            position: absolute;
            top: -24px;
            right: -8px;
            width: 32px;
            height: 32px;
            padding: 4px;
            line-height: 24px;
            text-align: center;
            color: #333;
            background-color: #82d616;
            border: unset;
        }

        .btn + .btn {
            margin-left: 20px !important;
        }

        table .btn + .btn {
            margin-left: 0 !important;
            margin-top: 8px !important;
        }

        table.dataTable thead>tr>th {
            text-wrap: nowrap;
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
    </style>
@endsection

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
        @if (session('warning'))
            <div class="row justify-content-end">
                <div class="col-md-4">
                    <div class="alert alert-warning alert-dismissible fade show">
                        <button type="button" aria-hidden="true" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="nc-icon nc-simple-remove"></i>
                        </button>
                        <span>
                            <b> Warning - </b>
                            {{ session('warning') }}
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
        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="card ">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="card-title">Orders list</h4>
                        <button data-toggle="modal" data-target="#myModalEmail" class="btn btn-round btn-success">
                            {{ __('New Order') }}
                        </button>
                    </div>

                    <div class="card-body ">
                        <form method="get" action="" class="form-horizontal" id="order_list_search_form">
                            <div class="row">
                                <div class="col-12">
                                    <ul class="list-group text-xs flex-row list_bill_status justify-content-center">
                                        @foreach($tracking_status as $k=>$v)
                                            <li
                                                class="list-group-item d-flex justify-content-between align-items-center item_bill_status {{ old('bill_status') !== null && old('bill_status') * 1 === $k * 1 ? 'bill_selected' : '' }}" >
                                                <span class="text-center label_bill_status min-w-60">{{ $v }}</span>
                                                @if ($k != config('app.tracking_status_all'))
                                                    <span class="badge badge-pill">
                                                        {{ $count_status[$k] ?? 0 }}
                                                    </span>
                                                @endif
                                                <input type="radio" name="bill_status" value="{{ $k }}"
                                                    {{ old('bill_status') !== null && old('bill_status') * 1 === $k * 1 ? 'checked' : '' }}>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <hr>
                            <div class="row justify-content-center">
                                <div class="col-xl-4 col-12">
                                    <div class="row">
                                        <label class="col-sm-4 col-form-label">From date</label>
                                        <div class="col-sm-8">
                                            <div class="form-group">
                                                <input type="text" class="form-control datepicker" id="date_from"
                                                    value="{{ old('date_from') ?? date('Y-m-d', strtotime('-1 month')) }}"
                                                    name="date_from" placeholder="YYYY-MM-DD" />
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
                                                    value="{{ old('date_to') ?? date('Y-m-d') }}"
                                                    name="date_to" placeholder="YYYY-MM-DD" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-12 col-12">
                                    <div class="d-flex justify-content-center my-3">
                                        <button type="submit" class="btn btn-info btn-round min-w-160 m-0">Search</button>
                                        <a id="btnExport" class="btn btn-info btn-round m-0 min-w-160"
                                            href="{{ route('staff.staffOrderExport', ['datefrom' => 0, 'dateto' => 0]) }}">Export</a>
                                    </div>
                                </div>
                            </div>

                            {{--
                            <hr>
                            <div class="row">
                                <div class="col-xl-4 col-12 col-12">
                                    <div class="row">
                                        <label class="col-sm-4 col-form-label">Email</label>
                                        <div class="col-sm-8">
                                            <div class="form-group">
                                                <input type="text" id="email-input" name="email" class="form-control"
                                                    value="@if (isset($oldInput['email'])) {{ $oldInput['email'] }} @endif"
                                                    autocomplete="off" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-12">
                                    <div class="row">
                                        <label class="col-sm-4 col-form-label">Type</label>
                                        <div class="col-sm-8">
                                            <div class="form-group">
                                                <select class="form-control" name="status" id="status">
                                                    <option selected></option>
                                                    @foreach (App\Models\Order::$statusName as $value => $status)
                                                        <option value="{{ $value }}"
                                                            @if (isset($oldInput['status']) && $oldInput['status'] == $value) selected="selected" @endif>
                                                            {{ ucfirst($status) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-12">
                                    <div class="row justify-content-center">
                                        <button type="submit" class="btn btn-info btn-round min-w-160 m-0">Search</button>
                                    </div>
                                </div>
                            </div>
                            --}}
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-footer">
                        <div class="mb-3 d-flex justify-content-end">
                            <form id="bulk-download-form" method="POST" action="{{ route('staff.orders.downloadPreviews') }}">
                                @csrf
                                <input type="hidden" name="order_ids" id="bulk-order-ids" value="">
                                <button type="submit" class="btn btn-primary btn-round" id="btn-download-previews">Download Previews</button>
                            </form>
                        </div>
                        @if (count($orders))
                            <div class="table-responsive">
                                <table class="table" id="datatable">
                                    <thead class="text-primary">
                                        <tr>
                                            <th class="text-center"><input type="checkbox" id="select-all"></th>
                                            <th>{{ __('ID') }}</th>
                                            <th>{{ __('Order Code') }}</th>
                                            <th>{{ __("Customer's Order") }}</th>
                                            {{--<th>{{ __("Receiver Name") }}</th>--}}
                                            <th>{{ __('Receiver') }}</th>
                                            <th>{{ __('Item') }}</th>
                                            {{--<th>{{ __('Package info') }}</th>--}}
                                            <th>{{ __('Amount') }}</th>
                                            <th>{{ __('Create Date') }}</th>
                                            <th>{{ __('Tracking Number') }}</th>
                                            <th class="disabled-sorting text-center">{{ __('Action') }}</th>
                                            {{--<th class="disabled-sorting"></th>--}}
                                            <th class="disabled-sorting"></th>
                                            {{--<th class="disabled-sorting"></th>--}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            {{--<div class="d-flex justify-content-center justify-content-md-end amt-16">
                                {{ $orders->appends(request()->all())->links() }}
                            </div>--}}
                        @else
                            <div class="text-center">{{ __('No data.') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>






    <!-- Classic Modal -->
    <div class="modal fade bd-example-modal-lg" id="myModalEmail" tabindex="-1" role="dialog"
        aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header justify-content-center">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="nc-icon nc-simple-remove"></i>
                    </button>
                    <h4 class="title title-up m-0">{{ __('Select Email') }}</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group search-form-group">
                        <label for="type" class="col-form-label search-label"><b>{{ __('Email') }}</b></label>
                        <div class="search-input position-relative">
                            <input type="input" class="form-control w-100" id="email-input-create"
                                list="dropdown-email-create" name="email"
                                value="@if (isset($oldInput['email'])) {{ $oldInput['email'] }} @endif"
                                autocomplete="off" />
                            <p class="text-danger mb-0" id="error-message">

                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-start">
                    <div class="left-side">
                        <div class="btn btn-round btn-success" onclick="redirectToNewPage()">
                            {{ __('New Order') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="preview-label" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body" id="preview-barcode">
                </div>
            </div>
        </div>
    </div>


@endsection

@push('scripts')
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script> --}}
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.4/jspdf.min.js"></script>

    <script>
        /**/
        let emails = @php echo json_encode($emails) @endphp;
        let users = @php echo json_encode($users) @endphp;

        // createSuggestBlock(document.getElementById("email-input"), emails, 'dropdown-email');
        createSuggestBlock(document.getElementById("email-input-create"), emails, 'dropdown-email-create');

        function redirectToNewPage() {


            const value = $('#email-input-create').val();

            if (!value) {
                $('#email-input-create').addClass('is-invalid');
                $('#error-message').text('Email required!');

                return;
            }

            const user = users.find(item => {
                return item.email == value;
            });

            if (!user) {
                $('#email-input-create').addClass('is-invalid');
                $('#error-message').text('Email incorrect!');

                return;
            }

            let url = "{{ route('staff.orders.new', ['id' => 'id']) }}"
            url = url.replace('id', user.id);

            window.location.href = url
        }

        function previewPDF(file) {
            const jsPDF = window.jsPDF;
            const splitFile = file.split('.');
            const fileType = splitFile[splitFile.length - 1];
            const validImageTypes = ['gif', 'jpeg', 'png', 'tiff', 'jpg', 'heif'];

            let imgSrc;
            if (validImageTypes.includes(fileType)) {
                let doc = new jsPDF("p", "mm", "a4");
                let width = doc.internal.pageSize.getWidth();
                let height = doc.internal.pageSize.getHeight();
                doc.addImage(file, 'JPEG', 10, 10, width, height);
                imgSrc = doc.output('bloburl');
            } else {
                imgSrc = file
            }

            $("#preview-barcode").find("embed").remove();
            let embed = "<embed src=" + imgSrc +
                " frameborder='0' width='100%' height='500px' headers='test' type='application/pdf' class='preview-pdf'>"
            $("#preview-barcode").append(embed);


        }


        // Delete order, label, hold order
        function deleteOrder (order_id) {
            let is_confirm = confirm('Do you want to delete this order?');
            if (is_confirm && !!order_id) {
                $.ajax({
                    url: '/staff/orders/delete-order/' + order_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function (res) {
                        console.log(res)
                        if (!!res.status && res.status === 'error') {
                            alert(res.message);
                        } else if (!!res.status && res.status === 'success') {
                            alert(res.message);
                            location.reload();
                        } else {
                            alert('An error occurred, please check again!');
                        }
                    },
                    error: function (err) {
                        console.log(err);
                    }
                })
            }
        }

        function deleteLabel (order_id) {
            let is_confirm = confirm('Do you want to delete this label?');
            if (is_confirm && !!order_id) {
                $.ajax({
                    url: '/staff/orders/delete-label/' + order_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function (res) {
                        console.log(res)
                        if (!!res.status && res.status === 'error') {
                            alert(res.message);
                        } else if (!!res.status && res.status === 'success') {
                            alert(res.message);
                            location.reload();
                        } else {
                            alert('An error occurred, please check again!');
                        }
                    },
                    error: function (err) {
                        console.log(err);
                    }
                })
            }
        }

        function holdOrder (order_id) {
            let is_confirm = confirm('Do you want to hold this order?');
            if (is_confirm && !!order_id) {
                $.ajax({
                    url: '/staff/orders/hold-order/' + order_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function (res) {
                        console.log(res)
                        if (!!res.status && res.status === 'error') {
                            alert(res.message);
                        } else if (!!res.status && res.status === 'success') {
                            alert(res.message);
                            location.reload();
                        } else {
                            alert('An error occurred, please check again!');
                        }
                    },
                    error: function (err) {
                        console.log(err);
                    }
                })
            }
        }

        function resumeOrder (order_id) {
            let is_confirm = confirm('Do you want to resume this order?')
            if (is_confirm && !!order_id) {
                $.ajax({
                    url: '/staff/orders/resume-order/' + order_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function (res) {
                        console.log(res)
                        if (!!res.status && res.status === 'error') {
                            alert(res.message)
                        } else if (!!res.status && res.status === 'success') {
                            alert(res.message)
                            location.reload();
                        } else {
                            alert('An error occurred, please check again!')
                        }
                    },
                    error: function (err) {
                        console.log(err)
                    }
                })
            }
        }



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
            let url_export = "{{ route('staff.staffOrderExport', ['datefrom' => 0, 'dateto' => 0]) }}";
            const myArray = url_export.split("/");
            myArray[myArray.length-2] = date_from;
            myArray[myArray.length-1] = date_to;
            let new_url_export = myArray.join('/');
            $('a#btnExport').attr("href", new_url_export);


            $('#date_from').on('change blur', function() {
                let date = $(this).val();
                let elink = $('a#btnExport').attr('href');
                const myArray = elink.split("/");
                let oLink = "{{ route('staff.staffOrderExport', ['datefrom' => 0, 'dateto' => 0]) }}";
                let dt = date
                let df = myArray[myArray.length - 1];
                let llink = oLink.slice(0, oLink.length - 3) + dt + "/" + df

                $('a#btnExport').attr("href", llink);
            });

            $('#date_to').on('change blur', function() {
                let date = $(this).val();
                let elink = $('a#btnExport').attr('href');
                const myArray = elink.split("/");
                let oLink = "{{ route('staff.staffOrderExport', ['datefrom' => 0, 'dateto' => 0]) }}";
                let dt = myArray[myArray.length - 2];
                let df = date;
                let llink = oLink.slice(0, oLink.length - 3) + dt + "/" + df

                $('a#btnExport').attr("href", llink);
            });



            /* bill status */
            $('.item_bill_status').click(function() {
                /*if (!$(this).hasClass("bill_selected")) {
                    $('.item_bill_status').each(function () {
                        $(this).removeClass("bill_selected");
                    })
                }*/

                $('.item_bill_status').each(function() {
                    $(this).removeClass("bill_selected");
                })

                $(this).toggleClass("bill_selected");

                $('.item_bill_status.bill_selected>input[name=bill_status]').prop('checked', true);

                $('#datatable').DataTable().ajax.reload();
            })


            // Datatable server-side
            $('#datatable').DataTable({
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
                ajax: {
                    url: "{{ route('staff.orders.datatable') }}",
                    data: function (d) {
                        d.date_from = $('#date_from').val();
                        d.date_to = $('#date_to').val();
                        const billStatusEl = document.querySelector('.item_bill_status.bill_selected>input[name=bill_status]');
                        d.bill_status = billStatusEl ? billStatusEl.value : '{{ config('app.tracking_status_all') }}';
                    }
                },
                columns: [
                    { data: 'checkbox', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'id', searchable: false },
                    { data: 'order_code', searchable: true },
                    { data: 'customer', searchable: false },
                    { data: 'receiver', searchable: false },
                    { data: 'item', searchable: false },
                    { data: 'amount', searchable: false },
                    { data: 'created_at', searchable: false },
                    { data: 'tracking', searchable: false },
                    { data: 'actions', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'extra', orderable: false, searchable: false },
                ],
                createdRow: function(row, data) {
                    $('td', row).eq(0).html(data.checkbox);
                    $('td', row).eq(2).html('<p title="BILL" style="margin: 0">' + data.order_code + '</p>');
                    $('td', row).eq(3).html(data.customer);
                    $('td', row).eq(4).html(data.receiver);
                    $('td', row).eq(5).html(data.item);
                    $('td', row).eq(8).html(data.tracking);
                    $('td', row).eq(9).html(data.actions);
                    $('td', row).eq(10).html(data.extra);
                }
            });


            // Select all handler
            $(document).on('change', '#select-all', function() {
                const checked = $(this).is(':checked');
                $('.order-checkbox').prop('checked', checked);
            });

            // Row checkbox sync with select-all
            $(document).on('change', '.order-checkbox', function() {
                const total = $('.order-checkbox').length;
                const selected = $('.order-checkbox:checked').length;
                $('#select-all').prop('checked', total > 0 && selected === total);
                if (selected < total) {
                    $('#select-all').prop('indeterminate', selected > 0);
                } else {
                    $('#select-all').prop('indeterminate', false);
                }
            });

            // Bulk download: gather selected IDs into hidden input as JSON
            $('#bulk-download-form').on('submit', function(e) {
                const ids = $('.order-checkbox:checked').map(function(){ return $(this).val(); }).get();
                if (ids.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one order.');
                    return false;
                }
                $('#bulk-order-ids').val(JSON.stringify(ids));
            });

            // Delete order, label
            /*$('.delete-order').click(function () {
                let is_confirm = confirm('Do you want to delete this order?')
                let order_id = $(this).data('order-id');
                if (is_confirm && !!order_id) {
                    $.ajax({
                        url: '/staff/orders/delete-order/' + order_id,
                        type: 'GET',
                        dataType: 'json',
                        success: function (res) {
                            if (!!res.status && res.status === 'error') {
                                alert(res.message)
                            } else if (!!res.status && res.status === 'success') {
                                alert(res.message)
                                location.reload();
                            } else {
                                alert('An error occurred, please check again!')
                            }
                        },
                        error: function (err) {
                            console.log(err)
                        }
                    })
                }
            })

            $('.hold-order').click(function () {
                let is_confirm = confirm('Do you want to hold this order?')
                let order_id = $(this).data('order-id');
                if (is_confirm && !!order_id) {
                    $.ajax({
                        url: '/staff/orders/hold-order/' + order_id,
                        type: 'GET',
                        dataType: 'json',
                        success: function (res) {
                            if (!!res.status && res.status === 'error') {
                                alert(res.message)
                            } else if (!!res.status && res.status === 'success') {
                                alert(res.message)
                                location.reload();
                            } else {
                                alert('An error occurred, please check again!')
                            }
                        },
                        error: function (err) {
                            console.log(err)
                        }
                    })
                }
            })

            $('.resume-order').click(function () {
                let is_confirm = confirm('Do you want to resume this order?')
                let order_id = $(this).data('order-id');
                if (is_confirm && !!order_id) {
                    $.ajax({
                        url: '/staff/orders/resume-order/' + order_id,
                        type: 'GET',
                        dataType: 'json',
                        success: function (res) {
                            if (!!res.status && res.status === 'error') {
                                alert(res.message)
                            } else if (!!res.status && res.status === 'success') {
                                alert(res.message)
                                location.reload();
                            } else {
                                alert('An error occurred, please check again!')
                            }
                        },
                        error: function (err) {
                            console.log(err)
                        }
                    })
                }
            })

            $('.delete-label').click(function () {
                let is_confirm = confirm('Do you want to delete this label?')
                let order_id = $(this).data('order-id');
                if (is_confirm && !!order_id) {
                    $.ajax({
                        url: '/staff/orders/delete-label/' + order_id,
                        type: 'GET',
                        dataType: 'json',
                        success: function (res) {
                            if (!!res.status && res.status === 'error') {
                                alert(res.message)
                            } else if (!!res.status && res.status === 'success') {
                                alert(res.message)
                                location.reload();
                            } else {
                                alert('An error occurred, please check again!')
                            }
                        },
                        error: function (err) {
                            console.log(err)
                        }
                    })
                }
            })*/
        });
    </script>
@endpush
