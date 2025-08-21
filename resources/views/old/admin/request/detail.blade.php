@extends('layouts.admin')

@section('breadcrumb')
    @include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('admin.dashboard')
        ],
        [
            'text' => 'Request list',
            'url' => route('admin.request.list',  ['status' => $userRequest->status])
        ],
        [
            'text' => $userRequest->id
        ]
    ]
])
@endsection

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('Request detail') }}</h2>
        </div>
        <div class="card-body row">
            <div class="col-md-6">
                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('User') }}</b></label>
                    <div class="search-input col-form-label">
                        {{ $userRequest['user']['email'] }}
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Option') }}</b></label>
                    <div class="search-input col-form-label">
                        {{ App\Models\UserRequest::$optionName[$userRequest['option']] ?? '' }}
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Note') }}</b></label>
                    <div class="search-input col-form-label">
                        {{ $userRequest['note'] }}
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('File') }}</b></label>
                    @if(isset($userRequest['file']))
                        <div class="search-input">
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewPDF(`{{ asset($userRequest['file']) }}`)" >
                                Preview file
                            </button>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Type') }}</b></label>
                    <div class="search-input col-form-label">
                        {{ ucfirst($userRequest['mRequestType']['name']) }}
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Status') }}</b></label>
                    <div class="search-input col-form-label">
                        @foreach (App\Models\UserRequest::$statusName as $key => $status)
                                @if (isset($userRequest['status']) && $userRequest['status'] == $key)
                                    {{  $status }}
                                @endif
                        @endforeach
                    </div>
                </div>

                @if($userRequest['mRequestType']['name'] == 'add package')
                    @if(isset($userRequest['packing_type']))
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Packing type') }}</b></label>
                            <div class="search-input col-form-label">
                                i{{ App\Models\UserRequest::$packingTypes[$userRequest['packing_type']] }}
                            </div>
                        </div>
                    @endif

                    @if(isset($userRequest['prep_type']))
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Prep') }}</b></label>
                            <div class="search-input col-form-label">
                                {{ App\Models\UserRequest::$prepTypes[$userRequest['prep_type']] }}
                            </div>
                        </div>
                    @endif

                    @if(isset($userRequest['label_by_type']))
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Label by') }}</b></label>
                            <div class="search-input col-form-label">
                                {{ App\Models\UserRequest::$labelByTypes[$userRequest['label_by_type']] }}
                            </div>
                        </div>
                    @endif

                    @if(isset($userRequest['store_type']))
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Store type') }}</b></label>
                            <div class="search-input col-form-label">
                                {{ App\Models\UserRequest::$storeTypes[$userRequest['store_type']] }}
                            </div>
                        </div>
                    @endif
                @endif

                @if($userRequest['mRequestType']['name'] == 'warehouse labor')
                    <form action="{{ route('staff.request.updateTime') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Total time') }}</b></label>
                            <div class="search-input col-form-label">
                                {{ $requestHour->hour ?? 0 }} hour
                            </div>
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#timing-modal">
                                Check
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        <div class="card-footer">
            <button type="button" class="btn btn-info amb-16" data-toggle="modal" data-target="#saved-package-modal">Saved package</button>
            
            @if (count($packages) == 0)
                <div class="text-center">No data.</div>
            @else
                @if($userRequest['mRequestType']['name'] == "relabel")
                    <div class="table-responsive">
                        <table class="table table-align-middle table-bordered table-striped table-sm" id="admin-request-detail-table-relabel">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>{{ __('Group Name') }}</th>
                                    <th>{{ __('Package number') }}</th>
                                    <th>{{ __('Unit number') }}</th>
                                    <th>{{ __('Saved package') }}</th>
                                    <th>{{ __('Tracking') }}</th>
                                    <th>{{ __('Images') }}</th>
                                    <th>{{ __('File') }}</th>
                                    <th>{{ __('Unit Code') }}</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($packages as $package)
                                <tr>
                                    <td>{{ ($packages->currentPage() - 1) * $packages->perPage() + $loop->iteration }}</td>
                                    <td>{{ $package->name }}</td>
                                    <td>{{ $package->package_number }}</td>
                                    <td>{{ $package->unit_number }}</td>
                                    <td>{{ $package->received_package_number }}</td>
                                    <td>
                                        @php
                                            if(isset($trackings[$package['id']])) {
                                                $text = str_replace('||', '<br />',$trackings[$package['id']]);
                                            echo html_entity_decode($text, ENT_QUOTES, "UTF-8");
                                            }
                                        @endphp
                                    </td>
                                    <td>
                                        @if(isset($packageGroupImages[$package->request_package_group_id]) && count($packageGroupImages[$package->request_package_group_id]) >0 )
                                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#image-modal" onclick="showImage({{ $loop->iteration }})">
                                                Preview
                                            </button>
                                            @php
                                                $index = $loop->iteration
                                            @endphp
                                            @foreach($packageGroupImages[$package->request_package_group_id] as $image)
                                                <img class="d-none image-preview-{{ $index }}" src="{{ asset($image) }}" />
                                            @endforeach
                                        @else
                                            No images
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($package->file))
                                            <div class="search-input">
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-preview-code" onclick="previewPDF(`{{ asset($package->file) }}`)" >
                                                    Preview file
                                                </button>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $package->barcode }}</td>
                                    <td>
                                        @if(isset($package->barcode))
                                            <div class="d-none" id="package-barcode-{{ $loop->iteration }}">
                                                {!! DNS2D::getBarcodeSVG($package->barcode, 'QRCODE') !!}
                                            </div>
                                            <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewBarcode('package-barcode-{{ $loop->iteration }}')">
                                                {{ __('Preview Code') }}
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modal-history-{{ $package->id }}" >
                                            History
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @elseif($userRequest['mRequestType']['name'] == 'return')
                    <div class="table-responsive">
                        <table class="table table-align-middle table-bordered table-striped table-sm" id="admin-request-detail-table-return">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>{{ __('Group Name') }}</th>
                                    <th>{{ __('Unit number') }}</th>
                                    <th>{{ __('Saved unit') }}</th>
                                    <th>{{ __('Tracking') }}</th>
                                    <th>{{ __('File') }}</th>
                                    <th>{{ __('Unit Code') }}</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($packages as $package)
                                <tr>
                                    <td>{{ ($packages->currentPage() - 1) * $packages->perPage() + $loop->iteration }}</td>
                                    <td>{{ $package->name }}</td>
                                    <td>{{ $package->unit_number }}</td>
                                    <td>{{ $package->received_unit_number }}</td>
                                    <td>
                                        @php
                                            if(isset($trackings[$package['id']])) {
                                                $text = str_replace('||', '<br />',$trackings[$package['id']]);
                                            echo html_entity_decode($text, ENT_QUOTES, "UTF-8");
                                            }
                                        @endphp
                                    </td>
                                    <td>
                                        @if(isset($package->file))
                                            <div class="search-input">
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-preview-code" onclick="previewPDF(`{{ asset($package->file) }}`)" >
                                                    Preview file
                                                </button>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $package->barcode }}</td>
                                    <td>
                                        @if(isset($package->barcode))
                                            <div class="d-none" id="package-barcode-{{ $loop->iteration }}">
                                                {!! DNS2D::getBarcodeSVG($package->barcode, 'QRCODE') !!}
                                            </div>
                                                <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewBarcode('package-barcode-{{ $loop->iteration }}')">
                                                {{ __('Preview Code') }}
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modal-history-{{ $package->id }}" >
                                            History
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @elseif($userRequest['mRequestType']['name'] == 'removal')
                    <div class="table-responsive">
                        <table class="table table-align-middle table-bordered table-striped table-sm" id="admin-request-detail-table-removal">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>{{ __('Group Name') }}</th>
                                    <th>{{ __('Unit number') }}</th>
                                    <th>{{ __('Saved unit') }}</th>
                                    <th>{{ __('Images') }}</th>
                                    <th>{{ __('File') }}</th>
                                    <th>{{ __('Unit Code') }}</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($packages as $package)
                                <tr>
                                    <td>{{ ($packages->currentPage() - 1) * $packages->perPage() + $loop->iteration }}</td>
                                    <td>{{ $package->name }}</td>
                                    <td>{{ $package->unit_number }}</td>
                                    <td>{{ $package->received_unit_number }}</td>
                                    <td>
                                        @if(isset($packageGroupImages[$package->request_package_group_id]) && count($packageGroupImages[$package->request_package_group_id]) >0 )
                                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#image-modal" onclick="showImage({{ $loop->iteration }})">
                                                Preview
                                            </button>
                                            @php
                                                $index = $loop->iteration
                                            @endphp
                                            @foreach($packageGroupImages[$package->request_package_group_id] as $image)
                                                <img class="d-none image-preview-{{ $index }}" src="{{ asset($image) }}" />
                                            @endforeach
                                        @else
                                            No images
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($package->file))
                                            <div class="search-input">
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-preview-code" onclick="previewPDF(`{{ asset($package->file) }}`)" >
                                                    Preview file
                                                </button>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $package->barcode }}</td>
                                    <td>
                                        @if(isset($package->barcode))
                                            <div class="d-none" id="package-barcode-{{ $loop->iteration }}">
                                                {!! DNS2D::getBarcodeSVG($package->barcode, 'QRCODE') !!}
                                            </div>
                                                <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewBarcode('package-barcode-{{ $loop->iteration }}')">
                                                {{ __('Preview Code') }}
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modal-history-{{ $package->id }}" >
                                            History
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @elseif ($userRequest['mRequestType']['name'] == 'add package')
                    @if(isset($userRequest['is_allow']))
                        <h2>Product in Package</h2>
                        <div class="table-responsive">
                            <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-request-detail-table">
                            <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Barcode') }}</th>
                                        <th>{{ __('Image') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($packageGroups as $group)
                                    <tr>
                                        <td>{{ ($packageGroups->currentPage() - 1) * $packageGroups->perPage() + $loop->iteration }}</td>
                                        <td>{{ $group->packageGroup->name }}</td>
                                        <td>{{ $group->packageGroup->barcode }}</td>
                                        <td> @if(isset($packageGroupImages[$group->id]) && count($packageGroupImages[$group->id]) >0 )
                                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#image-modal" onclick="showImage({{ $loop->iteration }})">
                                                    Preview
                                                </button>
                                                @php
                                                    $index = $loop->iteration
                                                @endphp
                                                @foreach($packageGroupImages[$group->id] as $image)
                                                    <img class="d-none image-preview-{{ $index }}" src="{{ asset($image) }}" />
                                                @endforeach
                                            @else
                                                No images
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <h2>Package list</h2>
                        <div class="table-responsive">
                            <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-request-detail-table">
                            <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>{{ __('Code') }}</th>
                                        <th>{{ __('Unit number') }}</th>
                                        <th>{{ __('Weight') }}</th>
                                        <th>{{ __('Width') }}</th>
                                        <th>{{ __('Height') }}</th>
                                        <th>{{ __('Length') }}</th>
                                        <th>{{ __('Warehouse Area') }}</th>
                                        <th>{{ __('Is stored') }}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($packages as $package)
                                    <input type="hidden" value="{{ $package->id }}"  id="id-{{ $loop->iteration }}"/>
                                    <input type="hidden" value="{{ $package->received_unit_number }}"  id="unit-{{ $loop->iteration }}"/>
                                    <tr>
                                        <td>{{ ($packages->currentPage() - 1) * $packages->perPage() + $loop->iteration }}</td>
                                        <td>{{ $package->barcode }}</td>
                                        <td>{{ $package->unit_number }}</td>
                                        <td>{{ $package->weight ?? 0 }}</td>
                                        <td>{{ $package->width ?? 0 }}</td>
                                        <td>{{ $package->height ?? 0 }}</td>
                                        <td>{{ $package->length ?? 0 }}</td>
                                        <td>{{ $package->warehouseArea->name ?? "" }}</td>
                                        <td>
                                            @if($package->status == App\Models\Package::STATUS_STORED) Yes @else No @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modal-history-{{ $package->id }}" >
                                                History
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-align-middle table-bordered table-striped table-sm" id="admin-request-detail-table">
                            <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>{{ __('Group Name') }}</th>
                                        <th>{{ __('Package number') }}</th>
                                        <th>{{ __('Unit number') }}</th>
                                        <th>{{ __('Received package') }}</th>
                                        <th>{{ __('Total Unit') }}</th>
                                        <th>{{ __('Images') }}</th>
                                        <th>{{ __('Tracking') }}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($packages as $package)
                                    <tr>
                                        <td>{{ ($packages->currentPage() - 1) * $packages->perPage() + $loop->iteration }}</td>
                                        <td>{{ $package->name }}</td>
                                        <td>{{ $package->package_number }}</td>
                                        <td>{{ $package->unit_number }}</td>
                                        <td>
                                            {{ $package->received_package_number }}
                                        </td>
                                        <td>
                                            {{ $package->received_unit_number }}
                                        </td>
                                        <td>
                                            @if(isset($packageGroupImages[$package->request_package_group_id]) && count($packageGroupImages[$package->request_package_group_id]) >0 )
                                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#image-modal" onclick="showImage({{ $loop->iteration }})">
                                                    Preview
                                                </button>
                                                @php
                                                    $index = $loop->iteration
                                                @endphp
                                                @foreach($packageGroupImages[$package->request_package_group_id] as $image)
                                                    <img class="d-none image-preview-{{ $index }}" src="{{ asset($image) }}" />
                                                @endforeach
                                            @else
                                                No images
                                            @endif
                                        </td>
                                        <td>@php
                                                if(isset($trackings[$package['id']])) {
                                                    $text = str_replace('||', '<br />',$trackings[$package['id']]);
                                                echo html_entity_decode($text, ENT_QUOTES, "UTF-8");
                                                }
                                            @endphp
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modal-history-{{ $package->id }}" >
                                                History
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                @elseif ($userRequest['mRequestType']['name'] == 'outbound')
                    <div class="table-responsive">
                        <table class="table table-align-middle table-bordered table-striped table-sm" id="admin-request-detail-table-outbound">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>{{ __('Group Name') }}</th>
                                    <th>{{ __('Package number') }}</th>
                                    <th>{{ __('Unit number') }}</th>
                                    <th>{{ __('Saved package') }}</th>
                                    <th>{{ __('File') }}</th>
                                    <th>{{ __('Outbound code') }}</th>
                                    @if(isset($userRequest['is_allow']))
                                        <th>{{ __('Use Insurance') }}</th>
                                        <th>{{ __('Insurance Fee') }}</th>
                                        <th>{{ __('Ship mode') }}</th>
                                    @endif
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($packages as $package)
                                <tr>
                                    <td>{{ ($packages->currentPage() - 1) * $packages->perPage() + $loop->iteration }}</td>
                                    <td>{{ $package->name }}</td>
                                    <td>{{ $package->package_number }}</td>
                                    <td>{{ $package->unit_number }}</td>
                                    <td>{{ $package->received_package_number }}</td>
                                    <td>
                                        @if(isset($package->file))
                                            <div class="search-input">
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-preview-code" onclick="previewPDF(`{{ asset($package->file) }}`)" >
                                                    Preview file
                                                </button>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($package->barcode))
                                            <div class="d-none" id="package-barcode-{{ $loop->iteration }}">
                                                {!! DNS2D::getBarcodeSVG($package->barcode, 'QRCODE') !!}
                                            </div>
                                                <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewBarcode('package-barcode-{{ $loop->iteration }}')">
                                                {{ __('Preview Code') }}
                                            </button>
                                        @endif
                                    </td>
                                    @if(isset($userRequest['is_allow']))
                                        <td>
                                            @if($package->is_insurance == 1) 
                                                {{ __('Yes') }}
                                            @else
                                                {{ __('No') }}
                                            @endif
                                        </td>
                                        <td>{{ $package->insurance_fee ? number_format($package->insurance_fee, 2) : 0 }}</td>
                                        <td>{{ \App\Models\UserRequest::$shipModes[$package->ship_mode] ?? "" }}</td>
                                    @endif
                                    <td>
                                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modal-history-{{ $package->id }}" >
                                            History
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-align-middle table-bordered table-striped table-sm" id="admin-request-detail-table-normal">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>{{ __('Group Name') }}</th>
                                    <th>{{ __('Package number') }}</th>
                                    <th>{{ __('Unit number') }}</th>
                                    <th>{{ __('Saved package') }}</th>
                                    <th>{{ __('File') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($packages as $package)
                                <tr>
                                    <td>{{ ($packages->currentPage() - 1) * $packages->perPage() + $loop->iteration }}</td>
                                    <td>{{ $package->name }}</td>
                                    <td>{{ $package->package_number }}</td>
                                    <td>{{ $package->unit_number }}</td>
                                    <td>{{ $package->received_package_number }}</td>
                                    <td>
                                        @if(isset($package->file))
                                            <div class="search-input">
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-preview-code" onclick="previewPDF(`{{ asset($package->file) }}`)" >
                                                    Preview file
                                                </button>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modal-history-{{ $package->id }}" >
                                            History
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endif
            <div class="d-flex justify-content-center justify-content-md-end amt-16">
                {{ $packages->appends(request()->all())->links('components.pagination') }}
            </div>
        </div>
    </div>
</div>

@foreach($packages as $package)
<!-- Modal -->
<div id="modal-history-{{ $package->id }}" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Request history</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if (count($requestHistory[$package->id]) == 0)
                    <div class="text-center">No data.</div>
                @else
                    <div class="table-responsive" id="modal-table">
                        @if(!in_array($userRequest['mRequestType']['name'], ["add package", "removal", "return"]))
                            <table class="table table-align-middle table-bordered table-striped table-sm" id="admin-request-history">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>{{ __('Package Number') }}</th>
                                        <th>{{ __('Staff') }}</th>
                                        <th>{{ __('Date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requestHistory[$package->id] as $history)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $history->package_number }}</td>
                                            <td>{{ $history->staff->email }}</td>
                                            <td>{{ $history->created_at }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            @if($userRequest['mRequestType']['name'] === 'add package')
                                <table class="table table-align-middle table-bordered table-striped table-sm" id="admin-request-history-add-package">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>{{ __('Unit Number') }}</th>
                                            <th>{{ __('Staff') }}</th>
                                            <th>{{ __('Date') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($requestHistory[$package->id] as $history)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $history->unit_number }}</td>
                                                <td>{{ $history->staff->email }}</td>
                                                <td>{{ $history->created_at }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <table class="table table-align-middle table-bordered table-striped table-sm" id="admin-request-history">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>{{ __('Unit Number') }}</th>
                                            <th>{{ __('Staff') }}</th>
                                            <th>{{ __('Date') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($requestHistory[$package->id] as $history)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $history->unit_number }}</td>
                                                <td>{{ $history->staff->email }}</td>
                                                <td>{{ $history->created_at }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Modal -->
<div id="modal-preview-code" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="preview-barcode">
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div id="saved-package-modal" class="modal modal-fullsize bd-example-scan-lg" tabindex="-2" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Saved package</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if (count($packagesSaved) == 0)
                    <div class="text-center">No data.</div>
                @else
                    <div class="table-responsive" id="modal-table">
                        <table class="table table-align-middle table-bordered table-striped table-sm" id="admin-request-received-package-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>{{ __('Group') }}</th>
                                    <th>{{ __('Package Code') }}</th>
                                    <th>{{ __('Unit') }}</th>
                                    <th>{{ __('Received') }}</th>
                                    <th>{{ __('Warehouse') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($packagesSaved as $i => $package)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        @php
                                            $group_name = "";
                                            if($package['detail_groups_name'] != '') {
                                                $group_name = $package['detail_groups_name'];
                                            } else if($package['group_name'] != ''){
                                                $group_name = $package['group_name'];
                                            } else {
                                                $group_name = $package['packageGroup']['name'];
                                            }
                                        @endphp
                                        <td>{{ $group_name }}</td>
                                        <td>{{ $package['barcode'] }}</td>
                                        <td>{{ $package['unit_number'] }}</td>
                                        <td>{{ $package['received_unit_number'] }}</td>
                                        <td>{{ $package['warehouseArea']['name'] ?? '' }}</td>
                                        <td>{{ App\Models\Package::$statusName[$package['status']] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="timing-modal" class="modal fade bd-example-modal-lg" tabindex="-2" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Working time</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if (count($workingTimes) == 0)
                    <div class="text-center">No data.</div>
                @else
                    <div class="table-responsive" id="modal-table">
                        <table class="table table-align-middle table-bordered table-striped table-sm" id="admin-request-working-time-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>{{ __('Start time') }}</th>
                                    <th>{{ __('End time') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($workingTimes as $i => $time)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $time['start_at'] }}</td>
                                        <td>{{ $time['finish_at'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="image-modal" class="modal fade bd-example-scan-lg modal-fullsize" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Package images</h5>
                <button class="btn btn-success aml-8" data-toggle="modal" data-target="#modal-preview-code" onclick="printSelected()">Print selected</button>
                <button class="btn btn-primary aml-8" data-toggle="modal" data-target="#modal-preview-code" onclick="printAll()">Print all</button>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row amb-30">
                    <div class="col-md-12 text-center main-thumbs">
                        <img id="main" class="main-thumbs-img" />
                    </div>
                </div>

                <div class="row" id="thumbs">
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <script>
        function previewPDF(file) {
            const { jsPDF } = window.jspdf;
            const splitFile = file.split('.');
            const fileType = splitFile[splitFile.length - 1];
            const validImageTypes = ['gif', 'jpeg', 'png', 'tiff', 'jpg', 'heif'];

            let imgSrc;
            if (validImageTypes.includes(fileType)) {
                let doc = new jsPDF("p", "mm", "a4");

                let width = doc.internal.pageSize.getWidth();
                let height = doc.internal.pageSize.getHeight();
                doc.addImage(file, 'JPEG',  10, 10, width, height);
                imgSrc = doc.output('bloburl');
            } else {
                imgSrc = file
            }

            $("#preview-barcode").find("embed").remove();
            let embed = "<embed src="+ imgSrc +" frameborder='0' width='100%' height='500px' type='application/pdf' class='preview-pdf'>"
            $("#preview-barcode").append(embed)
        }



    function previewBarcode(id) {
        const { jsPDF } = window.jspdf;
        let doc = new jsPDF("p", "mm", "a4");
        let svgHtml = $(`#${id}`).html();
        if (svgHtml) {
            svgHtml = svgHtml.replace(/\r?\n|\r/g, '').trim();
        }

        let canvas = document.createElement('canvas');
        let context = canvas.getContext('2d');
        v = canvg.Canvg.fromString(context, svgHtml);
        v.start()

        let imgData = canvas.toDataURL('image/png');

        doc.addImage(imgData, 'PNG', 10, 10, 100, 100);
        imgSrc = doc.output('bloburl');

        $("#preview-barcode").find("embed").remove();
        let embed = "<embed src="+ imgSrc +" frameborder='0' width='100%' height='500px' type='application/pdf' class='preview-pdf'>"
        $("#preview-barcode").append(embed)
    }

    function printSelected() {
        previewPDF($('#main').attr('src'));
    }

    function printAll() {
        const { jsPDF } = window.jspdf;

        let imgSrc;
        let doc = new jsPDF("p", "mm", "a4");

        let width = doc.internal.pageSize.getWidth();
        let height = doc.internal.pageSize.getHeight();
        const listImage = $('#thumbs').find('.thumb-item img')
        for(let i = 0; i < listImage.length; i++) {
            const file = listImage[i].src

            doc.addImage(file, 'JPEG',  0, 0, width, height, `image-${i}`);
            doc.addPage();
        }

        imgSrc = doc.output('bloburl');

        $("#preview-barcode").find("embed").remove();
            let embed = "<embed src="+ imgSrc +" frameborder='0' width='100%' height='500px' type='application/pdf' class='preview-pdf'>"
        $("#preview-barcode").append(embed)
    }

    function showImage(index) {
        const element = $('#thumbs');
        element.children().remove()
        const getImages = $(`.image-preview-${index}`)

        for(let i = 0; i < getImages.length; i++) {

            const rawHtml = `<div class="col-md-4 thumb-item text-center amb-30"><img src="${getImages[i].src}" width="200" height="200" /> </div>`
            element.append(rawHtml)
        }

        $('#main').attr('src', $('#thumbs').find('.thumb-item:first-child img').attr('src'));

        $('#thumbs').find('.thumb-item img').on('click', function () {
            $('#main').attr('src', $(this).attr('src'));
        });
    }
    </script>
@endsection
