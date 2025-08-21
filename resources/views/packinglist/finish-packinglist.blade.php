@extends('layouts.app',[
'class' => '',
'folderActive' => '',
'elementActive' => 'packing'
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
        width: 80%!important;
    }
</style>
@endsection

@section('content')
    <?php
    header('Content-Type: image/png');
    ?>
<div class="content">
    <div class="fade-in">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="mb-0">{{ __('Packing List Outbound') }}</h2>
            </div>
            <div class="card-footer">
                @if (count($orderJourneys) == 0)
                    <div class="text-center">{{ __('No data.') }}</div>
                @else
                    <div class="d-flex">
                        <div class="table-responsive w-50">
                            <table class="table"
                                id="staff-package-table">
                                <thead class="text-primary">
                                    <tr>
                                        <th style="width:20vw">{{ __('Order ID') }}</th>
                                        <th>{{ __('Date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orderJourneys as $orderJourney)
                                        <tr>
                                            <td>
                                                {{ $orderJourney->order_id }}
                                            </td>
                                            <td>{{ $orderJourney->created_date }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="w-50" style="margin-top: 30px;">
                            <form action={{ route('staff.packing.finish') }} method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="d-flex flex-row w-80" style="align-items: end;justify-content:center;">
                                    <div class="form-group search-form-group d-flex flex-column mb-0" style="width: 70%; margin-right: 40px;">
                                        <label for="master_bill" class="col-form-label search-label text-bold"><b>{{ __('Master Bill') }} *</b></label>
                                        <div class="search-input position-relative mb-3">
                                            <input id="master_bill" type="text" class="form-control w-100" name="master_bill">
                                        </div>
                                        <label for="carrier_id" class="col-form-label search-label text-bold"><b>{{ __('Carrier') }} *</b></label>
                                        <div class="search-input position-relative">
                                            <select name="carrier_id" id="carrier_id" class="form-control w-100">
                                                @foreach($carriers as $k => $v)
                                                    <option value="{{ $k }}">{{ $v }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <input id="packing_id" type="hidden" name="packing_id" value={{$packing_id}}>
                                    </div>
                                    <button type="submit" style="width:100px;height:40px" class="btn btn-block btn-primary"><b>{{ __('Finish') }}</b></button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
