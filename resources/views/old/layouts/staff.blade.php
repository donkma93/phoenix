<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Warehouse')</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/icons.css') }}">

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-243072486-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-243072486-1');
    </script>

    <!-- Add the slick-theme.css if you want default styling -->
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <!-- Add the slick-theme.css if you want default styling -->
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
    @yield('styles')
</head>

<body class="c-app">
    @include('layouts.partials.sidebar', [
        'items' => [
            [
                'url' => route('staff.dashboard'),
                'icon' => 'fa-tachometer',
                'text' => 'Dashboard',
                'role' => [1, 3, 4, 5, 7]
            ],
            [
                'url' => route('staff.pickup.index'),
                'icon' => 'fa-tachometer',
                'text' => 'Pickup Request',
                'role' => [1, 3, 4, 5, 7]
            ],
            [
                'url' => route('staff.packing.outbound'),
                'icon' => 'fa-tachometer',
                'text' => 'Packing List Outbound',
                'role' => [1, 3, 4, 5, 7]
            ],
               [
                'url' => route('staff.orders.list'),
                'icon' => 'fa-list-alt',
                'text' => 'Order',
                'role' => [1, 3, 4, 5, 7]
            ],
        ],
    ])

    <div class="c-wrapper c-fixed-components">
        @include('layouts.partials.header')
        <div class="c-body">
            <main class="c-main apx-16 apx-lg-32">
                @yield('content')
            </main>
            @include('layouts.partials.footer')
        </div>
    </div>
    <div id="loading" style="display: none;">
        <div class="loader">Loading...</div>
    </div>

    @yield('flash')

    <!-- Scripts -->
    <script type="text/javascript" src="https://unpkg.com/canvg@3.0.4/lib/umd.js"></script>
    <script src="{{ asset('js/common.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/notification.js') }}"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    @yield('scripts')
</body>

</html>
