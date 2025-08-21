@extends('layouts.admin')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('admin.dashboard')
        ],
        [
            'text' => 'Order overview',
        ]
    ]
])
@endsection

@section('content')
<div class="row"> 
    <div class="col-md-6 col-lg-12 col-xl-6">
        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">{{ __('Ready to ship') }}</h2>
            </div>

            <div class="card-body row">
                <div class="col-md-4 col-lg-4 col-xl-4">
                    <div>Pending</div>
                    <div>{{ $readyToShipTotal[App\Models\Order::PICKING_PENDING] ?? 0  }}</div>
                </div>
                <div class="col-md-4 col-lg-4 col-xl-4">
                    <div>In tote</div>
                    <div>{{ $readyToShipTotal[App\Models\Order::PICKING_INTOTE] ?? 0  }}</div>
                </div>
                <div class="col-md-4 col-lg-4 col-xl-4">
                    <div>Fulfill</div>
                    <div>{{ $readyToShipTotal[App\Models\Order::PICKING_FULFILLED] ?? 0  }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-12 col-xl-6">
        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">{{ __('Due today') }}</h2>
            </div>

            <div class="card-body row">
                <div class="col-md-4 col-lg-4 col-xl-4">
                    <div>Pending</div>
                    <div>{{ $dueTodayTotal[App\Models\Order::PICKING_PENDING] ?? 0  }}</div>
                </div>
                <div class="col-md-4 col-lg-4 col-xl-4">
                    <div>In tote</div>
                    <div>{{ $dueTodayTotal[App\Models\Order::PICKING_INTOTE] ?? 0  }}</div>
                </div>
                <div class="col-md-4 col-lg-4 col-xl-4">
                    <div>Fulfill</div>
                    <div>{{ $dueTodayTotal[App\Models\Order::PICKING_FULFILLED] ?? 0  }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-lg-12 col-xl-12">
        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">{{ __('Picker overview') }}</h2>
                <a class="btn btn-primary" href="{{ route('admin.staffOrderOverview', [ 'type' => 0 ]) }}">Export</a>
            </div>

            <div class="card-body">
                @if(count($pickerOverview))
                    <div class="table-responsive">
                        <table class="table table-align-middle table-bordered table-striped table-sm" id="admin-picker-overview-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Ready to ship') }}</br>Pending</th>
                                    <th>{{ __('Due today') }}</br>Pending</th>
                                    <th>{{ __('Ready to ship') }}</br>In tote</th>
                                    <th>{{ __('Due today') }}</br>In tote</th>
                                    <th>{{ __('Ready to ship') }}</br>Fulfill</th>
                                    <th>{{ __('Due today') }}</br>Fulfill</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pickerOverview as $picker)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $picker->email }}</td>
                                    <td>{{ $picker->rtsPending ?? 0 }}</td>
                                    <td>{{ $picker->dtPending ?? 0 }}</td>
                                    <td>{{ $picker->rtsInTote ?? 0 }}</td>
                                    <td>{{ $picker->dtInTote ?? 0 }}</td>
                                    <td>{{ $picker->rtsFulfill ?? 0 }}</td>
                                    <td>{{ $picker->dtFulfill ?? 0 }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center">{{ __('No data.') }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-lg-12 col-xl-12">
        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">{{ __('Packer overview') }}</h2>
                <a class="btn btn-primary" href="{{ route('admin.staffOrderOverview', [ 'type' => 1 ]) }}">Export</a>
            </div>

            <div class="card-body">
                @if(count($packerOverview))
                    <div class="table-responsive">
                        <table class="table table-align-middle table-bordered table-striped table-sm" id="admin-picker-overview-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Ready to ship') }}</br>Pending</th>
                                    <th>{{ __('Due today') }}</br>Pending</th>
                                    <th>{{ __('Ready to ship') }}</br>In tote</th>
                                    <th>{{ __('Due today') }}</br>In tote</th>
                                    <th>{{ __('Ready to ship') }}</br>Fulfill</th>
                                    <th>{{ __('Due today') }}</br>Fulfill</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($packerOverview as $packer)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $packer->email }}</td>
                                    <td>{{ $packer->rtsPending ?? 0 }}</td>
                                    <td>{{ $packer->dtPending ?? 0 }}</td>
                                    <td>{{ $packer->rtsInTote ?? 0 }}</td>
                                    <td>{{ $packer->dtInTote ?? 0 }}</td>
                                    <td>{{ $packer->rtsFulfill ?? 0 }}</td>
                                    <td>{{ $packer->dtFulfill ?? 0 }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center">{{ __('No data.') }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection