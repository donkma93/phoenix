@extends('layouts.admin')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('admin.dashboard')
        ],
        [
            'text' => 'Partner'
        ]
    ]
])
@endsection

@section('content')
<div class="fade-in">
    <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('Partner list') }}</h2>
            <a class="btn btn-success" href="{{ route('admin.partner.new') }}">
                {{ __('New Partner') }}
            </a>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('admin.partner.list') }}" class="form-horizontal" role="form">
                <div class="form-group search-form-group">
                    <label for="barcode" class="col-form-label search-label"><b>{{ __('Code') }}</b></label>
                    <div class="search-input position-relative">
                        <input type="input" id="barcode" class="form-control w-100" name="barcode" value="@if (isset($oldInput['barcode'])){{$oldInput['barcode']}}@endif" />
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="name" class="col-form-label search-label"><b>{{ __('Name') }}</b></label>
                    <div class="search-input">
                        <input type="text" class="form-control" name="name" value="@if (isset($oldInput['name'])){{$oldInput['name']}}@endif" />
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
            @if (count($partners) == 0)
                <div class="text-center">{{ __('No data.') }}</div>
            @else
                <div class="table-responsive">
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="admin-partner-list-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Phone') }}</th>
                                <th>{{ __('Address') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($partners as $partner)
                            <tr>
                                <td>{{ ($partners->currentPage() - 1) * $partners->perPage() + $loop->iteration }}</td>
                                <td>{{ $partner->partner_code }}</td>
                                <td>{{ $partner->partner_name }}</td>
                                <td>{{ $partner->phone }}</td>
                                <td>{{ $partner->address }}</td>
                                <td>
                                    <a class="btn btn-info" href="{{ route('admin.partner.detail', ['id' => $partner->id]) }}">Detail</a>
                                </td>
                                
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center justify-content-md-end amt-16">
                    {{ $partners->appends(request()->all())->links('components.pagination') }}
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

