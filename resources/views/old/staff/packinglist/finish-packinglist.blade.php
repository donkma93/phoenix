@extends('layouts.staff')

@section('breadcrumb')
    @include('layouts.partials.breadcrumb', [
        'items' => [
            [
                'text' => 'Dashboard',
                'url' => route('staff.dashboard'),
            ],
            [
                'text' => 'Packing List Outbound',
                'url' => route('staff.packing.outbound'),
            ],
            [
                'text' => 'Finish Packing List',
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
                <h2 class="mb-0">{{ __('Packing List Outbound') }}</h2>
            </div>
            <div class="card-footer">
                @if (count($orderJourneys) == 0)
                    <div class="text-center">{{ __('No data.') }}</div>
                @else
                    <div class="d-flex">
                        <div class="table-responsive w-50">
                            <table class="table table-align-middle table-bordered table-striped table-sm"
                                id="staff-package-table">
                                <thead>
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
                                            <td style="text-align:center">{{ $orderJourney->created_date }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="w-50">
                            <form action={{ route('staff.packing.finish') }} method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="d-flex flex-row w-100" style="align-items: center;justify-content:center;">
                                    <div class="form-group search-form-group d-flex flex-column" style="width: 70%;">
                                        <label class="col-form-label search-label text-bold"><b>{{ __('Master Bill') }}</b></label>
                                        <div class="search-input position-relative">
                                            <input id="master_bill" type="text" class="form-control w-100" name="master_bill">
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
@endsection