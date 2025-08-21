<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Warehouse')</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-243072486-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-243072486-1');
    </script>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/icons.css') }}">
    @yield('styles')
</head>

<body class="c-app">
    @include('layouts.partials.sidebar', [
        'items' => [
            [
                'url' => route('admin.dashboard'),
                'icon' => 'fa-tachometer',
                'text' => 'Dashboard',
                'hasChildren' => true,
                'children' => [
                    [
                        'url' =>route('admin.dashboard'),
                        'text' => 'Overview'
                    ],
                    [
                        'url' =>route('admin.orderOverview'),
                        'text' => 'Order overview'
                    ],
                ]
            ],
            [
                'url' => route('admin.scanner'),
                'icon' => 'fa-qrcode',
                'text' => 'Scan barcode'
            ],
            [
                'url' => route('admin.request.list'),
                'icon' => 'fa-exchange',
                'text' => 'Request'
            ],
            [
                'url' => route('admin.package.list'),
                'icon' => 'fa-cube',
                'text' => 'Package'
            ],
            [
                'url' => route('admin.package.history'),
                'icon' => 'fa-history',
                'text' => 'Package history'
            ],
            [
                'url' => route('admin.package-group.list'),
                'icon' => 'fa-cubes',
                'text' => 'Package Group'
            ],
            [
                'url' => route('admin.package-group-history.list'),
                'icon' => 'fa-clock-o',
                'text' => 'Package Group History'
            ],
            [
                'url' => route('admin.product.list'),
                'icon' => 'fa-archive',
                'text' => 'Product'
            ],
            [
                'url' => route('admin.inventory.list'),
                'icon' => 'fa-sticky-note',
                'text' => 'Inventory'
            ],
            [
                'url' => route('admin.warehouse.list'),
                'icon' => 'fa-flag',
                'text' => 'Warehouse'
            ],
            [
                'url' => route('admin.warehouseArea.list'),
                'icon' => 'fa-home',
                'text' => 'Warehouse Area'
            ],
            [
                'url' => route('admin.storeFulfill.list'),
                'icon' => 'fa-map-marker',
                'text' => 'Store'
            ],
            [
                'url' => route('admin.category.list'),
                'icon' => 'fa-bookmark',
                'text' => 'Category'
            ],
            [
                'url' => route('admin.orders.list'),
                'icon' => 'fa-list-alt',
                'text' => 'Order'
            ],
            [
                'url' => route('admin.tote.list'),
                'icon' => 'fa-shopping-bag',
                'text' => 'Tote',
            ],
              [
                'url' => route('admin.partner.list'),
                'icon' => 'fa-handshake-o',
                'text' => 'Partner',
            ],
            [
                'url' => route('admin.pricing.list'),
                'icon' => 'fa-list-alt',
                'text' => 'Pricing request'
            ],
            [
                'url' => route('admin.unit-price.list'),
                'icon' => 'fa-chain',
                'text' => 'Unit Price'
            ],
            [
                'url' => route('admin.invoice.list'),
                'icon' => 'fa-folder',
                'text' => 'Invoice'
            ],
            [
                'url' => route('admin.user.list'),
                'icon' => 'fa-user',
                'text' => 'User'
            ]
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
    <script src="{{ asset('js/admin_notification.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scripts')
</body>

</html>
