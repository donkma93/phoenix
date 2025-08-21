<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Phoenix Logistics</title>

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
        <link rel="stylesheet" href="{{ asset('css/home.css') }}">
        <link rel="stylesheet" href="{{ asset('css/icons.css') }}">
        <!-- Scripts -->
        <script src="{{ asset('js/common.js') }}" defer></script>

        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-243072486-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'UA-243072486-1');
        </script>

        <style>
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
    </head>
    <body data-spy="scroll" data-target=".nav-menu" data-offset="144">
        <header class="header fixed-top">
            <div class="container">
                <div class="d-flex align-items-center">
                    <a href="#" class="d-block amr-auto logo-text">
                        PHOENIX
                    </a>
                    <nav class="d-none d-lg-block">
                        <ul class="list-unstyle d-flex nav-menu nav">
                            <li class="nav-item"><a class="nav-link" href="">{{ __('welcome.home') }}</a></li>
                            <li class="nav-item"><a class="nav-link" href="#about">{{ __('welcome.about') }}</a></li>
                            <li class="nav-item"><a class="nav-link" href="#price">{{ __('welcome.pricing') }}</a></li>
                            <li class="nav-item"><a class="nav-link" href="#services">{{ __('welcome.services') }}</a></li>
                            <li class="nav-item"><a class="nav-link" href="#contact">{{ __('welcome.contact') }}</a></li>
                            <li class="nav-item">
                                <a class="nav-link" href="@if(app()->getLocale() == "en") {{ route('home', ['locale' => 'vn']) }} @else # @endif">
                                    <img src="{{ asset('images/vietnam.png') }}" width="25">
                                </a>
                                <a class="nav-link" href="@if(app()->getLocale() == "vn") {{ route('home', ['locale' => 'en']) }} @else # @endif">
                                    <img src="{{ asset('images/english.png') }}" width="25">
                                </a>
                            </li>
                        </ul>
                    </nav>
                    @if (Auth::check())
                        <a href="{{ route(\App\Models\User::$home[auth()->user()->role]) }}" class="nav-link get-started-btn aml-24">Dashboard</a>
                    @else
                        <a href="javascript:;" class="nav-link get-started-btn aml-24" data-toggle="modal" data-target="#login-form">{{ __('welcome.login') }}</a>
                    @endif
                </div>
            </div>
        </header>
        <section id="hero" class="d-flex align-items-center">
            <div class="container" style="z-index: 10">
                <div class="row">
                    <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center apt-lg-0 apt-24">
                        <h1>Phoenix Logistics</h1>
                        <h2>Time is money. We save you both.</h2>
                        <div>
                            <a href="#about" class="nav-link hero-get-started-btn">{{ __('welcome.start') }}</a>
                        </div>
                    </div>
                    <div class="col-lg-6 order-1 order-lg-2">
                        <img src="{{ asset('images/hero-image-home.png') }}" class="hero-img" alt="hero image">
                    </div>
                </div>
            </div>
        </section>
        <main>
            <section id="about" class="apy-60">
                <div class="container">
                    <h2 class="section-title">{{ __('welcome.about') }}</h2>
                    <p class="text-justify">{!! __('welcome.about-content-1') !!}</p>
                    <p class="text-justify">{!! __('welcome.about-content-2') !!}</p>
                    <p class="text-justify">{!! __('welcome.about-content-3') !!}</p>
                    <div class="row text-center amt-30">
                        <div class="col-xl-4 col-md-6 amb-24">
                            <div class="card about-item rounded-lg">
                                <img src="{{ asset('images/about-us-1.jpg') }}" alt="">
                                <h4>Removal</h4>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6 amb-24">
                            <div class="card about-item rounded-lg">
                                <img src="{{ asset('images/about-us-2.png') }}" alt="">
                                <h4>Fulfillment</h4>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6 amb-24">
                            <div class="card about-item rounded-lg">
                                <img src="{{ asset('images/about-us-3.png') }}" alt="">
                                <h4>Relabel</h4>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6 amb-24">
                            <div class="card about-item rounded-lg">
                                <img src="{{ asset('images/about-us-4.png') }}" alt="">
                                <h4>Shipping</h4>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6 amb-24">
                            <div class="card about-item rounded-lg">
                                <img src="{{ asset('images/about-us-5.jpg') }}" alt="">
                                <h4>Repack</h4>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6 amb-24">
                            <div class="card about-item rounded-lg">
                                <img src="{{ asset('images/about-us-6.png') }}" alt="">
                                <h4>Lưu kho</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section id="price" class="apy-60 abg-gray-100 position-relative">
                <div class="container">
                    <h2 class="section-title">{{ __('welcome.pricing-request') }}</h2>
                    <div class="container">
                        <form method="POST" action="{{ route('pricingRequest') }}" class="form-horizontal" role="form" id="pricing-request-form">
                        @csrf
                            <div class="form-group search-form-group">
                                <div class="search-input">
                                    <input type="input" class="form-control w-100" id="request-email" name="email" placeholder="Email*"/>
                                </div>
                            </div>

                            <div class="form-group search-form-group">
                                <div class="search-input">
                                    <input type="input" class="form-control w-100" id="request-company" name="company" placeholder="{{ __('welcome.company') }}*"/>
                                </div>
                            </div>

                            <div class="form-group search-form-group">
                                <div class="search-input">
                                    <input type="input" class="form-control w-100" id="request-name" name="name" placeholder="{{ __('welcome.name') }}*"/>
                                </div>
                            </div>

                            <div class="form-group search-form-group">
                                <div class="search-input">
                                    <input type="input" class="form-control w-100" id="request-phone" name="phone" placeholder="{{ __('welcome.phone') }}*"/>
                                </div>
                            </div>

                            <div class="form-group search-form-group">
                                <div class="search-input">
                                    <textarea class="form-control" name="note" id="request-note" rows="3" placeholder="{{ __('welcome.additional-information') }}"></textarea>
                                </div>  
                            </div>

                            <div class="form-group search-form-group">
                                <label for="email" class="col-form-label search-label"><b>{{ __('Which services are you interested in?*') }}</b></label>
                                <div class="search-input">
                                    <div><input type="checkbox" class="amr-16 aml-8 request-service" name="service" value="E-commerce" />{{ __('welcome.e-commerce') }}</div>
                                    <div><input type="checkbox" class="amr-16 aml-8 request-service" name="service" value="3rd Party Fulfillment" />{{ __('welcome.3rd-party-fulfillment') }}</div>
                                    <div><input type="checkbox" class="amr-16 aml-8 request-service" name="service" value="Kitting" />{{ __('welcome.kitting') }}</div>
                                    <div><input type="checkbox" class="amr-16 aml-8 request-service" name="service" value="Packaging" />{{ __('welcome.packaging') }}</div>
                                    <div><input type="checkbox" class="amr-16 aml-8 request-service" name="service" value="Special Projects" />{{ __('welcome.special-project') }}</div>
                                    <div><input type="checkbox" class="amr-16 aml-8 request-service" name="service" value="Returns Processing" />{{ __('welcome.returns-processing') }}</div>
                                </div>  
                            </div>

                            <div>
                                <div class="btn btn-success" id="send-button" onclick="checkRequestPricing()">{{ __('welcome.send') }}</div>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
            <section id="why-us" class="apy-60">
                <div class="container">
                    <div class="text-center">
                        <h2 class="font-34 atext-indigo-800">{!! __('welcome.why-us') !!}</h2>
                        <p class="font-15 atext-gray-500">{!! __('welcome.why-us-content-1') !!}</p>
                        <img src="{{ asset('images/why-us.png') }}" alt="" class="w-100 d-inline-block amb-24 shadow rounded">
                    </div>
                    <ul class="list-unstyle amt-20" id="why-us-accordion">
                        <li class="abg-white rounded shadow ap-16 amb-4">
                            <a href="#why-1" data-toggle="collapse" aria-expanded="false" class="d-flex align-items-center">
                                <h3 class="flex-grow-1 row align-items-center amb-0">
                                    <span class="col-sm-1 font-24 atext-blue-400 font-weight-bolder">01</span>
                                    <span class="col-sm-11">{!! __('welcome.why-us-strengths-1') !!}</span>
                                </h3>
                                <i class="fa fa-angle-up icon-up" aria-hidden="true"></i>
                                <i class="fa fa-angle-down icon-down" aria-hidden="true"></i>
                            </a>
                            <div id="why-1" class="collapse row" data-parent="#why-us-accordion">
                                <div class="col-sm-11 offset-sm-1">
                                    {!! __('welcome.why-us-strengths-1-content') !!}
                                </div>
                            </div>
                        </li>
                        <li class="abg-white rounded shadow ap-16 amb-4">
                            <a href="#why-2" data-toggle="collapse" aria-expanded="false" class="d-flex align-items-center">
                                <h3 class="flex-grow-1 row align-items-center amb-0">
                                    <span class="col-sm-1 font-24 atext-blue-400 font-weight-bolder">02</span>
                                    <span class="col-sm-11">{!! __('welcome.why-us-strengths-2') !!}</span>
                                </h3>
                                <i class="fa fa-angle-up icon-up" aria-hidden="true"></i>
                                <i class="fa fa-angle-down icon-down" aria-hidden="true"></i>
                            </a>
                            <div id="why-2" class="collapse row" data-parent="#why-us-accordion">
                                <div class="col-sm-11 offset-sm-1">
                                    {!! __('welcome.why-us-strengths-2-content') !!}
                                </div>
                            </div>
                        </li>
                        <li class="abg-white rounded shadow ap-16 amb-4">
                            <a href="#why-3" data-toggle="collapse" aria-expanded="false" class="d-flex align-items-center">
                                <h3 class="flex-grow-1 row align-items-center amb-0">
                                    <span class="col-sm-1 font-24 atext-blue-400 font-weight-bolder">03</span>
                                    <span class="col-sm-11">{!! __('welcome.why-us-strengths-3') !!}</span>
                                </h3>
                                <i class="fa fa-angle-up icon-up" aria-hidden="true"></i>
                                <i class="fa fa-angle-down icon-down" aria-hidden="true"></i>
                            </a>
                            <div id="why-3" class="collapse row" data-parent="#why-us-accordion">
                                <div class="col-sm-11 offset-sm-1">
                                    {!! __('welcome.why-us-strengths-3-content') !!}
                                </div>
                            </div>
                        </li>
                        <li class="abg-white rounded shadow ap-16 amb-4">
                            <a href="#why-4" data-toggle="collapse" aria-expanded="false" class="d-flex align-items-center">
                                <h3 class="flex-grow-1 row align-items-center amb-0">
                                    <span class="col-sm-1 font-24 atext-blue-400 font-weight-bolder">04</span>
                                    <span class="col-sm-11">{!! __('welcome.why-us-strengths-4') !!}</span>
                                </h3>
                                <i class="fa fa-angle-up icon-up" aria-hidden="true"></i>
                                <i class="fa fa-angle-down icon-down" aria-hidden="true"></i>
                            </a>
                            <div id="why-4" class="collapse row" data-parent="#why-us-accordion">
                                <div class="col-sm-11 offset-sm-1">
                                    {!! __('welcome.why-us-strengths-4-content') !!}
                                </div>
                            </div>
                        </li>
                        <li class="abg-white rounded shadow ap-16 amb-4">
                            <a href="#why-5" data-toggle="collapse" aria-expanded="false" class="d-flex align-items-center">
                                <h3 class="flex-grow-1 row align-items-center amb-0">
                                    <span class="col-sm-1 font-24 atext-blue-400 font-weight-bolder">05</span>
                                    <span class="col-sm-11">{!! __('welcome.why-us-strengths-5') !!}</span>
                                </h3>
                                <i class="fa fa-angle-up icon-up" aria-hidden="true"></i>
                                <i class="fa fa-angle-down icon-down" aria-hidden="true"></i>
                            </a>
                            <div id="why-5" class="collapse row" data-parent="#why-us-accordion">
                                <div class="col-sm-11 offset-sm-1"> {!! __('welcome.why-us-strengths-5-content') !!}</div>
                            </div>
                        </li>
                    </ul>
                </div>
            </section>
            <section id="services" class="apy-60 abg-gray-100">
                <div class="container">
                    <h2 class="section-title">{!! __('welcome.services') !!}</h2>
                    <p class=" text-justify">{!! __('welcome.services-content-1') !!}</p>
                    <p class="text-justify amb-32">{!! __('welcome.services-content-2') !!}</p>
                    <div class="row amb-32">
                        <div class="col-xl-4 col-md-6 amb-24">
                            <div class="card h-100 text-center rounded">
                                <h3 class="font-24 amb-10 atext-indigo-800">{!! __('welcome.services-strengths-1') !!}</h3>
                                <p class="font-14 am-0">{!! __('welcome.services-strengths-1-content') !!}</p>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6 amb-24">
                            <div class="card h-100 text-center rounded">
                                <h3 class="font-24 amb-10 atext-indigo-800">{!! __('welcome.services-strengths-2') !!}</h3>
                                <p class="font-14 am-0">{!! __('welcome.services-strengths-2-content') !!}</p>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6 amb-24">
                            <div class="card h-100 text-center rounded">
                                <h3 class="font-24 amb-10 atext-indigo-800">{!! __('welcome.services-strengths-3') !!}</h3>
                                <p class="font-14 am-0">{!! __('welcome.services-strengths-3-content') !!}</p>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6 amb-24">
                            <div class="card h-100 text-center rounded">
                                <h3 class="font-24 amb-10 atext-indigo-800">{!! __('welcome.services-strengths-4') !!}</h3>
                                <p class="font-14 am-0">{!! __('welcome.services-strengths-4-content') !!}</p>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6 amb-24">
                            <div class="card h-100 text-center rounded">
                                <h3 class="font-24 amb-10 atext-indigo-800">{!! __('welcome.services-strengths-5') !!}</h3>
                                <p class="font-14 am-0">{!! __('welcome.services-strengths-5-content') !!}</p>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6 amb-24">
                            <div class="card h-100 text-center rounded">
                                <h3 class="font-24 amb-10 atext-indigo-800">{!! __('welcome.services-strengths-6') !!}</h3>
                                <p class="font-14 am-0">{!! __('welcome.services-strengths-6-content') !!}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-center amb-32">
                        <div class="col-lg-7 order-2 order-lg-1">
                            <p class="font-15 atext-gray-500 text-justify">{!! __('welcome.services-content-3') !!}</p>
                        </div>
                        <div class="col-lg-5 order-1 order-lg-2 amb-12">
                            <img src="{{ asset('images/service-1.png') }}" alt="" class="img-fluid rounded-lg shadow">
                        </div>
                    </div>
                    <div class="row align-items-center amb-32">
                        <div class="col-lg-5 amb-12">
                            <img src="{{ asset('images/service-2.png') }}" alt="" class="img-fluid rounded-lg shadow">
                        </div>
                        <div class="col-lg-7">
                            <h3 class="font-24 atext-indigo-800 service-title">{!! __('welcome.services-function-1') !!}</h3>
                            <p class="font-15 atext-gray-500 text-justify">{!! __('welcome.services-function-1-content') !!}</p>
                        </div>
                    </div>
                    <div class="row align-items-center amb-32">
                        <div class="col-lg-7 order-2 order-lg-1">
                            <h3 class="font-24 atext-indigo-800 service-title">{!! __('welcome.services-function-2') !!}</h3>
                            <p class="font-15 atext-gray-500 text-justify">{!! __('welcome.services-function-2-content') !!}</p>
                        </div>
                        <div class="col-lg-5 order-1 order-lg-2 amb-12">
                            <img src="{{ asset('images/service-3.png') }}" alt="" class="img-fluid rounded-lg shadow">
                        </div>
                    </div>
                    <div class="row align-items-center amb-32">
                        <div class="col-lg-5 amb-12">
                            <img src="{{ asset('images/service-4.jpg') }}" alt="" class="img-fluid rounded-lg shadow">
                        </div>
                        <div class="col-lg-7">
                            <h3 class="font-24 atext-indigo-800 service-title">{!! __('welcome.services-function-3') !!}</h3>
                            <p class="font-15 atext-gray-500 text-justify">{!! __('welcome.services-function-3-content') !!}</p>
                        </div>
                    </div>
                    <div class="row align-items-center amb-32">
                        <div class="col-lg-7 order-2 order-lg-1">
                            <h3 class="font-24 atext-indigo-800 service-title">{!! __('welcome.services-function-4') !!}</h3>
                            <p class="font-15 atext-gray-500 text-justify">{!! __('welcome.services-function-4-content') !!}</p>
                        </div>
                        <div class="col-lg-5 order-1 order-lg-2 amb-12">
                            <img src="{{ asset('images/service-5.jpg') }}" alt="" class="img-fluid rounded-lg shadow">
                        </div>
                    </div>
                    <div class="row align-items-center amb-32">
                        <div class="col-lg-5 amb-12">
                            <img src="{{ asset('images/service-6.png') }}" alt="" class="img-fluid rounded-lg shadow">
                        </div>
                        <div class="col-lg-7">
                            <h3 class="font-24 atext-indigo-800 service-title">{!! __('welcome.services-function-5') !!}</h3>
                            <p class="font-15 atext-gray-500 text-justify">{!! __('welcome.services-function-5-content') !!}</p>
                        </div>
                    </div>
                    <div class="row align-items-center amb-32">
                        <div class="col-lg-7 order-2 order-lg-1">
                            <h3 class="font-24 atext-indigo-800 service-title">{!! __('welcome.services-function-6') !!}</h3>
                            <p class="font-15 atext-gray-500 text-justify">{!! __('welcome.services-function-6-content') !!}</p>
                        </div>
                        <div class="col-lg-5 order-1 order-lg-2 amb-12">
                            <img src="{{ asset('images/service-7.png') }}" alt="" class="img-fluid rounded-lg shadow">
                        </div>
                    </div>
                    <div class="row align-items-center amb-32">
                        <div class="col-lg-5 amb-12">
                            <img src="{{ asset('images/service-8.png') }}" alt="" class="img-fluid rounded-lg shadow">
                        </div>
                        <div class="col-lg-7">
                            <h3 class="font-24 atext-indigo-800 service-title">{!! __('welcome.services-function-7') !!}</h3>
                            <p class="font-15 atext-gray-500 text-justify">{!! __('welcome.services-function-7-content') !!}</p>
                            {!! __('welcome.services-function-7-content-list') !!}
                        </div>
                    </div>
                </div>
            </section>
            <section id="contact" class="apy-60">
                <div class="container">
                    <h2 class="section-title">{!! __('welcome.contact') !!}</h2>
                    <p class="amb-32 text-center">{!! __('welcome.contact-content-1') !!}</p>
                    <div class="contact-card">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="info">
                                    <div class="info-icon"><i class="fa fa-map-marker" aria-hidden="true"></i></div>
                                   <div>
                                       <h3 class="font-22 atext-indigo-800 amb-4">Location:</h3>
                                       <p class="font-14 atext-indigo-500 amb-0">Số 1,ngõ 16 Đỗ Xuân Hợp, phường Mỹ Đình 1, quận Nam Từ Liêm, Hà Nội</p>
                                   </div>
                                </div>
                                <div class="info">
                                    <div class="info-icon"><i class="fa fa-envelope" aria-hidden="true"></i></div>
                                   <div>
                                       <h3 class="font-22 atext-indigo-800 amb-4">Email:</h3>
                                       <a href="mailto:#" class="font-14 info-link">info@phoenixlogistics.vn</a>
                                   </div>
                                </div>
                                <div class="info">
                                    <div class="info-icon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                                   <div>
                                       <h3 class="font-22 atext-indigo-800 amb-4">Call:</h3>
                                       <p class="font-14 atext-indigo-500"><a href="tel:+84868413333">086.841.3333</a></p>
                                   </div>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="map-box">
                                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.282881565734!2d105.7617774!3d21.0213643!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x313454a17ed8509f%3A0x4b6c683c8cfd1226!2zMSBOZ8O1IDE2IMSQ4buXIFh1w6JuIEjhu6NwLCBN4bu5IMSQw6xuaCwgVOG7qyBMacOqbSwgSMOgIE7hu5lp!5e0!3m2!1sen!2s!4v1680659748307!5m2!1sen!2s" class="map" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                    <!--<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3048.2318303274046!2d-74.25296578469654!3d40.18165387782684!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c17fc17dfe279f%3A0xf6cd35d8a40ebcb1!2s2248%20U.S.%209%2C%20Howell%20Township%2C%20NJ%2007731%2C%20Hoa%20K%E1%BB%B3!5e0!3m2!1svi!2s!4v1648052351687!5m2!1svi!2s" class="map" allowfullscreen="" loading="lazy"></iframe>-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
        <footer>
            <div class="footer-top apt-60 apb-30 abg-gray-100">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 amb-30">
                            <h2 class="font-28 text-uppercase amb-12 atext-indigo-800" style="line-height: 1">Phoenix LLC</h2>
                            <p class="font-14 amb-0 atext-gray-400">
                                Số 1,ngõ 16 Đỗ Xuân Hợp<br />
                                phường Mỹ Đình 1, quận Nam Từ Liêm, Hà Nội<br />
                                Việt Nam<br><br>
                                <strong>Phone:</strong> <a href="tel:+84868413333">086.841.3333</a><br>
                                <strong>Email:</strong> info@phoenix.com
                            </p>
                        </div>
                        <div class="col-lg-3 col-md-6 amb-30">
                            <h2 class="font-16 amb-12 atext-indigo-800">{!! __('welcome.link') !!}</h2>
                            <ul class="list-unstyle apl-8">
                                <li class="amb-16">
                                    <div class="d-flex align-items-center">
                                        <i class="fa fa-angle-right amr-8 atext-blue-400"></i>
                                        <a href="#hero" class="nav-link footer-link">{!! __('welcome.home') !!}</a>
                                    </div>
                                </li>
                                <li class="amb-16">
                                    <div class="d-flex align-items-center">
                                        <i class="fa fa-angle-right amr-8 atext-blue-400"></i>
                                        <a href="#about" class="nav-link footer-link">{!! __('welcome.about') !!}</a>
                                    </div>
                                </li>
                                <li class="amb-16">
                                    <div class="d-flex align-items-center">
                                        <i class="fa fa-angle-right amr-8 atext-blue-400"></i>
                                        <a href="#price" class="nav-link footer-link">{!! __('welcome.pricing') !!}</a>
                                    </div>
                                </li>
                                <li class="amb-16">
                                    <div class="d-flex align-items-center">
                                        <i class="fa fa-angle-right amr-8 atext-blue-400"></i>
                                        <a href="#services" class="nav-link footer-link">{!! __('welcome.services') !!}</a>
                                    </div>
                                </li>
                                <li class="amb-16">
                                    <div class="d-flex align-items-center">
                                        <i class="fa fa-angle-right amr-8 atext-blue-400"></i>
                                        <a href="{{ route('term', ['locale' => app()->getLocale()]) }}" class="nav-link footer-link">{!! __('welcome.terms-of-service-title-1') !!}</a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="col-lg-3 col-md-6 amb-30">
                            <h2 class="font-16 amb-12 atext-indigo-800">{!! __('welcome.services') !!}</h2>
                            <ul class="list-unstyle apl-8">
                                <li class="amb-16">
                                    <div class="d-flex align-items-center">
                                        <i class="fa fa-angle-right amr-8 atext-blue-400"></i>
                                        <a href="#services" class="nav-link footer-link">Re-label</a>
                                    </div>
                                </li>
                                <li class="amb-16">
                                    <div class="d-flex align-items-center">
                                        <i class="fa fa-angle-right amr-8 atext-blue-400"></i>
                                        <a href="#services" class="nav-link footer-link">{!! __('welcome.storage') !!}</a>
                                    </div>
                                </li>
                                <li class="amb-16">
                                    <div class="d-flex align-items-center">
                                        <i class="fa fa-angle-right amr-8 atext-blue-400"></i>
                                        <a href="#services" class="nav-link footer-link">Fulfillment</a>
                                    </div>
                                </li>
                                <li class="amb-16">
                                    <div class="d-flex align-items-center">
                                        <i class="fa fa-angle-right amr-8 atext-blue-400"></i>
                                        <a href="#services" class="nav-link footer-link">{!! __('welcome.advise') !!}</a>
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
            <div class="abg-indigo-800 apy-30">
                <div class="container">
                    <p class="font-14 text-white amb-0">© Copyright <strong>Phoenix</strong>. All Rights Reserved</p>
                </div>
            </div>
        </footer>

        {{-- scroll to top button --}}
        <div id="scroll-top-btn" class="show">
            <i class="fa fa-arrow-up text-white"></i>
        </div>

        <a href="tel:+18484448939" id="call-button">
            <i class="fa fa-phone" style="color:white"></i>
        </a>

        {{-- mobile nav menu --}}
        <i class="fa fa-bars d-block d-lg-none" id="mobile-menu-btn" data-toggle="modal" data-target="#mobile-menu"></i>
        <div class="modal fade" id="mobile-menu" tabindex="-1" role="dialog"aria-hidden="true">
            <div class="modal-dialog h-100 apx-16 apb-16 apt-50" role="document">
              <div class="modal-content arounded-6 h-100">
                <div class="modal-body">
                    <ul class="list-unstyle d-flex flex-column nav-menu nav">
                        <li class="nav-item"><a class="nav-link" href="">{{ __('welcome.home') }}</a></li>
                        <li class="nav-item"><a class="nav-link" href="#about">{{ __('welcome.about') }}</a></li>
                        <li class="nav-item"><a class="nav-link" href="#price">{{ __('welcome.pricing') }}</a></li>
                        <li class="nav-item"><a class="nav-link" href="#services">{{ __('welcome.services') }}</a></li>
                        <li class="nav-item"><a class="nav-link" href="#contact">{{ __('welcome.contact') }}</a></li>
                        <li class="nav-item">
                            <a class="nav-link" href="@if(app()->getLocale() == "en") {{ route('home', ['locale' => 'vn']) }} @else # @endif">
                                <img src="{{ asset('images/vietnam.png') }}" width="25">
                            </a>
                            <a class="nav-link" href="@if(app()->getLocale() == "vn") {{ route('home', ['locale' => 'en']) }} @else # @endif">
                                <img src="{{ asset('images/english.png') }}" width="25">
                            </a>
                        </li>
                    </ul>
                </div>
              </div>
            </div>
        </div>

        {{-- login form --}}
        <form class="modal fade" method="POST" action="{{ route('login', ['locale' => app()->getLocale()]) }}" id="login-form" tabindex="-1" role="dialog">
            @csrf
            <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content amx-auto arounded-6">
                <div class="modal-header">
                    <h2 class="modal-title text-center">{!! __('welcome.login') !!}</h2>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="email">{{ __('Email') }}</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="off" autofocus>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div class="form-group amb-24">
                        <label for="password">{!! __('welcome.password') !!}</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="off">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                    <div class="form-group d-flex align-items-center justify-content-between">
                        <div class="form-check">
                            <input class="form-check-input amt-6" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">{!! __('welcome.remember-me') !!}</label>
                        </div>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request', ['locale' => app()->getLocale()]) }}">{!! __('welcome.forgot-password') !!}</a>
                        @endif
                    </div>

                    <div class="form-group am-0">
                        <button type="submit" class="submit-btn apy-8">
                            {!! __('welcome.login') !!}
                        </button>
                        <div class="amt-4">
                            {!! __('welcome.dont-have-account') !!}
                            <a href="{{ route('register', ['locale' => app()->getLocale()]) }}">
                                {!! __('welcome.register-now') !!}
                            </a>
                        </div>
                    </div>
                </div>
              </div>
            </div>
        </form>
        
        <script src="{{ asset('js/home.js') }}"></script>
        @if ($errors->any())
        <script>
            $('#login-form').modal('show')
        </script>
        @endif
        <script>
            async function checkRequestPricing() {
                if($('#send-button').text() === "{{__('welcome.sent')}}") {
                    return;
                }

                $('#request-email').removeClass('is-invalid');
                $('#request-company').removeClass('is-invalid');
                $('#request-name').removeClass('is-invalid');
                $('#request-phone').removeClass('is-invalid');
                const email = $('#request-email').val()
                if(!email || email === "") {
                    $('#request-email').addClass('is-invalid')

                    return
                }

                if(!email.match(/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/)) {
                    $('#request-email').addClass('is-invalid')

                    return
                }

                const company = $('#request-company').val()
                if(!company || company === "") {
                    $('#request-company').addClass('is-invalid')

                    return
                }

                const name = $('#request-name').val()
                if(!name || name === "") {
                    $('#request-name').addClass('is-invalid')
                    return
                }

                const phone = $('#request-phone').val()
                if(!phone || phone === "") {
                    $('#request-phone').addClass('is-invalid')
                    return
                }

                if(!phone.match(/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im)) {
                    $('#request-phone').addClass('is-invalid')

                    return
                }

                let services = "";
                $("input:checkbox[name=service]:checked").each(function(){
                    services += $(this).val() + ","
                });

                if(services === "") {
                    $('#request-service').addClass('is-invalid')
                    return
                }
                
                if (services[services.length-1] === ",")services = services.slice(0,-1);

                const note = $('#request-note').val()

                loading(true);
                await $.ajax({
                    type: 'POST',
                    url: "{{ route('pricingRequest') }}",
                    data: {
                        email,
                        name,
                        phone,
                        company,
                        note,
                        services,
                        _token: '{{csrf_token()}}'
                    },
                    success:function(data) {
                        loading(false);
                        $('#send-button').text("{{__('welcome.sent')}}");
                        $('#send-button').addClass('btn-secondary')
                        $('#send-button').removeClass('btn-success')
                        $('#send-button').removeClass('btn-danger')
                        $('#request-email').attr('disabled','disabled');
                        $('#request-company').attr('disabled','disabled');
                        $('#request-name').attr('disabled','disabled');
                        $('#request-phone').attr('disabled','disabled');
                        $('#request-note').attr('disabled','disabled');
                        $('#request-service').attr('disabled','disabled');
                    },
                    error: function(e) {
                        loading(false);
                        $('#send-button').text("{{__('welcome.resend')}}");
                        $('#send-button').addClass('btn-danger')
                        $('#send-button').removeClass('btn-success')
                    }
                });
            }
        </script>
        <div id="loading" style="display: none;">
            <div class="loader">Loading...</div>
        </div>
    </body>
</html>
