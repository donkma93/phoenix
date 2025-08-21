@extends('layouts.staff')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Home',
            'url' => route('home', ['locale' => app()->getLocale()])
        ],
        [
            'text' => 'Dashboard'
        ]
    ]
])
@endsection

@section('content')
<div class="px-4 px-md-0">
    <h2>Request Summary</h2>
    <hr>
    <div class="row justify-content-center justify-content-md-between">
        @foreach($requests as $request)
        <a class="col-sm-6 col-md-4 amb-24 link-card" href="{{ route('staff.request.list', ['type' => $request['m_request_type_id']]) }}">
            <div class="card arounded-8 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">
                        <div class="atext-gray-500 text-uppercase">
                            REQUEST {{  strtoupper($request['statusName']) }}
                        </div>
                        <div class="font-32 {{ $request['color'] }}">
                            {{ $request['total'] }}
                        </div>
                    </div>
                    <div class="card-icon flex-center rounded-circle bg-primary">
                        <i class="fa {{ $request['icon'] }} font-24 text-white"></i>
                    </div>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</div>

<br /><br />

<div class="px-4 px-md-0">
    <h2>Order Summary</h2>
    <hr>
    <div class="row justify-content-center justify-content-md-between">
        @foreach($orders as $order)
        <a class="col-sm-6 col-md-4 amb-24 link-card" href="{{ route('staff.orders.list', ['fulfillment' => $order['fulfillment']]) }}">
            <div class="card arounded-8 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">
                        <div class="atext-gray-500 text-uppercase">
                            ORDER {{  strtoupper($order['fulfillmentName']) }}
                        </div>
                        <div class="font-32 {{ $order['color'] }}">
                            {{ $order['total'] }}
                        </div>
                    </div>
                    <div class="card-icon flex-center rounded-circle bg-primary">
                        <i class="fa {{ $order['icon'] }} font-24 text-white"></i>
                    </div>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</div>

<br /><br />

<div class="px-4 px-md-0">
    <h2>Items Summary</h2>
    <hr>
    <div class="row justify-content-center justify-content-md-between">
        <a class="col-sm-6 col-md-4 amb-24 link-card" href="#">
            <div class="card arounded-8 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">
                        <div class="atext-gray-500 text-uppercase">
                            ITEM PICK TODAY
                        </div>
                        <div class="font-32 atext-green-500">
                            {{ $itemsPickToday }}
                        </div>
                    </div>
                    <div class="card-icon flex-center rounded-circle bg-primary">
                        <i class="fa fa-get-pocket font-24 text-white"></i>
                    </div>
                </div>
            </div>
        </a>
        <a class="col-sm-6 col-md-4 amb-24 link-card" href="#">
            <div class="card arounded-8 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">
                        <div class="atext-gray-500 text-uppercase">
                            ITEM DUETODAY
                        </div>
                        <div class="font-32 atext-red-500">
                            {{ $itemsDueToday }}
                        </div>
                    </div>
                    <div class="card-icon flex-center rounded-circle bg-primary">
                        <i class="fa fa-exclamation font-24 text-white"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection
