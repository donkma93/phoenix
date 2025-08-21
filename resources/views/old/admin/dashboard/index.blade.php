@extends('layouts.admin')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard'
        ]
    ]
])
@endsection

@section('content')
<div class="px-4">
    <h2>{{ __('Order Due today') }}</h2>
    <hr>
    <div class="row justify-content-center justify-content-md-between">
        <div class="col-sm-6 col-md-4 amb-24">
            <div class="card arounded-6 border-dark h-100">
                <div class="card-body text-center apx-8">
                    <div class="font-64 atext-blue-500">{{ $dueTodayTotal[App\Models\Order::PICKING_PENDING] ?? 0  }}</div>
                    <div class="text-uppercase flex-center atext-gray-600 font-weight-medium">
                        <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width: 16px; height: 16px">
                            <i class="fa fa-plus font-8 atext-gray-400"></i>
                        </span>
                        {{ __('Pending') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 amb-24">
            <div class="card arounded-6 border-dark h-100">
                <div class="card-body text-center apx-8">
                    <div class="font-64 atext-red-500">{{ $dueTodayTotal[App\Models\Order::PICKING_INTOTE] ?? 0 }}</div>
                    <div class="text-uppercase flex-center atext-gray-600 font-weight-medium">
                        <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width: 16px; height: 16px">
                            <i class="fa fa-file-o font-8 atext-gray-400"></i>
                        </span>
                        {{ __('In tote') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 amb-24">
            <div class="card arounded-6 border-dark h-100">
                <div class="card-body text-center apx-8">
                    <div class="font-64 atext-green-500">{{ $dueTodayTotal[App\Models\Order::PICKING_FULFILLED] ?? 0 }}</div>
                    <div class="text-uppercase flex-center atext-gray-600 font-weight-medium">
                        <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width: 16px; height: 16px">
                            <i class="fa fa-spinner font-8 atext-gray-400"></i>
                        </span>
                        {{ __('Fulfill') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="px-4">
    <h2>{{ __('Order Ready to ship') }}</h2>
    <hr>
    <div class="row justify-content-center justify-content-md-between">
        <div class="col-sm-6 col-md-4 amb-24">
            <div class="card arounded-6 border-dark h-100">
                <div class="card-body text-center apx-8">
                    <div class="font-64 atext-blue-500">{{ $readyToShipTotal[App\Models\Order::PICKING_PENDING] ?? 0  }}</div>
                    <div class="text-uppercase flex-center atext-gray-600 font-weight-medium">
                        <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width: 16px; height: 16px">
                            <i class="fa fa-plus font-8 atext-gray-400"></i>
                        </span>
                        {{ __('Pending') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 amb-24">
            <div class="card arounded-6 border-dark h-100">
                <div class="card-body text-center apx-8">
                    <div class="font-64 atext-red-500">{{ $readyToShipTotal[App\Models\Order::PICKING_INTOTE] ?? 0 }}</div>
                    <div class="text-uppercase flex-center atext-gray-600 font-weight-medium">
                        <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width: 16px; height: 16px">
                            <i class="fa fa-file-o font-8 atext-gray-400"></i>
                        </span>
                        {{ __('In tote') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 amb-24">
            <div class="card arounded-6 border-dark h-100">
                <div class="card-body text-center apx-8">
                    <div class="font-64 atext-green-500">{{ $readyToShipTotal[App\Models\Order::PICKING_FULFILLED] ?? 0 }}</div>
                    <div class="text-uppercase flex-center atext-gray-600 font-weight-medium">
                        <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width: 16px; height: 16px">
                            <i class="fa fa-spinner font-8 atext-gray-400"></i>
                        </span>
                        {{ __('Fulfill') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="px-4">
    <h2>{{ __('Package Summary') }}</h2>
    <hr>
    <div class="row">
        <div class="col-sm-6 col-md-4 amb-24">
            <div class="card arounded-6 border-info h-100">
                <div class="card-body text-center apx-8">
                    <div class="font-64 atext-blue-500">{{ $packageTotal }}</div>
                    <div class="text-uppercase flex-center atext-gray-600 font-weight-medium">
                        <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width: 16px; height: 16px">
                            <i class="fa fa-calculator font-8 atext-gray-400"></i>
                        </span>
                        {{ __('Total Package') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 amb-24">
            <div class="card arounded-6 border-info h-100">
                <div class="card-body text-center apx-8">
                    <div class="font-64 atext-red-500">{{ $packageItems[App\Models\Package::STATUS_SHIPPING] ?? 0 }}</div>
                    <div class="text-uppercase flex-center atext-gray-600 font-weight-medium">
                        <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width: 16px; height: 16px">
                            <i class="fa fa-truck font-8 atext-gray-400"></i>
                        </span>
                        {{ __('Package Shipping') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 amb-24">
            <div class="card arounded-6 border-info h-100">
                <div class="card-body text-center apx-8">
                    <div class="font-64 atext-green-500">{{ $packageItems[App\Models\Package::STATUS_INBOUND] ?? 0 }}</div>
                    <div class="text-uppercase flex-center atext-gray-600 font-weight-medium">
                        <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width: 16px; height: 16px">
                            <i class="fa fa-cube font-8 atext-gray-400"></i>
                        </span>
                        {{ __('Package Inbound') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 amb-24">
            <div class="card arounded-6 border-info h-100">
                <div class="card-body text-center apx-8">
                    <div class="font-64 atext-yellow-500">{{ $packageItems[App\Models\Package::STATUS_STORED] ?? 0 }}</div>
                    <div class="text-uppercase flex-center atext-gray-600 font-weight-medium">
                        <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width: 16px; height: 16px">
                            <i class="fa fa-bookmark font-8 atext-gray-400"></i>
                        </span>
                        {{ __('Package Stored') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 amb-24">
            <div class="card arounded-6 border-info h-100">
                <div class="card-body text-center apx-8">
                    <div class="font-64 atext-gray-500">{{ $packageItems[App\Models\Package::STATUS_OUTBOUND] ?? 0 }}</div>
                    <div class="text-uppercase flex-center atext-gray-600 font-weight-medium">
                        <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width: 16px; height: 16px">
                            <i class="fa fa-arrow-right font-8 atext-gray-400"></i>
                        </span>
                        {{ __('Package Outbound') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br><br>

<div class="px-4">
    <h2>{{ __('Request Summary') }}</h2>
    <hr>
    <div class="row justify-content-center justify-content-md-between">
        <div class="col-sm-6 col-md-4 amb-24">
            <div class="card arounded-6 border-dark h-100">
                <div class="card-body text-center apx-8">
                    <div class="font-64 atext-blue-500">{{ $requestTotal }}</div>
                    <div class="text-uppercase flex-center atext-gray-600 font-weight-medium">
                        <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width: 16px; height: 16px">
                            <i class="fa fa-plus font-8 atext-gray-400"></i>
                        </span>
                        {{ __('Total Request') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 amb-24">
            <div class="card arounded-6 border-dark h-100">
                <div class="card-body text-center apx-8">
                    <div class="font-64 atext-red-500">{{ $requestItems[App\Models\UserRequest::STATUS_NEW] ?? 0 }}</div>
                    <div class="text-uppercase flex-center atext-gray-600 font-weight-medium">
                        <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width: 16px; height: 16px">
                            <i class="fa fa-file-o font-8 atext-gray-400"></i>
                        </span>
                        {{ __('Request New') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 amb-24">
            <div class="card arounded-6 border-dark h-100">
                <div class="card-body text-center apx-8">
                    <div class="font-64 atext-green-500">{{ $requestItems[App\Models\UserRequest::STATUS_INPROGRESS] ?? 0 }}</div>
                    <div class="text-uppercase flex-center atext-gray-600 font-weight-medium">
                        <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width: 16px; height: 16px">
                            <i class="fa fa-spinner font-8 atext-gray-400"></i>
                        </span>
                        {{ __('Request Inprogress') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 amb-24">
            <div class="card arounded-6 border-dark h-100">
                <div class="card-body text-center apx-8">
                    <div class="font-64 atext-yellow-500">{{ $requestItems[App\Models\UserRequest::STATUS_DONE] ?? 0 }}</div>
                    <div class="text-uppercase flex-center atext-gray-600 font-weight-medium">
                        <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width: 16px; height: 16px">
                            <i class="fa fa-check font-8 atext-gray-400"></i>
                        </span>
                        {{ __('Request Done') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="px-4">
    <h2>{{ __('Member Summary') }}</h2>
    <hr>
    <div class="row justify-content-center justify-content-md-between">
        <div class="col-sm-6 col-md-4 amb-24">
            <div class="card arounded-6 border-info h-100">
                <div class="card-body text-center apx-8">
                    <div class="font-64 atext-blue-500">{{ $userTotal }}</div>
                    <div class="text-uppercase flex-center atext-gray-600 font-weight-medium">
                        <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width: 16px; height: 16px">
                            <i class="fa fa-calculator font-8 atext-gray-400"></i>
                        </span>
                        {{ __('Total Member') }}
                    </div>
                </div>
            </div>
        </div>
        @php
            $picker = $userCount[App\Models\User::ROLE_PICKER] ?? 0;
            $packer = $userCount[App\Models\User::ROLE_PACKER] ?? 0;
            $receiver = $userCount[App\Models\User::ROLE_RECEIVER] ?? 0;
            $staff = $userCount[App\Models\User::ROLE_STAFF] ?? 0;
            $totalStaff = $picker + $packer + $receiver + $staff;
        @endphp
        <div class="col-sm-6 col-md-4 amb-24">
            <div class="card arounded-6 border-info h-100">
                <div class="card-body text-center apx-8">
                    <div class="font-64 atext-red-500">{{ $totalStaff }}</div>
                    <div class="text-uppercase flex-center atext-gray-600 font-weight-medium">
                        <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width: 16px; height: 16px">
                            <i class="fa fa-user-o font-8 atext-gray-400"></i>
                        </span>
                        {{ __('Staff') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 amb-24">
            <div class="card arounded-6 border-info h-100">
                <div class="card-body text-center apx-8">
                    <div class="font-64 atext-green-500">{{ $userCount[App\Models\User::ROLE_USER] ?? 0 }}</div>
                    <div class="text-uppercase flex-center atext-gray-600 font-weight-medium">
                        <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width: 16px; height: 16px">
                            <i class="fa fa-user font-8 atext-gray-400"></i>
                        </span>
                        {{ __('User') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br><br>
@endsection
