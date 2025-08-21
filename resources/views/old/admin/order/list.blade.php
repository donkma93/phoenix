@extends('layouts.admin')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('admin.dashboard')
        ],
        [
            'text' => 'Order'
        ]
    ]
])
@endsection

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('Order list') }}</h2>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.orders.list') }}" class="form-horizontal" role="form">
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
                        <select id="fulfillment" name="picking_status" class="form-control w-100">
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
                    <label for="picking_status" class="col-form-label search-label"><b>{{ __('State') }}</b></label>
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
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="admin-order-table">
                        <thead>
                            <tr>
                                <th>{{ __('No') }}</th>
                                <th>{{ __('Order Number') }}</th>
                                <th>{{ __('Sender Information') }}</th>
                                <th>{{ __('Recipient Information') }}</th>
                                <th>{{ __('Product Information') }}</th>
                                <th>{{ __('Package Information') }}</th>
                                <th>{{ __('Order Status') }}</th>
                                <th>{{ __('Payment Status') }}</th>
                                <th>{{ __('Fulfillment Status') }}</th>
                                <th>{{ __('Ship Rate') }}</th>
                                <th>{{ __('Tracking Number') }}</th>
                                <th>{{ __('Label') }}</th>
                                <th>{{ __('Picking') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td>{{ ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration }}</td>
                                    <td>{{ $order->order_number ?? '' }}</td>
                                    <td>
                                        <div><b>Name: </b>{{ $order->addressFrom->name ?? $order->orderTransaction->shipping_name ?? '' }}</div>
                                        <div><b>Street: </b>{{ $order->addressFrom->street1 ?? $order->orderTransaction->shipping_street ?? '' }}</div>
                                        <div><b>Address1: </b>{{ $order->addressFrom->street2 ?? $order->orderTransaction->shipping_address1 ?? '' }}</div>
                                        <div><b>Address2: </b>{{ $order->addressFrom->street3 ?? $order->orderTransaction->shipping_address2 ?? '' }}</div>
                                        <div><b>Company: </b>{{ $order->addressFrom->company ?? $order->orderTransaction->shipping_company ?? '' }}</div>
                                        <div><b>City: </b>{{ $order->addressFrom->city ?? $order->orderTransaction->shipping_city ?? '' }}</div>
                                        <div><b>Zip: </b>{{ $order->addressFrom->zip ?? $order->orderTransaction->shipping_zip ?? '' }}</div>
                                        <div><b>Province: </b>{{ $order->addressFrom->state ?? $order->orderTransaction->shipping_province ?? '' }}</div>
                                        <div><b>Country: </b>{{ $order->addressFrom->country ?? $order->orderTransaction->shipping_country ?? '' }}</div>
                                        <div><b>Phone: </b>{{ $order->addressFrom->phone ?? $order->orderTransaction->shipping_phone ?? '' }}</div>
                                    </td>
                                    <td>
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
                                    <td style="text-align: left">
                                        @php
                                            $sizeType = $order->orderPackage->size_type ? ucfirst(App\Models\OrderPackage::$sizeName[$order->orderPackage->size_type]) : '';
                                            $weightType = $order->orderPackage->weight_type ? ucfirst(App\Models\OrderPackage::$weightName[$order->orderPackage->weight_type]) : '';

                                            $length = $order->orderPackage->length ? $order->orderPackage->length . ' ' . $sizeType : '';
                                            $width = $order->orderPackage->width ? $order->orderPackage->width . ' ' . $sizeType : '';
                                            $height = $order->orderPackage->height ? $order->orderPackage->height . ' ' . $sizeType : '';
                                            $weight = $order->orderPackage->weight ? $order->orderPackage->weight . ' ' . $weightType : '';
                                        @endphp
                                        <div><b>Length: </b>{{ $length }}</div>
                                        <div><b>Width: </b>{{ $width }}</div>
                                        <div><b>Height: </b>{{ $height }}</div>
                                        <div><b>Weight: </b>{{ $weight }}</div>
                                    </td>
                                    <td style="text-align: center">{{ ucfirst(App\Models\Order::$statusName[$order->status]) }}</td>
                                    <td style="text-align: center">{{ ucfirst(App\Models\Order::$paymentName[$order->payment]) }}</td>
                                    <td style="text-align: center">{{ ucfirst(App\Models\Order::$fulfillName[$order->fulfillment]) }}</td>
                                    <td style="text-align: center">{{ isset($order->orderTransaction->orderRate) ? $order->orderTransaction->orderRate->getDisplayRate() : '' }} {{ $order->orderTransaction->orderRate->currency ?? '' }}</td>
                                    <td style="text-align: left">
                                        <div>{{ isset($order->orderTransaction->orderRate->provider) ?  $order->orderTransaction->orderRate->provider . ' Tracking' :  '' }}</div>
                                        <div>{{ $order->orderTransaction->tracking_number ?? '' }}</div>
                                    <td>
                                        @if (isset($order->orderTransaction->label_url))
                                            <div class="row amx-n16 amb-8">
                                                <div class="rq-pkg-field apx-16">
                                                    <button type="button" class="btn btn-success" data-toggle="modal"
                                                        data-target="#preview-label" onclick="previewPDF(`{{ asset($order->orderTransaction->label_url) }}`)">
                                                        Preview PDF
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        {{ ucfirst(App\Models\Order::$pickingName[$order->picking_status]) }}
                                    </td>
                                    <td>
                                        <a class="btn btn-block btn-info" href="{{ route('admin.orders.detail', ['id' => $order->id ]) }}">
                                            {{ __('Detail') }}
                                        </a>
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

    filterInput(document.getElementById("email-input"), emails, 'dropdown-email');

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
</script>
@endsection
