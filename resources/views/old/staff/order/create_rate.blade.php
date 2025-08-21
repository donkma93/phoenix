@extends('layouts.user')

@section('breadcrumb')
    @include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('dashboard')
        ],
        [
            'text' => 'Order',
            'url' => route('staff.orders.list')
        ],
        [
            'text' => $orderId
        ],
        [
            'text' => 'Create Rate'
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

@php
    $errorMsg = session('errorMsg') ?? null;
@endphp

@section('content')
    <div class="fade-in">
        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">{{ __('Create Order Rate') }}</h2>
            </div>

            <div id="error_message"></div>

            @if (count($rates))
                <div class="card-body" id="rates">
                    <div class="table-responsive">
                        <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-order-table">
                            <thead style="text-align: center">
                                <tr>
                                    <th>{{ __('No') }}</th>
                                    <th>{{ __('Provider Image') }}</th>
                                    <th>{{ __('Provider Name') }}</th>
                                    <th>{{ __('Duration Terms') }}</th>
                                    <th>{{ __('Estimated') }} </th>
                                    <th>{{ __('Attributes') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody style="text-align: center">
                                @foreach ($rates as $index => $rate)
                                    <tr style="text-align: center">
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <img src="{{ $rate->provider_image_75 }}" alt="">
                                        </td>
                                        <td>
                                            <b>{{ $rate->provider }} {{ $rate->service_name ?? '' }}</b>
                                        </td>
                                        <td style="text-align: left">{{ $rate->duration_terms }}</td>
                                        <td> {{ $rate->estimated_days == "1" ? $rate->estimated_days . " day" : $rate->estimated_days . " days" }} </td>
                                        <td>
                                            @if ($rate->attributes)
                                                @foreach ($rate->attributes as $attribute)
                                                    <div> {{ $attribute }} </div>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td style="text-align: left">
                                            <b>{{ round($rate->amount, 2) }}</b> {{ $rate->currency }}
                                        </td>
                                        <td>
                                            {{-- <input type="radio"
                                                name="rate"
                                                value="{{ $rate }}"> --}}

                                            <button class="btn btn-primary update-rate"" data-rate="{{ $rate->id }}">Choose Rate</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="text-center">{{ __('Rates Unavailable') }}</div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $('.update-rate').on('click', function(e) {
            e.preventDefault();
            const rateId = $(this).data('rate');

            console.log(rateId);
            loading(true);

            $.ajax({
                type: 'POST',
                url: `{{ route('staff.orders.rates.store', ['orderId' => $orderId]) }}`,
                data: {
                    rate: rateId,
                    _token: '{{csrf_token()}}'
                },
                success:function(data) {
                    if (data.length) {
                        loading(false);
                        alert('Invalid Rate. Please choose another rate');

                        errorsHtml = '<div class="alert alert-danger"><ul>';
                        $.each(data, function (k,v) {
                                errorsHtml += '<li>'+ v + '</li>';
                        });
                        errorsHtml += '</ul></di>';

                        $( '#error_message' ).html( errorsHtml );
                    } else {
                        let url = "{{ route('staff.orders.list') }}";
                        window.location.href = url;

                        alert('Create success');
                    }
                },
                error: function() {
                    loading(false);
                    alert('Something wrong! Please contact admin for more information!')
                }
            });
        });
    });
</script>
@endsection
