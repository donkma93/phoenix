@extends('layouts.app',[
'class' => '',
'folderActive' => '',
'elementActive' => 'pickup'
])

@section('styles')
<style>
    .table-responsive {
        overflow: unset;
    }

    .min-w-160 {
        min-width: 160px;
    }

    .w-80 {
        width: 80% !important;
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

@section('content')
<div class="content">
    <div class="fade-in">
        <form target="_blank" method="get" action="{{ route('staff.pickup.orderPrintMultiple') }}">
            <div class="card p-2">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">{{ __('Request '.$pickup_code) }}</h2>
                    <div class="search-form-group d-flex justify-content-center align-items-center"
                         style="flex-direction: column!important"
                    >
                        <div class="search-input text-center text-sm-left">
                            <input class="btn btn-primary mb-3 min-w-160" type="submit" value="{{ __('Print') }}">
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if (count($orderJourneys) == 0)
                    <div class="text-center">{{ __('No data.') }}</div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-align-middle table-bordered table-striped table-sm"
                               id="staff-package-table">
                            <thead class="text-primary">
                            <tr>
                                <th style="text-align: center;">
                                    <label for="select_all_order"
                                           class="d-flex mb-0 justify-content-center align-items-center"
                                           style="padding: 4px 0px;">
                                        <input id="select_all_order" class="" type="checkbox"
                                               name="" value="">
                                    </label>
                                </th>
                                <th>{{ __('Order') }}</th>
                                <th>{{ __('Pickup Date') }}</th>
                                <th>{{ __('User Picked') }}</th>
                                <th>{{ __('To Warehouse')}}
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($orderJourneys as $item)
                            <tr>
                                <td style="text-align: center; padding: 0;">
                                    <label for="{{ $item->order_code }}" class="d-block mb-0 py-2">
                                        <input id="{{ $item->order_code }}" class="select_item_order" type="checkbox"
                                               name="label_list[]" value="{{$item->order_id ?? ''}}">
                                    </label>
                                </td>
                                <td>{{ $item->order_code }}</td>
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
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#select_all_order').on('change', function () {
            if ($(this).prop('checked') === true) {
                $('.select_item_order').each(function () {
                    $(this).prop('checked', true);
                })
            } else {
                $('.select_item_order').each(function () {
                    $(this).prop('checked', false);
                })
            }

            $('.select_item_order').click(function () {
                let is_check_all = true;
                $('.select_item_order').each(function () {
                    if ($(this).prop('checked') === false) {
                        is_check_all = false;
                    }
                })

                if (is_check_all) {
                    $('#select_all_order').prop('checked', true);
                } else {
                    $('#select_all_order').prop('checked', false);
                }
            })
        })
    })
</script>
@endpush
