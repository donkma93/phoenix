@extends('layouts.staff')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('staff.dashboard')
        ],
        [
            'text' => 'Order'
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

@section('styles')
<style>
.label {
  color: white;
  border-radius: 25px;
  margin: 8px;
  padding: 8px;
}

.ma-8 {
    margin: 8px;
}

.success {background-color: #04AA6D;} /* Green */
.info {background-color: #2196F3;} /* Blue */
.warning {background-color: #ff9800;} /* Orange */
.danger {background-color: #f44336;} /* Red */
.other {background-color: #e7e7e7; color: black;} /* Gray */
</style>
@endsection

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('Order list') }}</h2>
            <button data-toggle="modal" data-target=".bd-example-modal-lg"  class="btn btn-success">
                {{ __('New Order') }}
            </button>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('staff.orders.list') }}" class="form-horizontal" role="form">

                <div class="form-group search-form-group ">
                        <div class="row">
                            <div class="col-5">
                                <label for="type" class="col-form-label search-label"><b>From date</b></label>
                                <div class="search-input position-relative">
                                    <input type="date" class="form-control w-100" id="date_from" name="date_from"
                                        value="@if (isset($oldInput['date_from'])) {{ $oldInput['date_from'] }} @endif"
                                        autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-5">
                                <label for="type" class="col-form-label search-label"><b>To date</b></label>
                                <div class="search-input position-relative">
                                    <input type="date" class="form-control w-100" id="date_to" name="date_to"
                                        value="@if (isset($oldInput['date_to'])) {{ $oldInput['date_to'] }} @endif"
                                        autocomplete="off" />
                                </div>
                            </div>
    
                            <div class="col-2">
                                <label for="type" class="col-form-label search-label">&nbsp;</label>
                                <div class="search-input position-relative">
                                    <!-- <a id="btnExport" class="btn btn-primary" href="">Export</a> -->
                                    <a id="btnExport" class="btn btn-primary" href="{{ route('staff.staffOrderExport', [ 'datefrom' => 0, 'dateto' => 0 ]) }}">Export</a>
                                </div>
                            </div>
                        </div>
                    </div>




            
                <div class="form-group search-form-group">
                    <label for="type" class="col-form-label search-label"><b>{{ __('Email') }}</b></label>
                    <div class="search-input position-relative">
                        <input type="input" class="form-control w-100" id="email-input" list="dropdown-email" name="email" value="@if (isset($oldInput['email'])){{$oldInput['email']}}@endif" autocomplete="off" />
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="status" class="col-form-label search-label"><b>{{ __('Type') }}</b></label>
                    <div class="search-input">
                        <select id="status" name="status" class="form-control w-100">
                            <option selected></option>
                            @foreach (App\Models\Order::$statusName as $value => $status)
                                <option value="{{ $value }}"
                                    @if (isset($oldInput['status']) && $oldInput['status'] == $value)
                                        selected="selected"
                                    @endif
                                >{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="payment" class="col-form-label search-label"><b>{{ __('Payment Status') }}</b></label>
                    <div class="search-input">
                        <select id="payment" name="payment" class="form-control w-100">
                            <option selected></option>
                            @foreach (App\Models\Order::$paymentName as $value => $status)
                                <option value="{{ $value }}"
                                    @if (isset($oldInput['payment']) && $oldInput['payment'] == $value)
                                        selected="selected"
                                    @endif
                                >{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="fulfillment" class="col-form-label search-label"><b>{{ __('Fulfillment Status') }}</b></label>
                    <div class="search-input">
                        <select id="fulfillment" name="fulfillment" class="form-control w-100">
                            <option selected></option>
                            @foreach (App\Models\Order::$fulfillName as $value => $status)
                                <option value="{{ $value }}"
                                    @if (isset($oldInput['fulfillment']) && $oldInput['fulfillment'] == $value)
                                        selected="selected"
                                    @endif
                                >{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="picking_status" class="col-form-label search-label"><b>{{ __('Picking Status') }}</b></label>
                    <div class="search-input">
                        <select id="picking_status" name="picking_status" class="form-control w-100">
                            <option selected></option>
                            @foreach (App\Models\Order::$pickingName as $value => $status)
                                <option value="{{ $value }}"
                                    @if (isset($oldInput['picking_status']) && $oldInput['picking_status'] == $value)
                                        selected="selected"
                                    @endif
                                >{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="state" class="col-form-label search-label"><b>{{ __('State') }}</b></label>
                    <div class="search-input">
                        <select id="state" name="state" class="form-control w-100">
                            <option selected></option>
                            @foreach (App\Models\Order::$stateName as $value => $state)
                                <option value="{{ $value }}"
                                    @if (isset($oldInput['state']) && $oldInput['state'] == $value)
                                        selected="selected"
                                    @endif
                                >{{ ucfirst($state) }}</option>
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
            </form>
        </div>
        <div class="card-footer">
            @if (count($orders))
                <div class="table-responsive">
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-order-table">
                        <thead>
                            <tr>
                                <th>{{ __('No') }}</th>
                                <th>{{ __('Order') }}</th>
                                <th>{{ __('Order Number') }}</th>
                                <th>{{ __('Customer') }}</th>
                                <th>{{ __('Items') }}</th>
                                <th>{{ __('Package info') }}</th>
                                <th>{{ __('Rates') }}</th>
                                <th>{{ __('Tracking Number') }}</th>
                                <th>{{ __('Label') }}</th>
                                <th>{{ __('Picking') }}</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td>
                                        {{-- {{ ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration }} --}}
                                        {{ $order->id }}
                                    </td>
                                    <td>
                                        <div  style="text-align: center">{{ $order->created_at->format('Y/m/d') }}</div>
                                        <div class="ma-8">
                                            <span class="label success">{{ ucfirst(App\Models\Order::$statusName[$order->status]) }}</span>
                                        </div>
                                        <div class="ma-8">
                                            <span class="label info">{{ ucfirst(App\Models\Order::$paymentName[$order->payment]) }}</span>
                                        </div>
                                        <div class="ma-8">
                                            <span class="label warning">{{ ucfirst(App\Models\Order::$fulfillName[$order->fulfillment]) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        {{ $order->order_number ?? '' }}
                                    </td>
                                    <td>
                                        <div class="c-header-nav-item mx-2 dropdown">
                                            <div class="c-header-nav-link" href="#" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                                <div><b>{{ $order->addressTo->name ?? $order->shipping_name ?? '' }}</b></div>
                                                <div>{{ $order->addressFrom->street1 ?? $order->orderTransaction->shipping_street ?? '' }}</div>
                                                <div>
                                                    {{ $order->addressTo->city ??  $order->shipping_city ?? '' }}, {{ strtoupper($order->addressTo->state ?? $order->shipping_province ?? '') }} {{ $order->addressTo->zip ?? $order->shipping_zip ?? '' }}
                                                </div>
                                            </div>

                                            <div class="dropdown-menu dropdown-menu-right pt-0 w-100">
                                                <div class="dropdown-header bg-light py-2">
                                                    <strong><b>Recipient Information</b></strong>
                                                </div>
                                                <div class="ap-16">
                                                    <div><b>Name: </b>{{ $order->addressTo->name ?? $order->shipping_name ?? '' }}</div>
                                                    <div><b>Street: </b>{{ $order->addressTo->street1 ?? $order->shipping_street ?? '' }}</div>
                                                    <div><b>Address1: </b>{{ $order->addressTo->street2 ?? $order->shipping_address1 ?? '' }}</div>
                                                    <div><b>Address2: </b>{{ $order->addressTo->street3 ?? $order->shipping_address2 ?? '' }}</div>
                                                    <div><b>Company: </b>{{ $order->addressTo->company ?? $order->shipping_company ?? '' }}</div>
                                                    <div><b>City: </b>{{ $order->addressTo->city ??  $order->shipping_city ?? '' }}</div>
                                                    <div><b>Zip: </b>{{ $order->addressTo->zip ?? $order->shipping_zip ?? '' }}</div>
                                                    <div><b>Province: </b>{{ $order->addressTo->state ?? $order->shipping_province ?? '' }}</div>
                                                    <div><b>Country: </b>{{ $order->addressTo->country ?? $order->shipping_country ?? '' }}</div>
                                                    <div><b>Phone: </b>{{ $order->addressTo->phone ?? $order->shipping_phone ?? '' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="text-align: left">
                                        @foreach ($order->orderProducts as $index => $orderProduct)
                                            @if($index)
                                                <hr>
                                            @endif
                                            <div><b>Name: </b>{{ $orderProduct->product->name }}</div>
                                            <div><b>Quantity: </b>{{ $orderProduct->quantity }}</div>
                                             @if(isset($orderProduct->product->image_url))
                                                <div>
                                                    <img id="image-upload" width="100" src="{{ asset($orderProduct->product->image_url) }}" alt="Product image" class="img-fluid">
                                                </div>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        @php
                                            $sizeType = isset($order->orderPackage->size_type) ? ucfirst(App\Models\OrderPackage::$sizeName[$order->orderPackage->size_type]) : '';
                                            $weightType = isset($order->orderPackage->weight_type) ? ucfirst(App\Models\OrderPackage::$weightName[$order->orderPackage->weight_type]) : '';

                                            $length = $order->orderPackage->length ?? 'Unknown';
                                            $width = $order->orderPackage->width ?? 'Unknown';
                                            $height = $order->orderPackage->height ?? 'Unknown';
                                            $weight = $order->orderPackage->weight ?? 'Unknown';
                                        @endphp
                                        <div>{{ $weight }} <b>{{ $weightType }}</b>  {{ $length }} x {{ $width }} x {{ $height }} <b>{{ $sizeType }}</b></div>
                                    </td>
                                    <td>
                                        @php
                                            $rate = $order->orderTransaction->orderRate ?? null;
                                        @endphp
                                        <div class="d-flex">
                                            {{-- <div><img src="{{ $rate->provider_image_75 ?? '' }}" alt=""></div> --}}
                                            <div>
                                                <div><b>{{ isset($rate->provider) ?  $rate->provider . ' Tracking' :  '' }}</b></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>{{ $order->orderTransaction->tracking_number ?? '' }}</div>
                                    </td>
                                    <td>
                                        @if (isset($order->orderTransaction->label_url))
                                            <button type="button" class="fmus01 btn btn-success" data-toggle="modal"
                                                data-target="#preview-label" onclick="previewPDF(`{{ asset($order->orderTransaction->label_url) }}`)">
                                                Preview PDF
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        {{ ucfirst(App\Models\Order::$pickingName[$order->picking_status]) }}
                                    </td>
                                    <td>
                                        <a class="btn btn-block btn-info" href="{{ route('staff.orders.detail', ['id' => $order->id ]) }}">
                                                {{ __('Detail') }}
                                        </a>
                                    </td>
                                    <td>
                                        @if (!empty($order->order_address_to_id) && $order->orderTransaction == null)
                                            @if (count($order->orderRates))
                                                <a class="btn btn-block btn-primary" href="{{ route('staff.orders.rates.create', ['orderId' => $order->id ]) }}">
                                                    {{ __('Choose Rate') }}
                                                </a>
                                            @else
                                                <a class="btn btn-block btn-warning" href="{{ route('staff.orders.labels.create', ['orderId' => $order->id ]) }}">
                                                    {{ __('Transaction') }}
                                                </a>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center justify-content-md-end amt-16">
                    {{ $orders->appends(request()->all())->links('components.pagination') }}
                </div>
            @else
                <div class="text-center">{{ __('No data.') }}</div>
            @endif
        </div>
    </div>
</div>

<!-- Modal -->
<div id="create-order-modal" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                {{ __('Select Email') }}
            </div>
            <div class="modal-body">
                <div class="form-group search-form-group">
                    <label for="type" class="col-form-label search-label"><b>{{ __('Email') }}</b></label>
                    <div class="search-input position-relative">
                        <input type="input" class="form-control w-100" id="email-input-create" list="dropdown-email-create" name="email" value="@if (isset($oldInput['email'])){{$oldInput['email']}}@endif" autocomplete="off" />
                        <p class="text-danger mb-0" id="error-message">

                        </p>
                    </div>
                </div>

                <div class="btn btn-success" onclick="redirectToNewPage()">
                    {{ __('New Order') }}
                </div>
            </div>
        </div>
    </div>
</div>

<div id="preview-label" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-body" id="preview-barcode">
        </div>
    </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let emails = @php echo json_encode($emails) @endphp;
    let users = @php echo json_encode($users) @endphp;

    filterInput(document.getElementById("email-input"), emails, 'dropdown-email');
    filterInput(document.getElementById("email-input-create"), emails, 'dropdown-email-create');

    function redirectToNewPage() {
        const value = $('#email-input-create').val();

        if(!value) {
            $('#email-input-create').addClass('is-invalid');
            $('#error-message').text('Email required!');

            return;
        }

        const user = users.find(item =>{
            return item.email == value;
        });

        if(!user) {
            $('#email-input-create').addClass('is-invalid');
            $('#error-message').text('Email incorrect!');

            return;
        }

        let url = "{{ route('staff.orders.new', ['id' => 'id']) }}"
        url = url.replace('id', user.id);

        window.location.href = url
    }

    function previewPDF(file) {
        const { jsPDF } = window.jspdf;
        const splitFile = file.split('.');
        const fileType = splitFile[splitFile.length - 1];
        const validImageTypes = ['gif', 'jpeg', 'png', 'tiff', 'jpg', 'heif'];

        let imgSrc;
        if (validImageTypes.includes(fileType)) {
            let doc = new jsPDF("p", "mm", "a4");

            let width = doc.internal.pageSize.getWidth();
            let height = doc.internal.pageSize.getHeight();
            doc.addImage(file, 'JPEG',  10, 10, width, height);
            imgSrc = doc.output('bloburl');
        } else {
            imgSrc = file
        }

        $("#preview-barcode").find("embed").remove();
        let embed = "<embed src="+ imgSrc +" frameborder='0' width='100%' height='500px' type='application/pdf' class='preview-pdf'>"
        $("#preview-barcode").append(embed)
    }



    $(document).ready(function () {
            
            $('#date_from').change(function() {
                debugger;
                let date = $(this).val();
                let elink = $('a#btnExport').attr('href');
                const myArray = elink.split("/");
                let oLink = "{{ route('staff.staffOrderExport', [ 'datefrom' => 0, 'dateto' => 0 ]) }}";
                let dt = date
                let df = myArray[ myArray.length-1];
                let llink = oLink.slice(0, oLink.length-3) + dt + "/" + df

                $('a#btnExport').attr("href", llink);
                
            });


            $('#date_to').change(function() {
                debugger;
                let date = $(this).val();
                let elink = $('a#btnExport').attr('href');
                const myArray = elink.split("/");
                let oLink = "{{ route('staff.staffOrderExport', [ 'datefrom' => 0, 'dateto' => 0 ]) }}";
                let dt = myArray[ myArray.length-2];
                let df = date;
                let llink = oLink.slice(0, oLink.length-3) + dt + "/" + df

                $('a#btnExport').attr("href", llink);
                
            });


        });

        
</script>
@endsection
