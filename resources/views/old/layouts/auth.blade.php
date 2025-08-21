<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Warehouse') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
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
    </head>
    <body>
        <div id="auth" class="abg-gray-100 min-vh-100 d-flex flex-column">
            <header class="py-3 bg-white">
                <div class="container">
                    <div class="d-flex align-items-center">
                        @php
                            $path = Route::current()->getName();
                        @endphp
                        
                        <h1 class="logo-text atext-indigo-800"><a href="{{ route('home', ['locale' => app()->getLocale()]) }}">phoenix</a></h1>
                        <nav class="d-lg-block">
                            <ul class="d-flex list-unstyle nav-menu nav">
                                <li class="nav-item">
                                    <a href="@if(app()->getLocale() == "en") {{ route($path, ['locale' => 'vn']) }} @else # @endif">
                                        <img src="{{ asset('images/vietnam.png') }}" width="25">
                                    </a>
                                    <a href="@if(app()->getLocale() == "vn") {{ route($path, ['locale' => 'en']) }} @else # @endif">
                                        <img src="{{ asset('images/english.png') }}" width="25">
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </header>
            <div class="flex-grow-1 d-flex align-items-center apy-60">
                @yield('content')
            </div>
            <footer>
                <div class="footer-top apt-60 apb-30 bg-white">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-3 col-md-6 amb-30">
                                <h2 class="font-28 text-uppercase amb-12 atext-indigo-800" style="line-height: 1">leuleu</h2>
                                <p class="font-14 amb-0 atext-gray-400">
                                    2248 Us Highway 9<br />
                                    Howell, NJ 07731<br />
                                    United States<br><br>
                                    <strong>Phone:</strong> <a href="tel:+18484448939">848.444.8939</a><br>
                                    <strong>Email:</strong> info@leuleullc.com
                                </p>
                            </div>
                            <div class="col-lg-3 col-md-6 amb-30">
                                <h2 class="font-16 amb-12 atext-indigo-800 font-weight-bold">{{ __('Links') }}</h2>
                                <ul class="list-unstyled apl-8">
                                    <li class="amb-16">
                                        <div class="d-flex align-items-center">
                                            <i class="fa fa-angle-right amr-8 atext-blue-400"></i>
                                            <a href="/#hero" class="footer-link">{!! __('welcome.home') !!}</a>
                                        </div>
                                    </li>
                                    <li class="amb-16">
                                        <div class="d-flex align-items-center">
                                            <i class="fa fa-angle-right amr-8 atext-blue-400"></i>
                                            <a href="/#about" class="footer-link">{!! __('welcome.about') !!}</a>
                                        </div>
                                    </li>
                                    <li class="amb-16">
                                        <div class="d-flex align-items-center">
                                            <i class="fa fa-angle-right amr-8 atext-blue-400"></i>
                                            <a href="/#price" class="footer-link">{!! __('welcome.pricing') !!}</a>
                                        </div>
                                    </li>
                                    <li class="amb-16">
                                        <div class="d-flex align-items-center">
                                            <i class="fa fa-angle-right amr-8 atext-blue-400"></i>
                                            <a href="/#services" class="footer-link">{!! __('welcome.services') !!}</a>
                                        </div>
                                    </li>
                                    <li class="amb-16">
                                        <div class="d-flex align-items-center">
                                            <i class="fa fa-angle-right amr-8 atext-blue-400"></i>
                                            <a href="{{ route('term', ['locale' => app()->getLocale()]) }}" class="footer-link">{!! __('welcome.terms-of-service-title-1') !!}</a>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-lg-3 col-md-6 amb-30">
                                <h2 class="font-16 amb-12 atext-indigo-800 font-weight-bold">{!! __('welcome.services') !!}</h2>
                                <ul class="list-unstyled apl-8">
                                    <li class="amb-16">
                                        <div class="d-flex align-items-center">
                                            <i class="fa fa-angle-right amr-8 atext-blue-400"></i>
                                            <a href="/#services" class="footer-link">{{ __('Re-label') }}</a>
                                        </div>
                                    </li>
                                    <li class="amb-16">
                                        <div class="d-flex align-items-center">
                                            <i class="fa fa-angle-right amr-8 atext-blue-400"></i>
                                            <a href="/#services" class="footer-link">{!! __('welcome.storage') !!}</a>
                                        </div>
                                    </li>
                                    <li class="amb-16">
                                        <div class="d-flex align-items-center">
                                            <i class="fa fa-angle-right amr-8 atext-blue-400"></i>
                                            <a href="/#services" class="footer-link">{{ __('Fulfillment') }}</a>
                                        </div>
                                    </li>
                                    <li class="amb-16">
                                        <div class="d-flex align-items-center">
                                            <i class="fa fa-angle-right amr-8 atext-blue-400"></i>
                                            <a href="/#services" class="footer-link">{!! __('welcome.advise') !!}</a>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-lg-3 col-md-6 amb-30">
                                <h2 class="font-16 amb-12 atext-indigo-800">{!! __('welcome.social-network') !!}</h2>
                                <p>{!! __('welcome.connect-with-us') !!}</p>
                                <div class="d-flex flex-wrap">
                                    <a href="" class="social-link">
                                        <i class="fa fa-twitter"></i>
                                        <span class="d-none">twitter</span>
                                    </a>
                                    <a href="" class="social-link">
                                        <i class="fa fa-facebook"></i>
                                        <span class="d-none">facebook</span>
                                    </a>
                                    <a href="" class="social-link">
                                        <i class="fa fa-instagram"></i>
                                        <span class="d-none">instagram</span>
                                    </a>
                                    <a href="" class="social-link">
                                        <i class="fa fa-skype"></i>
                                        <span class="d-none">skype</span>
                                    </a>
                                    <a href="" class="social-link">
                                        <i class="fa fa-linkedin"></i>
                                        <span class="d-none">linkedin</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="abg-indigo-800 apy-20">
                    <div class="container">
                        <p class="font-14 text-white amb-0">© Copyright <strong>Leuleu</strong>. All Rights Reserved</p>
                    </div>
                </div>
            </footer>
        </div>
        <!-- Scripts -->
        <script src="{{ asset('js/common.js') }}"></script>
        @yield('scripts')
    </body>
</html>
