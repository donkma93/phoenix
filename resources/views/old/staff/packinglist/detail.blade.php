@extends('layouts.staff')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('staff.dashboard')
        ],
        [
            'text' => 'Pickup Request',
            'url' => route('staff.pickup.index')
        ],
        [
            'text' => $pickup_code
        ]
    ]
])
@endsection

@if(session('success'))
@section('flash')
@include('layouts.partials.flash', [
    'messages' => [
        [
            'content' => session('success')
        ]
    ]
])
@endsection
@endif

@if(session('fail'))
@section('flash')
@include('layouts.partials.flash', [
    'messages' => [
        [
            'content' => session('fail'),
            'type' => 'error'
        ]
    ]
])
@endsection
@endif

@section('content')
<div class="fade-in">
  <form target="_blank" method="get" action="{{ route('staff.pickup.orderPrintMultiple') }}">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('Request '.$pickup_code) }}</h2>
             <div class="search-form-group d-flex justify-content-center align-items-center"
                  style="flex-direction: column!important"
                  >
                                <div class="search-input text-center text-sm-left">
                                    <input class="btn btn-primary mb-3" type="submit" value="{{ __('Print') }}">
                                </div>
            </div>
        </div>

        <div class="card-body">
            @if (count($orderJourneys) == 0)
                <div class="text-center">{{ __('No data.') }}</div>
            @else
                <div class="table-responsive">
                    <table width="300px" class="table table-align-middle table-bordered table-striped table-sm"
                        id="staff-package-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>{{ __('Order') }}</th>
                                <th>{{ __('Pickup Date') }}</th>
                                <th>{{ __('User Picked') }}</th>
                                <th>{{ __('To Warehouse')}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orderJourneys as $item)
                                <tr>
                                   <td>
                                        <input id="{{ $item->order_code }}" class="form-group" type="checkbox"
                                                name="label_list[]" value="{{$item->order_id ?? ""}}">
                                    </td>
                                    <td 
                                    d style="text-align:left"
                                    style="width:25%;">{{ $item->order_code }}</td>
                                    <td>{{ isset($item->pickup_date) ? $item->pickup_date : '--' }}</td>
                                    <td style="text-align:center">{{ $item->created_username }}</td>
                                    <td style="text-align:center">{{ $item->to_warehouse }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
    </div>
</form>
</div>
@endsection
