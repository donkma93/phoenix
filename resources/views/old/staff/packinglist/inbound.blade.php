@extends('layouts.staff')

@section('breadcrumb')
    @include('layouts.partials.breadcrumb', [
        'items' => [
            [
                'text' => 'Dashboard',
                'url' => route('staff.dashboard'),
            ],
            [
                'text' => 'Packing List Inbound',
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
                <h2 class="mb-0">{{ __('Packing List Inbound') }}</h2>
            </div>
            <div class="card-footer">
                @if (count($data['packing_list']) == 0)
                    <div class="text-center">{{ __('No data.') }}</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-align-middle table-bordered table-striped table-sm"
                            id="staff-package-table">
                            <thead>
                                <tr>
                                    <th style="width:20vw">{{ __('Master Bill') }}</th>
                                     <th>{{ __('Packing List') }}</th>
                                     <th>{{ __('Receive') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data['packing_list'] as $packing)
                                    @if(isset($packing->master_bill))
                                        <tr>
                                            <td>
                                                {{ $packing->master_bill }} 
                                            </td>
                                            <td>{{ $packing->packing_list_code }}</td>
                                            <td>{{ $packing->received}}</td>
                                            <td
                                            style="text-align:center"
                                            > {{ $packing->quantity }}
                                            </td>
                                            <td
                                            style="text-align:center"
                                            >{{ App\Models\PackingList::$statusName[$packing->status] }}</td>
                                            <td style="text-align:center">{{ $packing->created_date }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center justify-content-md-end amt-16">
                        {{ $data['packing_list']->appends(request()->all())->links('components.pagination') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection