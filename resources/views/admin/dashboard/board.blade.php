@extends('layouts.app', [
    'class' => '',
    'folderActive' => '',
    'elementActive' => 'orderOverview',
])

@section('content')
    <div class="content">
        <div class="row mt-4">
            <div class="col-md-6 col-lg-12 col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">{{ __('Ready to ship') }}</h4>
                    </div>
                    <div class="card-body row">
                        <div class="col-md-4">
                            <div class="text-muted">{{ __('Pending') }}</div>
                            <div class="h3 mb-0">{{ $readyToShipTotal[\App\Models\Order::PICKING_PENDING] ?? 0 }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted">{{ __('In tote') }}</div>
                            <div class="h3 mb-0">{{ $readyToShipTotal[\App\Models\Order::PICKING_INTOTE] ?? 0 }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted">{{ __('Fulfill') }}</div>
                            <div class="h3 mb-0">{{ $readyToShipTotal[\App\Models\Order::PICKING_FULFILLED] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-12 col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">{{ __('Due today') }}</h4>
                    </div>
                    <div class="card-body row">
                        <div class="col-md-4">
                            <div class="text-muted">{{ __('Pending') }}</div>
                            <div class="h3 mb-0">{{ $dueTodayTotal[\App\Models\Order::PICKING_PENDING] ?? 0 }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted">{{ __('In tote') }}</div>
                            <div class="h3 mb-0">{{ $dueTodayTotal[\App\Models\Order::PICKING_INTOTE] ?? 0 }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted">{{ __('Fulfill') }}</div>
                            <div class="h3 mb-0">{{ $dueTodayTotal[\App\Models\Order::PICKING_FULFILLED] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">{{ __('Picker overview') }}</h4>
                        <a class="btn btn-primary btn-sm btn-round" href="{{ route('admin.staffOrderOverview', ['type' => 0]) }}">Export</a>
                    </div>
                    <div class="card-body">
                        @if(count($pickerOverview))
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>{{ __('Email') }}</th>
                                            <th>{{ __('Ready to ship') }}<br>Pending</th>
                                            <th>{{ __('Due today') }}<br>Pending</th>
                                            <th>{{ __('Ready to ship') }}<br>In tote</th>
                                            <th>{{ __('Due today') }}<br>In tote</th>
                                            <th>{{ __('Ready to ship') }}<br>Fulfill</th>
                                            <th>{{ __('Due today') }}<br>Fulfill</th>
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

        <div class="row mb-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">{{ __('Packer overview') }}</h4>
                        <a class="btn btn-primary btn-sm btn-round" href="{{ route('admin.staffOrderOverview', ['type' => 1]) }}">Export</a>
                    </div>
                    <div class="card-body">
                        @if(count($packerOverview))
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>{{ __('Email') }}</th>
                                            <th>{{ __('Ready to ship') }}<br>Pending</th>
                                            <th>{{ __('Due today') }}<br>Pending</th>
                                            <th>{{ __('Ready to ship') }}<br>In tote</th>
                                            <th>{{ __('Due today') }}<br>In tote</th>
                                            <th>{{ __('Ready to ship') }}<br>Fulfill</th>
                                            <th>{{ __('Due today') }}<br>Fulfill</th>
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
    </div>
@endsection
