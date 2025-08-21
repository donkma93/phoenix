@extends('layouts.app', [
    'class' => '',
    'folderActive' => '',
    'elementActive' => 'dashboard',
])

@section('styles')
    <style>
        .text-decoration-none {
            text-decoration: none !important;
        }
    </style>
@endsection

@section('content')



    <div class="content">
        @if (session('success'))
            <div class="row justify-content-end">
                <div class="col-md-4">
                    <div class="alert alert-success alert-dismissible fade show">
                        <button type="button" aria-hidden="true" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="nc-icon nc-simple-remove"></i>
                        </button>
                        <span>
                            <b> Success - </b>
                            {{ session('success') }}
                        </span>
                    </div>
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="row justify-content-end">
                <div class="col-md-4">
                    <div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" aria-hidden="true" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="nc-icon nc-simple-remove"></i>
                        </button>
                        <span>
                            <b> Error - </b>
                            {{ session('error') }}
                        </span>
                    </div>
                </div>
            </div>
        @endif
        <div class="mt-5">
            <div>
                <h4 class="mb-0">Request Summary</h4>
                <hr>
            </div>
            <div class="row">
                @if (isset($requests))
                    @foreach ($requests as $request)
                        <div class="col-xl-4 col-lg-6 col-12">
                            <a class="card card-stats py-3 text-decoration-none"
                                href="{{ route('staff.request.list', ['type' => $request['m_request_type_name']]) }}">
                                <div class="card-body ">
                                    <div class="row">
                                        <div class="col-5 col-md-4">
                                            <div class="icon-big text-center icon-warning">
                                                <i class="{{ $request['icon'] }} {{ $request['color'] }}"></i>
                                            </div>
                                        </div>
                                        <div class="col-7 col-md-8">
                                            <div class="numbers">
                                                <p class="card-category">REQUEST {{ strtoupper($request['statusName']) }}
                                                </p>
                                                <p class="card-title">{{ $request['total'] }}
                                                <p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
        <div class="mt-5">
            <div>
                <h4 class="mb-0">Order Summary</h4>
                <hr>
            </div>
            <div class="row">
                @if (isset($orders))
                    @foreach ($orders as $order)
                        <div class="col-xl-4 col-lg-6 col-12">
                            <a class="card card-stats py-3 text-decoration-none"
                                href="{{ route('staff.orders.list', ['fulfillment' => $order['fulfillment']]) }}">
                                <div class="card-body ">
                                    <div class="row">
                                        <div class="col-5 col-md-4">
                                            <div class="icon-big text-center icon-warning">
                                                <i class="{{ $order['icon'] }} {{ $order['color'] }}"></i>
                                            </div>
                                        </div>
                                        <div class="col-7 col-md-8">
                                            <div class="numbers">
                                                <p class="card-category">ORDER {{ strtoupper($order['fulfillmentName']) }}
                                                </p>
                                                <p class="card-title">{{ $order['total'] }}
                                                <p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
        <div class="mt-5">
            <div>
                <h4 class="mb-0">Items Summary</h4>
                <hr>
            </div>
            <div class="row justify-content-between">
                <div class="col-xl-4 col-lg-6 col-12">
                    <a class="card card-stats py-3 text-decoration-none" href="#">
                        <div class="card-body ">
                            <div class="row">
                                <div class="col-5 col-md-4">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="nc-icon nc-check-2 text-danger"></i>
                                    </div>
                                </div>
                                <div class="col-7 col-md-8">
                                    <div class="numbers">
                                        <p class="card-category">ITEM PICK TODAY</p>
                                        <p class="card-title">{{ isset($itemsPickToday) ? $itemsPickToday : '' }}
                                        <p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xl-4 col-lg-6 col-12">
                    <a class="card card-stats py-3 text-decoration-none" href="#">
                        <div class="card-body ">
                            <div class="row">
                                <div class="col-5 col-md-4">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="nc-icon nc-alert-circle-i text-primary"></i>
                                    </div>
                                </div>
                                <div class="col-7 col-md-8">
                                    <div class="numbers">
                                        <p class="card-category">ITEM DUETODAY</p>
                                        <p class="card-title">{{ isset($itemsDueToday) ? $itemsDueToday : '' }}
                                        <p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="row mt-5" id="dashboard-tour">
            <div class="col-lg-4 col-sm-6">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-7">
                                <div class="numbers pull-left">
                                    $34,657
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="pull-right">
                                    <span class="badge badge-pill badge-success">
                                        +18%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="big-title">total earnings in last ten quarters</h6>
                        <canvas id="activeUsers" width="826" height="380"></canvas>
                    </div>
                    <div class="card-footer">
                        <hr>
                        <div class="row">
                            <div class="col-sm-7">
                                <div class="footer-title">Financial Statistics</div>
                            </div>
                            <div class="col-sm-5">
                                <div class="pull-right">
                                    <button class="btn btn-success btn-round btn-icon btn-sm">
                                        <i class="nc-icon nc-simple-add"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-6">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-7">
                                <div class="numbers pull-left">
                                    169
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="pull-right">
                                    <span class="badge badge-pill badge-danger">
                                        -14%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="big-title">total subscriptions in last 7 days</h6>
                        <canvas id="emailsCampaignChart" width="826" height="380"></canvas>
                    </div>
                    <div class="card-footer">
                        <hr>
                        <div class="row">
                            <div class="col-sm-7">
                                <div class="footer-title">View all members</div>
                            </div>
                            <div class="col-sm-5">
                                <div class="pull-right">
                                    <button class="btn btn-danger btn-round btn-icon btn-sm">
                                        <i class="nc-icon nc-button-play"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-6">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-7">
                                <div class="numbers pull-left">
                                    8,960
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="pull-right">
                                    <span class="badge badge-pill badge-warning">
                                        ~51%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="big-title">total downloads in last 6 years</h6>
                        <canvas id="activeCountries" width="826" height="380"></canvas>
                    </div>
                    <div class="card-footer">
                        <hr>
                        <div class="row">
                            <div class="col-sm-7">
                                <div class="footer-title">View more details</div>
                            </div>
                            <div class="col-sm-5">
                                <div class="pull-right">
                                    <button class="btn btn-warning btn-round btn-icon btn-sm">
                                        <i class="nc-icon nc-alert-circle-i"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
@endsection

@push('scripts')
    <script>

        $(document).ready(function() {
            // Javascript method's body can be found in assets/js/demos.js
            demo.initDashboardPageCharts();
            demo.initVectorMap();
        });
    </script>
@endpush
