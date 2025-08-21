@extends('layouts.user')

@section('breadcrumb')
    @include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('dashboard')
        ],
        [
            'text' => 'Request Receiving',
            'url' => route('requests.index')
        ],
        [
            'text' => 'Confirm Receiving Fee'
        ]
    ]
])
@endsection


@section('styles')
<style>
.modal-body-scroll {
    max-height: calc(100vh - 210px);
    overflow-y: auto;
}

.img-unit {
    max-height: 250px;
    max-width: 250px;
    overflow: hidden;
    border: 1px solid black;
    margin: 10px;
}

#prices {
    font-family: Arial, Helvetica, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

#prices td, #prices th {
    border: 1px solid #ddd;
    padding: 8px;
}

#prices tr:nth-child(even){background-color: #f2f2f2;}

#prices tr:hover {background-color: #ddd;}

#prices th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    background-color: #04AA6D;
    color: white;
}

#prices tr td:nth-child(3) {
    text-align: right;
}
</style>
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

@php
    $customCodes = array_map(function ($barcode) {
        return DNS2D::getBarcodeSVG($barcode, 'QRCODE');
    }, $codes);
@endphp

@section('content')
    <div class="fade-in">
        <div class="card">
            <div class="card-header">
               <h2 class="mb-0">Confirm Receiving Fee</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="" class="form-horizontal" role="form" enctype="multipart/form-data">
                    @csrf

                    <section id="price" class="apy-60 abg-gray-100 position-relative">
                        <div class="container">
                            <h2 class="section-title">Receiving Fee</h2>
                            <div class="row amb-32">
                                <div class="table-responsive">
                                    <div>
                                        <table id="prices">
                                            {{-- Receiving --}}
                                            <tr>
                                                <td>Receiving</td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td>Receiving Shipment</td>
                                                <td>${{ $default }}</td>
                                            </tr>

                                            <tr>
                                                <td></td>
                                                <td>Box Fee</td>
                                                <td>${{ $box['totalPackage'] * $box['price'] }}</td>
                                            </tr>

                                            <tr>
                                                <td></td>
                                                <td>Pallet Fee</td>
                                                <td>Waiting for staff to confirm</td>
                                            </tr>

                                            <tr>
                                                <td></td>
                                                <td>Store Fee</td>
                                                <td>Waiting for staff to confirm</td>
                                            </tr>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </section>

                    <div class="search-form-group">
                        <div class="search-label d-none d-sm-block"></div>
                        {{-- <button type="button" class="btn btn-secondary apx-16 amr-8" onclick="addGroup()">
                            {{ __('Add Group') }}
                        </button> --}}

                        <div class="col rq-pkg-field apx-16">
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewBarcode()" style="min-width: 115px">
                                Preview Code
                            </button>
                        </div>
                        {{-- <div class="search-input text-center text-sm-left">
                            <button class="btn btn-primary" type="submit">{{ __('Create Request') }}</button>
                        </div> --}}
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="scan-modal" class="modal fade bd-example-scan-lg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <video id="video" style="border: 1px solid gray; width: 100%; height: 100%"></video>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body" id="preview-barcode">
            </div>
        </div>
        </div>
    </div>

    <!-- QR code -->
    <img id="imgshow" height="200" hidden>
@endsection

@section('scripts')
<script>
    let customCodes = {!! json_encode($customCodes) !!};
    let barcodes = customCodes.map(svgHtml =>  svgHtml.replace(/\r?\n|\r/g, '').trim());

    let sender = {!! json_encode($sender) !!};
    let recipient = {!! json_encode($recipient) !!};
    let packagegroupDetails = {!! json_encode($packagegroupDetail) !!};

    let date = {!! $date !!};

    console.log(barcodes.length, sender, recipient, date);

    function previewBarcode() {
        const { jsPDF } = window.jspdf;
        let doc = new jsPDF("p", "mm", "a4");

        const pageWidth = doc.internal.pageSize.getWidth();
        const recipientWidth = pageWidth/2;
        console.log(pageWidth);

        let canvas = document.createElement('canvas');
        let context = canvas.getContext('2d');


        const bufferHeight = 20;

        for (let i = 0; i < barcodes.length; i++ ) {
            if (i) doc.addPage();

            let v = canvg.Canvg.fromString(context, barcodes[i]);
            v.start()

            let imgData = canvas.toDataURL('image/png');

            doc.text('LeuLeuLLC', 10, bufferHeight + 0 - 10);
            doc.text(`Box ${i + 1} of ${barcodes.length}`, 200, bufferHeight + 0 - 10, {
                align: 'right'
            });

            // sender
            doc.text('SHIP FROM', 10, bufferHeight + 10);
            doc.text(sender['name'], 10, bufferHeight + 20);
            doc.text(sender['street1'], 10, bufferHeight + 30);
            doc.text(`${sender['city']}, ${sender['state']} ${sender['zip']}`, 10, bufferHeight + 40);
            doc.text(sender['country'], 10, bufferHeight + 50);

            doc.text('SHIP TO', recipientWidth, bufferHeight + 10);
            doc.text(recipient['name'], recipientWidth, bufferHeight + 20);
            doc.text(recipient['company'], recipientWidth, bufferHeight + 30);
            doc.text(recipient['street1'], recipientWidth, bufferHeight + 40);
            doc.text(`${recipient['city']}, ${recipient['state']} ${recipient['zip']}`, recipientWidth, bufferHeight + 50);
            doc.text(recipient['country'], recipientWidth, bufferHeight + 60);

            // Date
            // doc.text(`Date ${date}`, 10, bufferHeight + 80);

            // Barcode
            doc.addImage(imgData, 'PNG', 10, 100, 100, 100);

            let skuBuffer = 220;

            doc.text('SKU', 10, skuBuffer);

            for (const pgDetail of Object.values(packagegroupDetails)) {
                skuBuffer += 10;
                doc.text(`${pgDetail.name}, Quantity: ${pgDetail.unit_number}`, 10, skuBuffer);
            }
        }

        imgSrc = doc.output('bloburl');

        $("#preview-barcode").find("embed").remove();
        let embed = "<embed src="+ imgSrc +" frameborder='0' width='100%' height='500px' type='application/pdf' class='preview-pdf'>"
        $("#preview-barcode").append(embed);
    }

    $(document).ready(function () {

    });

</script>
@endsection
