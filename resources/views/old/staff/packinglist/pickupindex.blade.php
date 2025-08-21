@extends('layouts.staff')

@section('breadcrumb')
    @include('layouts.partials.breadcrumb', [
        'items' => [
            [
                'text' => 'Dashboard',
                'url' => route('staff.dashboard'),
            ],
            [
                'text' => 'Pickup Request',
            ],
        ],
    ])
@endsection

@section('content')
    <?php
    header('Content-Type: image/png');
    ?>
    <div class="fade-in">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="mb-0">{{ __('Pickup Request') }}</h2>
            </div>
            <div class="card-footer">
                @if (count($data['pickups']) == 0)
                    <div class="text-center">{{ __('No data.') }}</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-align-middle table-bordered table-striped table-sm"
                            id="staff-package-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Request Code') }}</th>
                                     <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Count') }}</th>
                                    <th>{{ __('KG') }}</th>
                                    <th>{{ __('Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data['pickups'] as $pickup)
                                    <tr>
                                        <td
                                         style="text-align:left"
                                        >
                                           <a href="{{ route('staff.pickup.show', ['pickup_id' => $pickup->id]) }}"> {{ $pickup->pickup_code }} </a>
                                        </td>
                                        <td>{{ $pickup->created_username }}</td>
                                        <td
                                           style="text-align:center"
                                        >{{ App\Models\PickupRequest::$statusName[$pickup->status] }}</td>
                                        <td
                                         style="text-align:center"
                                        > {{ count($pickup->orderJourneys) }}
                                        </td>
                                        <td
                                        style="text-align:center;font-size:16px;"
                                        ><b>{{ $pickup->totalKG }}<b></td>
                                        <td>{{ $pickup->created_date }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center justify-content-md-end amt-16">
                        {{ $data['pickups']->appends(request()->all())->links('components.pagination') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection