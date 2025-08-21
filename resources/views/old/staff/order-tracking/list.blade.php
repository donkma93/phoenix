@extends('layouts.staff')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('staff.dashboard')
        ],
        [
            'text' => 'Order Tracking'
        ]
    ]
])
@endsection

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('staff.order-tracking.list') }}" class="form-horizontal" role="form">
                <div class="form-group search-form-group">
                    <label for="order_code" class="col-form-label search-label"><b>{{ __('Order Code') }}</b></label>
                    <div class="search-input">
                        <input type="text" class="form-control" name="order_code" value="@if (isset($oldInput['order_code'])){{$oldInput['order_code']}}@endif" />
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="order_number" class="col-form-label search-label"><b>{{ __('Order Number') }}</b></label>
                    <div class="search-input">
                        <input type="text" class="form-control" name="order_number" value="@if (isset($oldInput['order_number'])){{$oldInput['order_number']}}@endif" />
                    </div>
                </div>

                <div class="search-form-group">
                    <div class="search-label d-none d-sm-block"></div>
                    <div class="search-input text-center text-sm-left">
                        <input class="btn btn-primary" type="submit" value="{{ __('Search') }}">
                    </div>
                </div>
            </form>
        </div>
        <div class="card-footer">
            @if (count($orders) == 0)
                <div class="text-center">{{ __('No data.') }}</div>
            @else
              <form target="_blank" method="get" action="{{ route('staff.order-tracking.print') }}">
                  <div class="search-form-group d-flex justify-content-center align-items-center"
                  style="flex-direction: column!important"
                  >
                                <div class="search-input text-center text-sm-left">
                                    <input class="btn btn-primary mb-3" type="submit" value="{{ __('Print') }}">
                                </div>
                    </div>
            
                <div class="table-responsive">
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="admin-order-list-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>No</th>
                                <th>{{ __('Order Code') }}</th>
                                <th>{{ __('Order Number') }}</th>
                                <th>{{ __('Tracking Code') }}</th>
                                <th>{{ __('Customer') }}</th>
                                <th>{{ __('Package info') }}</th>
                                <th>{{ __('Rates') }}</th>
                                <th>{{ __('Label') }}</th>
                                <th>{{ __('Time In') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                             <td>
                                                    <input id="{{ $order->id }}" class="form-group" type="checkbox"
                                                        name="label_list[]" value="{{$order->order_code}}">
                                                </td>
                                <td>{{ ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration }}</td>
                                <td>{{ $order->order_code }}</td>
                                <td>{{ $order->order_number }}</td>
                                   <td>INFO</td>
                                   <td>INFO</td>
                                   <td>INFO</td>
                                   <td>INFO</td>
                                   <td>INFO</td>
                                  <td>{{ $order->updated_at }}</td>
                                <td>
                                    {{-- <a class="btn btn-info" href="{{ route('staff.order-tracking.detail', ['id' => $order->id]) }}">Detail</a> --}}
                                </td>
                                
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center justify-content-md-end amt-16">
                    {{ $orders->appends(request()->all())->links('components.pagination') }}
                </div>
            @endif
        </div>
    </div>
</div>
<!-- Modal -->
<div id="scan-modal" class="modal fade bd-example-scan-lg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <video id="video" style="border: 1px solid gray; width: 100%; height: 100%"></video>
            </div>
        </div>
    </div>
</div>
@endsection

