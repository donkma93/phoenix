@extends('layouts.admin')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('admin.dashboard')
        ],
        [
            'text' => 'Live shipping',
        ]
    ]
])
@endsection

@section('content')
<div class="row">
    <div class="col-md-12 col-lg-12 col-xl-12">
        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">{{ __('Picker overview') }}</h2>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="admin-picker-overview-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>{{ __('Email') }}</th>
                                @for($i = 0; $i < 24; $i++)
                                    <th>{{ $i}}</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pickerEmails as $picker)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $picker->picker->email }}</td>
                                    @php
                                        $data = $pickerData->filter(function($item) use ($picker) { 
                                            return $item->email == $picker->picker->email;
                                        })
                                    @endphp
                                    @for($i = 0; $i < 24; $i++)
                                        @php
                                            $info = $data->filter(function($item) use ($i) { 
                                                return $item->hour == $i;
                                            })->first()
                                        @endphp
                                        <th>{{ $info ? $info->count : 0 }}</th>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection