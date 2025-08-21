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

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-243072486-1');
    </script>
    @yield('styles')
</head>

<body class="c-app">
    @include('layouts.partials.sidebar', [
        'items' => [
            [
                'url' => route('pickup.index'),
                'icon' => 'fa-exchange',
                'text' => 'E-Packet',
            ],
            [
                'url' => route('orders.index'),
                'icon' => 'fa-shopping-cart',
                'text' => 'My Order',
            ],
            [
                'url' => route('package_groups.index'),
                'icon' => 'fa-cube',
                'text' => 'My Package Group',
            ],
                [
                'url' => route('inventories.list'),
                'icon' => 'fa-sticky-note',
                'text' => 'Inventory'
            ],
        ],
    ])

    <div class="c-wrapper c-fixed-components">
        @include('layouts.partials.header')
        <div class="c-body">
            <main class="c-main apx-md-16 apx-lg-32">
                @yield('content')
            </main>
            @include('layouts.partials.footer')
        </div>
    </div>
    @yield('flash')

    <div id="loading" style="display: none;">
        <div class="loader">Loading...</div>
    </div>

    <!-- Box chat -->
    <div id="form-chat" class="wrapper-box-chat">
        <div class="box-chat">
            <div class="d-flex align-items-center justify-content-between abg-blue-900 text-white ap-12 w-100">
                <div>Hỗ trợ</div>
                <i class="fa fa-minus js-close-chat font-20 pointer" aria-hidden="true"></i>
            </div>
            <div class="box-form d-flex flex-column position-relative">
                <div class="box-content-loading">
                    <div class="loader">Loading...</div>
                </div>
                <ul class="scroll-height flex-grow-1 box-content p-3 list-unstyled mb-0 font-12" id="chat-content">
                </ul>
                <div class="p-2 border-top d-flex align-items-center justify-content-center">
                    <div class="flex-grow-1 mr-2">
                        <input type="text" id="create_message"
                            class="form-control form-control-sm rounded-pill bg-light border-0" placeholder="Aa" />
                    </div>
                    <button type="submit">
                        <i class="fa fa-paper-plane text-primary pointer font-18" aria-hidden="true"
                            id="send_message"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end position-relative">
            <span class="position-absolute badge bg-danger text-white chat-noti" id="chat-noti">N</span>
            <div class="box-icon shadow js-box-chat">
                <i class="fa fa-comments-o" aria-hidden="true"></i>
            </div>
        </div>
    </div>


    <!-- Scripts -->
    <script type="text/javascript" src="https://unpkg.com/canvg@3.0.4/lib/umd.js"></script>
    <script src="{{ asset('js/common.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/user_notification.js') }}"></script>
    <script src="{{ asset('js/chatting.js') }}"></script>
    @yield('scripts')
</body>

</html>
