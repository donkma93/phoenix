@extends('layouts.user')

@section('breadcrumb')
    @include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('dashboard')
        ],
        [
            'text' => 'Order',
            'url' => route('orders.index')
        ],
        [
            'text' => $order->id
        ]
    ]
])
@endsection

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-header">
            <h2 class="mb-0">{{ __('Order  detail') }}</h2>
        </div>
        <div class="card-body">
            {{-- Status --}}
            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Order Number') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ $order->order_number ?? '' }}
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Order Status') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst(App\Models\Order::$statusName[$order->status]) }}
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Payment Status') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst(App\Models\Order::$paymentName[$order->payment]) }}
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Fulfillment Status') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst(App\Models\Order::$fulfillName[$order->fulfillment]) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Tracking Number') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ $order->orderTransaction->tracking_number ?? '' }}
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Tracking URL') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ $order->orderTransaction->tracking_url_provider ?? '' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Ship Provider') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->orderTransaction->orderRate->provider ?? '') }}
                        </div>
                    </div>
                </div>

                {{-- <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Ship Rate') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ isset($order->orderTransaction->orderRate) ? $order->orderTransaction->orderRate->getDisplayRate() ?? round($order->ship_rate, 2) : '' }}
                        </div>
                    </div>
                </div> --}}
            </div>
            <hr  style="display: {{ empty($order->orderTransaction) ? 'flex' : !$order->orderTransaction->tracking_number ? 'flex' : 'none' }}">

        <div class="row" style="display: {{ empty($order->orderTransaction) ? 'flex' : !$order->orderTransaction->tracking_number ? 'flex' : 'none' }}">
            <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                <div class="form-group row">
                    <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Tracking Number') }}</b></label>
                    <div class="col-8 col-sm-9 form-control border-0">
                            <input
                               class="form-control"
                                name="tracking_code" placeholder="Tracking number"
                            />
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                <div class="form-group row">
                    <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Tracking file') }}</b></label>
                    <div class="col-8 col-sm-9 form-control border-0">
                     <input type="file" 
                                                    accept="application/pdf"
                                                    hidden id="tracking_file" name="tracking_file"
                                                    class="btn-primary form-control">
                                                <div class="btn btn-info w-100" onclick="uploadTrackingFile()"> Upload file
                                                </div>
                                                @if ($errors->has('tracking_file'))
                                                    <p class="text-danger mb-0">
                                                        {{ $errors->first('tracking_file') }}
                                                    </p>
                                                @endif
                    </div>
                </div>
            </div>
        </div>

            <hr>
             <div class="row mb-3">
                                <div class="col-12 col-md-6 col-lg-12 col-xl-6 mb-3">

                                    <div class="form-group row mb-3">
                                        <label
                                            class="col-4 col-sm-3 col-form-label"><b>{{ __('Order Files') }}</b></label>
                                        <div class="col-8 col-sm-9 form-control border-0">
                                            <div class="search-input">
                                                <input type="file"
                                                    accept="application/pdf"
                                                    hidden id="order_files" name="order_files"
                                                    class="btn-primary form-control">
                                                <div class="btn btn-info w-100" onclick="uploadOrderFiles()"> Upload File
                                                </div>
                                                @if ($errors->has('order_files'))
                                                    <p class="text-danger mb-0">
                                                        {{ $errors->first('order_files') }}
                                                    </p>
                                                @endif

                                                @if (session('orderFilesErrors') !== null)
                                                    @foreach (session('orderFilesErrors') as $index => $error)
                                                        @php
                                                            $line = $index + 2;
                                                        @endphp
                                                        <p class="text-danger mb-0">
                                                            {{ "Line {$line}: {$error}" }}
                                                        </p>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>


                                </div>
                                <div class="col-12 col-md-6 col-lg-12 col-xl-6">

                                    <div class="form-group row">
                                        <div class="col-8 col-sm-9 border-0">
                                        @if (is_null($order->file_urls))
                                             <div>No file</div>
                                        @else
                                          @foreach(explode(',', $order->file_urls) as $info) 
                                            <div style="margin:10px 0">
                                         
                                            @if (str_ends_with($info, 'pdf'))
                                               {{$loop->index+1}}
                                           <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewPDF(`{{ $info }}`)">
                                            {{basename($info)}}
                                            </button>
                                        @else
                                           {{$loop->index+1}} |
                                          <a target="_blank" href="{{$info}}">{{basename($info)}}</a>
                                        @endif
                                              
                                            </div>
                                        @endforeach
                                        @endif
                                         
                                        </div>
                                    </div>


                                </div>
                            </div>
            <hr/>
            {{-- Customer and Item --}}
            <h3 class="amt-32">
                <b>{{ __('Default Item Infomation') }}</b>
            </h3>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Item Name') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->item_name) }}
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Item Quantity') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->item_quantity) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Item Price') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->item_price) }}
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Item Compare At Price') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->item_compare_at_price) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Item SKU') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->item_sku) }}
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Item Requires Shipping') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->item_requires_shipping) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Item Taxable') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->item_taxable) }}
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Item Fulfillment Status') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->item_fulfillment_status) }}
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            {{-- Customer and Item --}}
            <h3 class="amt-32">
                <b>{{ __('Address Infomation') }}</b>
            </h3>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Receiver Name') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->addressTo->name ?? $order->shipping_name ?? '') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Receiver Street') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->addressTo->street1 ?? $order->shipping_street ?? '') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Receiver Address 1') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->addressTo->street2 ?? $order->shipping_address1 ?? '') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Receiver Address 2') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->addressTo->street3 ?? $order->shipping_address2 ?? '') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Receiver Company') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->addressTo->company ?? $order->shipping_company ?? '') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Receiver City') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->addressTo->city ??  $order->shipping_city ?? '') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Receiver Zip') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->addressTo->zip ?? $order->shipping_zip ?? '') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Receiver Province') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->addressTo->state ?? $order->shipping_province ?? '') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Receiver Country') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->addressTo->country ?? $order->shipping_country ?? '') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Receiver Phone') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($order->addressTo->phone ?? $order->shipping_phone ?? '') }}
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            {{-- Product --}}
            <h3 class="amt-32">
                <b>{{ __('Product Infomation') }}</b>
            </h3>

            @foreach ($order->orderProducts as $index => $orderProduct)
                <div class="ap-24">
                    <div class="row amx-n16 amb-8">
                        <b>{{ $orderProduct->product->name }}</b>
                    </div>

                    <div class="row amx-n16 amb-8">
                        <div class="rq-pkg-field apx-16">
                            <b>{{ __('Product Status:') }}</b>
                        </div>
                        <div class="col apx-16">{{ ucfirst(App\Models\Product::$statusName[$orderProduct->product->status]) }}</div>
                    </div>

                    <div class="row amx-n16 amb-8">
                        <div class="rq-pkg-field apx-16">
                            <b>{{ __('Category:') }}</b>
                        </div>
                        <div class="col apx-16">{{ $orderProduct->product->category->name ?? '' }}</div>
                    </div>

                    <div class="row amx-n16 amb-8">
                        <div class="rq-pkg-field apx-16">
                            <b>{{ __('Quantity:') }}</b>
                        </div>
                        <div class="col apx-16">{{ $orderProduct->quantity }}</div>
                    </div>

                    <div class="row amx-n16 amb-8">
                        <div class="rq-pkg-field apx-16">
                            <b>{{ __('Product Fulfillment Fee:') }}</b>
                        </div>
                        <div class="col apx-16">{{ $orderProduct->product->fulfillment_fee }}</div>
                    </div>

                    <div class="row amx-n16 amb-8">
                        <div class="rq-pkg-field apx-16">
                            <b>{{ __('Extra Pick Fee:') }}</b>
                        </div>
                        <div class="col apx-16">{{ $orderProduct->product->extra_pick_fee }}</div>
                    </div>

                    <div class="row amx-n16 amb-8">
                        <div class="rq-pkg-field apx-16">
                            <b>{{ __('Total Fee:') }}</b>
                        </div>
                        <div class="col apx-16">{{ $orderProduct->total_fee }}</div>
                    </div>

                    <div class="row amx-n16 amb-8">
                        <div class="rq-pkg-field apx-16">
                            <b>{{ __('Image') }}</b>
                        </div>
                        @if(!isset($orderProduct->product->image_url))
                            <div class="col apx-16">{{ __('No image') }}</div>
                        @else
                            <div class="col apx-16">
                                <img id="image-upload" width="300" height="300" src="{{ asset($orderProduct->product->image_url) }}" alt="Product image" class="img-fluid">
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            <hr>

            {{-- Package --}}
            <h3 class="amt-32">
                <b>{{ __('Package Infomation') }}</b>
            </h3>

            @php
                $sizeType = $order->orderPackage->size_type ? ucfirst(App\Models\OrderPackage::$sizeName[$order->orderPackage->size_type]) : '';
                $weightType = $order->orderPackage->weight_type ? ucfirst(App\Models\OrderPackage::$weightName[$order->orderPackage->weight_type]) : '';

                $length = $order->orderPackage->length ? $order->orderPackage->length . ' ' . $sizeType : '';
                $width = $order->orderPackage->width ? $order->orderPackage->width . ' ' . $sizeType : '';
                $height = $order->orderPackage->height ? $order->orderPackage->height . ' ' . $sizeType : '';
                $weight = $order->orderPackage->weight ? $order->orderPackage->weight . ' ' . $weightType : '';
            @endphp

            <div class="ap-24">
                <div class="row amx-n16 amb-8">
                    <div class="rq-pkg-field apx-16">
                        <b>{{ __('Length:') }}</b>
                    </div>
                    <div class="col apx-16">{{ $length }}</div>
                </div>

                <div class="row amx-n16 amb-8">
                    <div class="rq-pkg-field apx-16">
                        <b>{{ __('Width:') }}</b>
                    </div>
                    <div class="col apx-16">{{ $width }}</div>
                </div>

                <div class="row amx-n16 amb-8">
                    <div class="rq-pkg-field apx-16">
                        <b>{{ __('Height:') }}</b>
                    </div>
                    <div class="col apx-16">{{ $height }}</div>
                </div>

                <div class="row amx-n16 amb-8">
                    <div class="rq-pkg-field apx-16">
                        <b>{{ __('Weight:') }}</b>
                    </div>
                    <div class="col apx-16">{{ $weight }}</div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="preview-pdf">
      </div>
    </div>
  </div>
