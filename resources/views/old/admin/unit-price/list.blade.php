@extends('layouts.admin')

@section('breadcrumb')
    @include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('admin.dashboard')
        ],
        [
            'text' => 'Unit Price'
        ],
    ]
])
@endsection

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('Unit Price List') }}</h2>
        </div>
        <div class="card-body">
            @if (count($requestType) == 0)
                <div class="text-center">No data.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="admin-request-detail-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>{{ __('Type') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Tax</td>
                                <td>
                                    <a class="btn btn-info" href="{{ route('admin.unit-price.detail', ['id' => App\Models\MTax::TAX_ID ])}}">{{ __('Detail') }}</a>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Storage</td>
                                <td>
                                    <a class="btn btn-info" href="{{ route('admin.unit-price.detail', ['id' => 0])}}">{{ __('Detail') }}</a>
                                </td>
                            </tr>
                            @foreach($requestType as $type)
                                <tr>
                                    <td>{{ $loop->iteration + 2 }}</td>
                                    <td>
                                        @if($type->name != 'add package')
                                            {{ ucfirst($type->name) }}
                                        @else
                                            Inbound
                                        @endif
                                    </td>
                                    <td>
                                        <a class="btn btn-info" href="{{ route('admin.unit-price.detail', ['id' => $type->id])}}">{{ __('Detail') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
