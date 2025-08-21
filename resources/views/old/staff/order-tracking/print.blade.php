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
    @endphp
    <div class="container">
        <div class="row">
            @foreach ($orders as $order)
                <div class="item col-6 col-md-6 col-xl-6">
                    <div class="humanReadable2 code">{{ $order->order_code }}</div>
                    <div class="barcodeVal2">
                        <img
                            src="data:image/png;base64,{{ base64_encode($generatorPNG->getBarcode($order->order_code, $generatorPNG::TYPE_CODE_128)) }}">
                    </div>
                    <div class="humanReadable2">
                        <div> Name: {{ $order->addressTo->name }}</div>
                        <div>{{ $order->addressTo->street1 ?? ($order->shipping_street ?? '') }}</div>
                        <div>
                            {{ $order->addressTo->city ?? ($order->shipping_city ?? '') . ' - ' . $order->addressTo->zip ?? ($order->zip ?? '') }} - {{ $order->addressTo->state ?? $order->shipping_province  }}
                        </div>
                           <div>
                            {{ $order->addressTo->zip ?? ($order->zip ?? '')}}
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @section('styles')
        <style type="text/css" media="screen,print">
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
