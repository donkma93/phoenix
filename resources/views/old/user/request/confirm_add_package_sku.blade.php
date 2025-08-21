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
            'text' => 'Confirm Receiving SKU'
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

@endphp

@section('content')
    <div class="fade-in">
        <div class="card">
            <div class="card-header">
               <h2 class="mb-0">Confirm Receiving SKU</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="" class="form-horizontal" role="form" enctype="multipart/form-data">
                    @csrf

                    <section id="price" class="apy-60 abg-gray-100 position-relative">
                        <div class="container">
                            <h2 class="section-title">Receiving SKU</h2>
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
                                                <td>Box SKU</td>
                                                <td>${{ $box['totalPackage'] * $box['price'] }}</td>
                                            </tr>

                                            <tr>
                                                <td></td>
                                                <td>Pallet SKU</td>
                                                <td>Waiting for staff to confirm</td>
                                            </tr>

                                            <tr>
                                                <td></td>
                                                <td>Store SKU</td>
                                                <td>Waiting for staff to confirm</td>
                                            </tr>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- <div class="search-form-group">
                        <div class="search-label d-none d-sm-block"></div>
                        <button type="button" class="btn btn-secondary apx-16 amr-8" onclick="addGroup()">
                            {{ __('Add Group') }}
                        </button>
                        <div class="search-input text-center text-sm-left">
                            <button class="btn btn-primary" type="submit">{{ __('Create Request') }}</button>
                        </div>
                    </div> --}}
                </form>
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
@endsection

@section('scripts')
<script>
    function previewBarcode(id) {
        const { jsPDF } = window.jspdf;
        let doc = new jsPDF("p", "mm", "a4");
        let svgHtml = $(`#${id}`).html();
        if (svgHtml) {
            svgHtml = svgHtml.replace(/\r?\n|\r/g, '').trim();
        }

        let canvas = document.createElement('canvas');
        let context = canvas.getContext('2d');
        v = canvg.Canvg.fromString(context, svgHtml);
        v.start()

        let imgData = canvas.toDataURL('image/png');

        const a = b = 10;
        const distance = 100;

        doc.addImage(imgData, 'PNG', 10, 10, 100, 200);
        doc.text("Hello world!", 10, 120);

        doc.addImage(imgData, 'PNG', 10, 150, 100, 100);

        doc.code39( "0123456789", 10, 270, 100, 20 );
        imgSrc = doc.output('bloburl');

        $("#preview-barcode").find("embed").remove();
        let embed = "<embed src="+ imgSrc +" frameborder='0' width='100%' height='500px' type='application/pdf' class='preview-pdf'>"
        $("#preview-barcode").append(embed);
    }

    $(document).ready(function () {

    });

</script>
@endsection
