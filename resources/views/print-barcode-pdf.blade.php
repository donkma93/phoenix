<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Barcode Print</title>

    <link rel="stylesheet" href="{{ public_path('css/main.css') }}">
    <link rel="stylesheet" href="{{ public_path('css/custom.css') }}">
    <link rel="stylesheet" href="{{ public_path('css/icons.css') }}">
    <link rel="stylesheet" href="{{ public_path('css/print-barcode.css') }}">
</head>
<body>
@php
    $generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();
    // Check if $orders is defined, if not initialize it as an empty array to prevent errors lamdt
    $orders = $orders ?? [];
@endphp
<div class="container1">
    <div class="row">
        @foreach ($orders as $order)
            <div class="item col-12 col-md-12 col-xl-12">
                <div class="humanReadable2 code">{{ $order->order_code }}</div>
                <div class="barcodeVal2">
                    <img
                        style="width: 80%;"
                        src="data:image/png;base64,{{ base64_encode($generatorPNG->getBarcode($order->order_code, $generatorPNG::TYPE_CODE_128)) }}">
                </div>
                <div class="humanReadable2" style="font-size: 28px !important;">
                    <div style="line-height: 1.2 !important;"> {{ $order->order_number ?? '' }}</div>
                    <div style="line-height: 1.2 !important;"> NAME: {{ $order->addressTo->name ?? '' }}</div>
                    <div style="line-height: 1.2 !important;">
                        {{ $order->addressTo->zip ?? ($order->zip ?? '')  }} - <b>{{$order->addressTo->country}}</b>
                    </div>
                    <div>
                        {{ $order->orderPackage->width ?? '' }} x {{ $order->orderPackage->length ?? '' }} x {{ $order->orderPackage->height ?? '' }} -
                        {{ isset($order->orderPackage->weight) ? ($order->orderPackage->weight) : '' }}
                    </div>
                </div>
            </div>
            <div style="break-after:page !important;"></div>
        @endforeach
    </div>
</div>
</body>
</html>
