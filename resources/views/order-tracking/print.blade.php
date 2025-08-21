<!DOCTYPE html>
<html>

<head>
    <title>Barcode Print Man</title>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/icons.css') }}">
    <script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
</head>

<body>
@php
$generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();
// Check if $orders is defined, if not initialize it as an empty array to prevent errors
$orders = $orders ?? [];
@endphp
<div class="container">
    <div class="row">
        @foreach ($orders as $order)
        <div class="item col-12 col-md-12 col-xl-12">
            <div class="humanReadable2 code">{{ $order->order_code }}</div>
            <div class="barcodeVal2">
                <img
                    style="width: 80%;"
                    src="data:image/png;base64,{{ base64_encode($generatorPNG->getBarcode($order->order_code, $generatorPNG::TYPE_CODE_128)) }}">
            </div>
            <div class="humanReadable2">
                <div> {{ $order->order_number ?? '' }}</div>
                <div> NAME: {{ $order->addressTo->name ?? '' }}</div>
                <div>
                    {{ $order->addressTo->zip ?? ($order->zip ?? '')  }} - <b> {{$order->addressTo->country}}</b>
                </div>
                <div>
                    {{ $order->orderPackage->width ?? '' }} x {{ $order->orderPackage->length ?? '' }} x {{ $order->orderPackage->height ?? '' }} -
                    {{ isset($order->orderPackage->weight) ? ($order->orderPackage->weight . ($order->orderPackage->weight_type ?? '')) : '' }}
                </div>
            </div>
        </div>
        <div style="break-after:page"></div>
        @endforeach
    </div>
</div>

@section('styles')
<style type="text/css" media="screen,print">


    @media print {
        .pagebreak { page-break-before: always; } /* page-break-after works, as well */
    }

    .item {
        margin: 20px 0px;
        border-bottom: 1px solid gray;
        padding-bottom: 50px;
    }

    .code {
        font-weight: bold;
    }

    div.barcodeVal1 {
        font-weight: normal;
        font-style: normal;
        line-height: normal;
        font-size: 32px;
    }

    div.barcodeVal1 {
        text-align: center;
    }

    .humanReadable2 {
        text-align: center;
        font-weight: bold;
        font-size: 32px;
    }


    div.barcodeVal2 {
        text-align: center;
        font-weight: normal;
        font-style: normal;
        line-height: normal;
        font-size: 32px;
    }

    div.barcodeVal2 {}
</style>


@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        console.log("ready!");
        window.print();
    });
</script>

</body>

</html>
