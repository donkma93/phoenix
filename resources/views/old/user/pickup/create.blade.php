@extends('layouts.user')

@section('breadcrumb')
    @include('layouts.partials.breadcrumb', [
        'items' => [
            [
                'text' => 'Dashboard',
                'url' => route('dashboard'),
            ],
            [
                'text' => 'Pickup Request',
                'url' => route('pickup.index'),
            ],
            [
                'text' => 'Create',
            ],
        ],
    ])
@endsection

@if (session('success'))
    @section('flash')
        @include('layouts.partials.flash', [
            'messages' => [
                [
                    'content' => session('success'),
                ],
            ],
        ])
    @endsection
@endif

@if (session('fail'))
    @section('flash')
        @include('layouts.partials.flash', [
            'messages' => [
                [
                    'content' => session('fail'),
                    'type' => 'error',
                ],
            ],
        ])
    @endsection
@endif

@section('content')
    <div class="fade-in">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h2 class="mb-0">{{ __('Create Pickup Request') }}</h2>
                      <div class="search-input text-center text-sm-left mb-6">
                        <a class="btn btn-primary mb-3" href={{ route('pickup.index') }}>{{ __('Back') }}</a>
                    </div>
            </div>

            <div class="card-body" id="manual">
                <div>
              
                    @if (count($orders) == 0)
                        <div class="text-center">{{ __('No data.') }}</div>
                    @else
                        <form method="post" action="{{ route('pickup.store') }}">
                            @csrf

                            <div class="table-responsive">
                                <table class="table table-align-middle table-bordered table-striped table-sm"
                                    id="staff-package-table">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>{{ __('Order ID') }}</th>
                                            <th>{{ __('Customer') }}</th>
                                            <th>{{ __('Info') }}</th>
                                            <th>{{ __('KG') }}</th>
                                            <th>{{ __('Date') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($orders as $order)
                                            <tr>
                                                <td>
                                                    <input id="{{ $order->id }}" class="form-group" type="checkbox"
                                                        name="order_ids[]" value="{{ $order->id }}">
                                                </td>
                                                <td>
                                                    {{ $order->id }}
                                                </td>
                                                <td style="text-align:center;">{{ $order->partner_code }}</td>
                                                <td>
                                                    <div><b>Name: </b>{{ $order->addressTo->name ?? $order->shipping_name ?? '' }}</div>
                                                    <div><b>Street: </b>{{ $order->addressTo->street1 ?? $order->shipping_street ?? '' }}</div>
                                                    <div><b>Address1: </b>{{ $order->addressTo->street2 ?? $order->shipping_address1 ?? '' }}</div>
                                                    <div><b>Address2: </b>{{ $order->addressTo->street3 ?? $order->shipping_address2 ?? '' }}</div>
                                                    <div><b>Company: </b>{{ $order->addressTo->company ?? $order->shipping_company ?? '' }}</div>
                                                    <div><b>City: </b>{{ $order->addressTo->city ??  $order->shipping_city ?? '' }}</div>
                                                    <div><b>Zip: </b>{{ $order->addressTo->zip ?? $order->shipping_zip ?? '' }}</div>
                                                    <div><b>Province: </b>{{ $order->addressTo->state ?? $order->shipping_province ?? '' }}</div>
                                                    <div><b>Country: </b>{{ $order->addressTo->country ?? $order->shipping_country ?? '' }}</div>
                                                    <div><b>Phone: </b>{{ $order->addressTo->phone ?? $order->shipping_phone ?? '' }}</div>
                                                    <div><b>WxHxD: </b>{{ $order->orderPackage->width.'x'.$order->orderPackage->height .'x'.$order->orderPackage->length}}</div></td>
                                                <td style="text-align:center;font-size:16px;"><b>{{ isset($order->orderPackage) ? $order->orderPackage->weight : '' }}</b></td>
                                                <td>{{ $order->created_at }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="search-form-group">
                                <div class="search-input text-center text-sm-left">
                                    <input class="btn btn-primary" type="submit" value="{{ __('Create') }}">
                                </div>
                            </div>
                        </form>

                        <div class="d-flex justify-content-center justify-content-md-end amt-16">
                            {{ $orders->appends(request()->all())->links('components.pagination') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
