<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Phoenix Logistics is an order fulfillment company with full services, which helps you improve operations, reduce costs and create a special experience for your customers.">
    <meta name="keywords" content="phoenix, phoenix logistics, logistics, fulfillment">
    <meta name="author" content="phoenix">
    <title>Phoenix Logistics</title>
    <link rel="shortcut icon" href="{{ asset('assets/template_1/img/logo-phoenix.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/template_1/css/plugins.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/template_1/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/template_1/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/template_1/css/colors/aqua.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preload" href="{{ asset('assets/template_1/css/fonts/thicccboi.css') }}" as="style" onload="this.rel='stylesheet'">
    @yield('css')
</head>

<body>
<div class="content-wrapper">
    @include('client.blocks.header')
    <!-- /header -->

    @yield('content')
    <!-- /content -->

</div>
<!-- /.content-wrapper -->

@include('client.blocks.footer')
<!-- /footer -->

<div class="progress-wrap">
    <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
        <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
    </svg>
</div>
<script src="{{ asset('assets/template_1/js/plugins.js') }}"></script>
<script src="{{ asset('assets/template_1/js/theme.js') }}"></script>
<script src="{{ asset('assets/template_1/js/jquery3.7.0.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js" integrity="sha512-3j3VU6WC5rPQB4Ld1jnLV7Kd5xr+cq9avvhwqzbH/taCRNURoeEpoPBK9pDyeukwSxwRPJ8fDgvYXd6SkaZ2TA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="{{ asset('assets/template_1/js/custom.js') }}"></script>
@yield('js')
</body>

</html>