</div>

@endsection


@section('scripts')
    <script>

        function uploadOrderFiles() {
            $('#order_files').click();
        }

        function uploadTrackingFile() {
            if (!$('input[name="tracking_code"]').val()) {
                return alert('Enter Tracking number first!');
            }
            $('#tracking_file').click();
        }

        $("document").ready(function() {
            $("#order_files").change(function(e) {
                console.log(this.files);

                // upload file
                var fd = new FormData();

                // Append data 
                fd.append('file', this.files[0]);
                fd.append('order_id',  "{{$order->id}}");
                fd.append('_token', '{{ csrf_token() }}');

                // Hide alert 
                $('#responseMsg').hide();

                // AJAX request 
                $.ajax({
                    url: "{{ route('orders.uploadFiles') }}",
                    method: 'post',
                    data: fd,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                        window.location.reload();
                        // console.log('response:: ', response);
                        // alert("Tải lên file thành công!");
                    },
                    error: function(response) {
                        alert("Có lỗi tải lên file!");
                        console.log("error : " + JSON.stringify(response));
                    }
                });
                // end
            });

            // tracking file

               $("#tracking_file").change(function(e) {
                console.log(this.files);

                // upload file
                var fd = new FormData();

                // Append data 
                fd.append('file', this.files[0]);
                fd.append('order_id',  "{{$order->id}}");
                fd.append('tracking_code',  $('input[name="tracking_code"]').val());
                fd.append('_token', '{{ csrf_token() }}');

                // Hide alert 
                $('#responseMsg').hide();

                // AJAX request 
                $.ajax({
                    url: "{{ route('orders.uploadTrackingInfo') }}",
                    method: 'post',
                    data: fd,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                        window.location.reload();
                        // console.log('response:: ', response);
                        // alert("Tải lên file thành công!");
                    },
                    error: function(response) {
                        alert("Có lỗi tải lên file!");
                        console.log("error : " + JSON.stringify(response));
                    }
                });
                // end
            });
        });

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
            doc.addImage(file, 'JPEG',  0, 0, width, height);
            imgSrc = doc.output('bloburl');

        } else {
            imgSrc = file
        }

        $("#preview-pdf").find("embed").remove();
            let embed = "<embed src="+ imgSrc +" frameborder='0' width='100%' height='500px' type='application/pdf' class='preview-pdf'>"
        $("#preview-pdf").append(embed)
    }

    </script>
@endsection