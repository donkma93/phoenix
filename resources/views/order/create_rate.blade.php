@extends('layouts.app',[
'class' => '',
'folderActive' => '',
'elementActive' => 'orders'
])

@section('styles')
<style>
    .card .card-header {
        padding: 0.75rem 1.25rem;
        margin-bottom: 0;
        border-bottom: 1px solid;
        background-color: #fff;
        border-color: #d8dbe0;
    }
    .search-form-group {
        display: flex;
        align-items: center;
    }
    .search-form-group .search-label {
        min-width: 160px;
    }
    .form-horizontal .col-form-label {
        padding-top: calc(.375rem + 1px);
        padding-bottom: calc(.375rem + 1px);
        padding-left: 0;
        padding-right: 0;
        text-align: left;
        margin-bottom: 0;
        font-size: inherit;
        line-height: 1.5;
    }
    .pointer {
        cursor: pointer!important;
    }
    i.pointer {
        padding: 8px;
    }
    .form-control {
        height: calc(1.5em + 1rem + 5px)!important;
        padding: 0.625rem 0.75rem!important;
    }
</style>
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
<div class="content">
    <div class="fade-in">
        <div class="card px-4 py-2">
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
                                            @if ($rate->provider_image_75)
                                                <img src="{{ $rate->provider_image_75 }}" alt="">
                                            @else
                                                <span>{{ $rate->provider }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <b>{{ $rate->provider }} {{ $rate->service_name ?? '' }}</b>
                                        </td>
                                        <td style="text-align: left">{{ $rate->duration_terms }}</td>
                                        <td> {{ $rate->estimated_days == "1" ? $rate->estimated_days . " day" : $rate->estimated_days . " days" }} </td>
                                        <td>
                                            @if ($rate->attributes)
                                                @php
                                                    $attributes = is_string($rate->attributes) ? json_decode($rate->attributes, true) : $rate->attributes;
                                                @endphp
                                                @if (is_array($attributes))
                                                    @foreach ($attributes as $key => $value)
                                                        @if (is_string($value))
                                                            <div>{{ $value }}</div>
                                                        @else
                                                            <div>{{ $key }}: {{ $value }}</div>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <div>{{ $rate->attributes }}</div>
                                                @endif
                                            @endif
                                        </td>
                                        <td style="text-align: left">
                                            <b>{{ round($rate->amount, 2) }}</b> {{ $rate->currency }}
                                        </td>
                                        <td>
                                            {{-- <input type="radio"
                                                name="rate"
                                                value="{{ $rate }}"> --}}

                                            <button class="btn btn-info btn-round update-rate"" data-rate="{{ $rate->id }}">Choose Rate</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="text-center mt-2">{{ __('Rates Unavailable') }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
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
                error: function(err) {
                    console.log(err)
                    loading(false);
                    alert('Something wrong! Please contact admin for more information!')
                }
            });
        });
    });
</script>
@endpush
