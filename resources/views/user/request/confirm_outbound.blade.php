@extends('layouts.user')

@section('breadcrumb')
    @include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('dashboard')
        ],
        [
            'text' => 'Request Outbound',
            'url' => route('requests.index')
        ],
        [
            'text' => 'Confirm Outbound Fee'
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
               <h2 class="mb-0">Confirm Outbound Fee</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="" class="form-horizontal" role="form" enctype="multipart/form-data">
                    @csrf

                    <section id="price" class="apy-60 abg-gray-100 position-relative">
                        <div class="container">
                            <h2 class="section-title">Outbound Fee</h2>
                            <div class="row amb-32">
                                <div class="table-responsive">
                                    <div>
                                        <table id="prices">
                                            {{-- Outbound --}}
                                            <tr>
                                                <td>Outbound</td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td>Outbound Shipment</td>
                                                <td>${{ $default }}</td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td>Order Pick Fee Per SKU</td>
                                                <td>${{ $sku['fee'] }}</td>
                                            </tr>

                                            <tr>
                                                <td></td>
                                                <td>File PDF Fee</td>
                                                <td>${{ $pdf['fee'] }}</td>
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

                                    <div> <b> Temporarily you cannot confirm the fee. Please wait a while! </b> </div>
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
    <div id="scan-modal" class="modal fade bd-example-scan-lg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <video id="video" style="border: 1px solid gray; width: 100%; height: 100%"></video>
                </div>
            </div>
        </div>
    </div>

    <!-- QR code -->
    <img id="imgshow" height="200" hidden>
@endsection

@section('scripts')
<script>

    $(document).ready(function () {

    });

</script>
@endsection
