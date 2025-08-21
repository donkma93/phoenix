@extends('layouts.app',[
'class' => '',
'folderActive' => '',
'elementActive' => 'dashboard'
])

@section('styles')
<style>
    .table-responsive {
        overflow: unset;
    }
    .min-w-160 {
        min-width: 160px;
    }
    .min-w-40 {
        min-width: 40px;
    }
    .min-w-60 {
        min-width: 60px;
    }
    .form-group {
        margin-bottom: 1rem;
    }
    .search-form-group {
        display: flex;
        align-items: center;
        flex-direction: column;
    }
    label {
        display: inline-block;
        margin-bottom: 0.5rem;
    }
    .col-form-label {
        padding-top: calc(0.375rem + 1px);
        padding-bottom: calc(0.375rem + 1px);
        margin-bottom: 0;
        font-size: inherit;
        line-height: 1.5;
    }
    .search-form-group .search-label {
         min-width: 160px;
     }
    .modal-header {
        position: relative;
    }
    button.close {
        position: absolute;
        top: 16px;
        right: 16px;
    }
    #preview-barcode {
        min-height: 200px;
    }
    .scan-btn {
         position: absolute;
         height: 100%;
         top: 0;
         right: 0;
         z-index: 1;
         box-shadow: none;
        margin: 0;
     }
    @media (min-width: 576px) {
        .search-form-group {
            flex-direction: row;
        }
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
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('Request detail') }}</h2>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label mb-0"><b>{{ __('User') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $userRequest['user']['email'] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label mb-0"><b>{{ __('Option') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ App\Models\UserRequest::$optionName[$userRequest['option']] ?? '' }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label mb-0"><b>{{ __('Note') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $userRequest['note'] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label mb-0"><b>{{ __('File') }}</b></label>
                        @if(isset($userRequest['file']))
                        <div class="search-input">
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-preview-code" onclick="previewPDF(`{{ asset($userRequest['file']) }}`)" >
                                Preview file
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label mb-0"><b>{{ __('Type') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ ucfirst($userRequest['mRequestType']['name']) }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label mb-0"><b>{{ __('Status') }}</b></label>
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
                        <label class="col-form-label search-label mb-0"><b>{{ __('Packing type') }}</b></label>
                        <div class="search-input col-form-label">
                            i{{ App\Models\UserRequest::$packingTypes[$userRequest['packing_type']] }}
                        </div>
                    </div>
                    @endif

                    @if(isset($userRequest['prep_type']))
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label mb-0"><b>{{ __('Prep') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ App\Models\UserRequest::$prepTypes[$userRequest['prep_type']] }}
                        </div>
                    </div>
                    @endif

                    @if(isset($userRequest['label_by_type']))
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label mb-0"><b>{{ __('Label by') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ App\Models\UserRequest::$labelByTypes[$userRequest['label_by_type']] }}
                        </div>
                    </div>
                    @endif

                    @if(isset($userRequest['store_type']))
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label mb-0"><b>{{ __('Store type') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ App\Models\UserRequest::$storeTypes[$userRequest['store_type']] }}
                        </div>
                    </div>
                    @endif
                    @endif

                    @if($userRequest['mRequestType']['name'] == 'warehouse labor')
                    <form action="{{ route('staff.request.updateTime') }}" method="POST">
                        @csrf
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label mb-0"><b>{{ __('Total time') }}</b></label>
                            <div class="search-input col-form-label">
                                {{ $requestHour->hour ?? 0 }} hour
                            </div>
                        </div>

                        @if($userRequest['status'] == App\Models\UserRequest::STATUS_INPROGRESS)
                        <div class="form-group search-form-group">
                            <input type="hidden" name="user_request_id" value="{{ $userRequest['id'] }}" />
                            @if(empty($lastWorking))
                            <input type="submit" class="btn btn-info" value="Resumed" />
                            @else
                            <input type="submit" class="btn btn-warning" value="Pause" />
                            @endif
                        </div>
                        @endif
                    </form>
                    @endif

                    <div class="form-group search-form-group ">
                        @if($userRequest['status'] == App\Models\UserRequest::STATUS_NEW)
                        <button class="btn btn-success" onclick="updateRequest({{ $userRequest['id'] }}, 1)">Start request</button>
                        @elseif($userRequest['status'] == App\Models\UserRequest::STATUS_INPROGRESS)
                        <button class="btn btn-success" onclick="updateRequest({{ $userRequest['id'] }}, 2)">Request completed</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <button type="button" class="btn btn-info amb-16" data-toggle="modal" data-target="#saved-package-modal">Saved package</button>
            @if($userRequest['status'] == App\Models\UserRequest::STATUS_INPROGRESS && $userRequest['mRequestType']['name'] == "add package" && isset($userRequest['is_allow']))
                <button type="button" class="btn btn-success amb-16" data-toggle="modal" data-target="#group-modal" onclick="setValueForModal({{ $userRequest->id }})">Scan</button>
            @endif
            @if (count($packages) == 0)
                <div class="text-center">No data.</div>
            @else
                @if($userRequest['mRequestType']['name'] == "relabel")
                    <div class="table-responsive" style="padding: 0;">
                        <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-request-detail-table-relabel">
                            <thead class="text-primary">
                                <tr>
                                    <th class="min-w-40 text-center">#</th>
                                    <th>{{ __('Group Name') }}</th>
                                    <th>{{ __('Package number') }}</th>
                                    <th>{{ __('Unit number') }}</th>
                                    <th>{{ __('Saved package') }}</th>
                                    <th>{{ __('Tracking') }}</th>
                                    <th>{{ __('Images') }}</th>
                                    <th>{{ __('File') }}</th>
                                    <th>{{ __('Unit Code') }}</th>
                                    <th class="min-w-60"></th>
                                    @if($userRequest['status'] == App\Models\UserRequest::STATUS_INPROGRESS)
                                        <th></th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($packages as $package)
                                <input type="hidden" value="{{ $package->id }}"  id="id-{{ $loop->iteration }}"/>
                                <input type="hidden" value="{{ $package->received_unit_number }}"  id="unit-{{ $loop->iteration }}"/>
                                <tr>
                                    <td class="text-center">{{ ($packages->currentPage() - 1) * $packages->perPage() + $loop->iteration }}</td>
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
                                                <button type="button" class="btn btn-primary btn-round" data-toggle="modal" data-target="#modal-preview-code" onclick="previewPDF(`{{ asset($package->file) }}`)" >
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
                                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-preview-code" onclick="previewBarcode('package-barcode-{{ $loop->iteration }}')">
                                                {{ __('Preview Code') }}
                                            </button>
                                        @endif
                                    </td>
                                    @if($userRequest['status'] == App\Models\UserRequest::STATUS_INPROGRESS)
                                        <td>
                                            @if($package->package_number > $package->received_package_number)
                                                @php
                                                    $currentReceived = $package->received_unit_number ?? 0;
                                                    $remain = $package->unit_number - $currentReceived;
                                                @endphp
                                                <button type="button" class="btn btn-info btn-round apx-8" data-toggle="modal" data-target="#group-modal" onclick="setValueForModal({{ $package->id }}, {{ $package->unit_number }}, {{ $userRequest->user_id }}, {{ $remain }}, {{ $package->package_group_id }} )">Add packages</button>
                                            @else
                                                Done
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @elseif($userRequest['mRequestType']['name'] == 'return')
                    <div class="table-responsive">
                        <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-request-detail-table-return">
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
                                    @if($userRequest['status'] == App\Models\UserRequest::STATUS_INPROGRESS)
                                        <th></th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($packages as $package)
                                <input type="hidden" value="{{ $package->id }}"  id="id-{{ $loop->iteration }}"/>
                                <input type="hidden" value="{{ $package->received_unit_number }}"  id="unit-{{ $loop->iteration }}"/>
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
                                    @if($userRequest['status'] == App\Models\UserRequest::STATUS_INPROGRESS)
                                        <td>
                                            @if($package->unit_number > $package->received_unit_number)
                                                @php
                                                    $currentReceived = $package->received_unit_number ?? 0;
                                                    $remain = $package->unit_number - $currentReceived;
                                                @endphp
                                                <button type="button" class="btn btn-info apx-8" data-toggle="modal" data-target="#group-modal" onclick="setValueForModal({{ $package->id }}, {{ $package->unit_number }}, {{ $userRequest->user_id }}, {{ $remain }}, {{ $package->package_group_id }} )">Add packages</button>
                                            @else
                                                Done
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @elseif($userRequest['mRequestType']['name'] == 'removal')
                    <div class="table-responsive">
                        <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-request-detail-table-removal">
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
                                    @if($userRequest['status'] == App\Models\UserRequest::STATUS_INPROGRESS)
                                        <th></th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($packages as $package)
                                <input type="hidden" value="{{ $package->id }}"  id="id-{{ $loop->iteration }}"/>
                                <input type="hidden" value="{{ $package->received_unit_number }}"  id="unit-{{ $loop->iteration }}"/>
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
                                    @if($userRequest['status'] == App\Models\UserRequest::STATUS_INPROGRESS)
                                        <td>
                                            @if($package->unit_number > $package->received_unit_number)
                                                @php
                                                    $currentReceived = $package->received_unit_number ?? 0;
                                                    $remain = $package->unit_number - $currentReceived;
                                                @endphp
                                                <button type="button" class="btn btn-info apx-8" data-toggle="modal" data-target="#group-modal" onclick="setValueForModal({{ $package->id }}, {{ $package->unit_number }}, {{ $userRequest->user_id }}, {{ $remain }}, {{ $package->package_group_id }} )">Search packages</button>
                                            @else
                                                Done
                                            @endif
                                        </td>
                                    @endif
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
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-request-detail-table">
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
                                        @if($userRequest['status'] == App\Models\UserRequest::STATUS_INPROGRESS)
                                            <th></th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($packages as $package)
                                    <input type="hidden" value="{{ $package->id }}"  id="id-{{ $loop->iteration }}"/>
                                    <input type="hidden" value="{{ $package->received_unit_number }}"  id="unit-{{ $loop->iteration }}"/>
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
                                        @if($userRequest['status'] == App\Models\UserRequest::STATUS_INPROGRESS)
                                            <td>
                                                @if($package->package_number > $package->received_package_number)
                                                    @php
                                                        $remain = $package->package_number;
                                                        $currentReceived = $package->received_package_number ?? 0;
                                                        $remain = $package->package_number - $currentReceived;
                                                    @endphp
                                                    <button type="button" class="btn btn-info apx-8" data-toggle="modal" data-target="#group-modal" onclick="setValueForModal({{ $package->id }}, {{ $package->unit_number }}, {{ $userRequest->user_id }}, {{ $remain }}, {{ $package->package_group_id }},{{ $package->width ?? 0 }}, {{ $package->length ?? 0 }}, {{ $package->height ?? 0 }}, {{ $package->weight ?? 0 }} )">Add packages</button>
                                                @else
                                                    Done
                                                @endif

                                            </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                @elseif ($userRequest['mRequestType']['name'] == 'outbound')
                    <div class="table-responsive">
                        <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-request-detail-table-outbound">
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
                                        <th>{{ __('Pallet') }}</th>
                                    @endif
                                    @if($userRequest['status'] == App\Models\UserRequest::STATUS_INPROGRESS)
                                        <th></th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($packages as $package)
                                <input type="hidden" value="{{ $package->id }}"  id="id-{{ $loop->iteration }}"/>
                                <input type="hidden" value="{{ $package->received_unit_number }}"  id="unit-{{ $loop->iteration }}"/>
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
                                        <td></td>
                                    @endif
                                    @if($userRequest['status'] == App\Models\UserRequest::STATUS_INPROGRESS)
                                        <td>
                                            @if($package->package_number > $package->received_package_number)
                                                @php
                                                    $currentReceived = $package->received_package_number ?? 0;
                                                    $remain = $package->package_number - $currentReceived;
                                                @endphp
                                                <button type="button" class="btn btn-info apx-8" data-toggle="modal" data-target="#group-modal" onclick="setValueForModal({{ $package->id }}, {{ $package->unit_number }}, {{ $userRequest->user_id }}, {{ $remain }}, {{ $package->package_group_id }}, 0, 0, 0, 0)">Search packages</button>
                                            @else
                                                Done
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-request-detail-table-normal">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>{{ __('Group Name') }}</th>
                                    <th>{{ __('Package number') }}</th>
                                    <th>{{ __('Unit number') }}</th>
                                    <th>{{ __('Saved package') }}</th>
                                    <th>{{ __('File') }}</th>
                                    @if($userRequest['status'] == App\Models\UserRequest::STATUS_INPROGRESS)
                                        <th></th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($packages as $package)
                                <input type="hidden" value="{{ $package->id }}"  id="id-{{ $loop->iteration }}"/>
                                <input type="hidden" value="{{ $package->received_unit_number }}"  id="unit-{{ $loop->iteration }}"/>
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
                                    @if($userRequest['status'] == App\Models\UserRequest::STATUS_INPROGRESS)
                                        <td>
                                            @if($package->package_number > $package->received_package_number)
                                                @php
                                                    $currentReceived = $package->received_package_number ?? 0;
                                                    $remain = $package->package_number - $currentReceived;
                                                @endphp
                                                <button type="button" class="btn btn-info apx-8" data-toggle="modal" data-target="#group-modal" onclick="setValueForModal({{ $package->id }}, {{ $package->unit_number }}, {{ $userRequest->user_id }}, {{ $remain }}, {{ $package->package_group_id }}, 0, 0, 0, 0)">Search packages</button>
                                            @else
                                                Done
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endif
            <div class="d-flex justify-content-center justify-content-md-end amt-16">
                {{ $packages->appends(request()->all())->links() }}
            </div>
        </div>
    </div>
</div>
</div>

<!-- Modal -->
<div id="modal-preview-code" class="modal fade bd-example-modal-lg modal-first-index" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="preview-barcode">
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div id="scan-modal" class="modal fade bd-example-scan-lg modal-first-index" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <video id="video" style="border: 1px solid gray; width: 100%; height: 100%"></video>
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

<!-- Modal -->
<div id="group-modal" class="modal modal-fullsize bd-example-scan-lg" tabindex="-2" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    @if(!in_array($userRequest['mRequestType']['name'], ["add package", "removal", "return"]))
                        Select packages
                    @else
                        Add packages
                    @endif

                    @if(in_array($userRequest['mRequestType']['name'], ["removal", "return"]))
                        <button type="button" id="barcode-scan-check" class="btn btn-info" data-toggle="modal" data-target="#scan-modal">Scan</button>
                    @endif
                </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @if(!in_array($userRequest['mRequestType']['name'], ["add package", "removal", "return"]))
                @if($userRequest['mRequestType']['name'] == 'outbound')
                    <div class="modal-body">
                        <div class="form-group search-form-group">
                            <div class="search-label d-none d-sm-block"></div>
                            <div class="search-input text-center text-sm-left">
                                <button type="button" id="barcode-scan-outbound-button" class="btn btn-info" data-toggle="modal" data-target="#scan-modal">Scan</button>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('staff.request.savePackage') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                        <div class="modal-footer" id="shift-table">
                            <div class="table-responsive">
                            <input type="hidden" name="user_request_id" value="{{ $userRequest['id'] }}" />
                            <input type="hidden" name="user_id" value="{{ $userRequest['user_id'] }}" />
                                <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-request-outbound-package-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Package code') }}</th>
                                            <th>{{ __('Warehouse') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="shift-table-body">

                                    </tbody>
                                </table>
                            </div>
                            @if($userRequest['status'] == App\Models\UserRequest::STATUS_INPROGRESS)
                                <div class="search-form-group w-100">
                                    <input class="btn btn-block btn-success" type="submit" value="{{ __('Save') }}">
                                </div>
                            @endif
                        </div>
                    </form>
                @elseif($userRequest['mRequestType']['name'] == 'warehouse labor')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <label class="col-form-label search-label mb-0"><input type="radio" name="select-option" class="amr-16 aml-8" value="change-detail" checked/><b>{{ __('Change detail') }}</b></label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group search-form-group">
                                    <label class="col-form-label search-label mb-0"><b>{{ __('Package unit') }}</b></label>
                                    <div class="search-input">
                                        <input type="number" class="form-control w-100" id="shift-unit" value="0" />
                                    </div>
                                </div>

                                <div class="form-group search-form-group">
                                    <label class="col-form-label search-label mb-0"><b>{{ __('Received unit') }}</b></label>
                                    <div class="search-input">
                                        <input type="number" class="form-control w-100" id="shift-received-unit" value="0"/>
                                    </div>
                                </div>

                                <div class="form-group search-form-group">
                                    <label class="col-form-label search-label mb-0"><b>{{ __('Package weight') }}</b></label>
                                    <div class="search-input position-relative">
                                        <input id="shift-weight" type="text" class="form-control w-100" name="weight"/>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group search-form-group">
                                    <label class="col-form-label search-label mb-0"><b>{{ __('Package length') }}</b></label>
                                    <div class="search-input position-relative">
                                        <input id="shift-length" type="text" class="form-control w-100" name="length"/>
                                    </div>
                                </div>

                                <div class="form-group search-form-group">
                                    <label class="col-form-label search-label mb-0"><b>{{ __('Package width') }}</b></label>
                                    <div class="search-input position-relative">
                                        <input id="shift-width" type="number" class="form-control w-100" name="width"/>
                                    </div>
                                </div>

                                <div class="form-group search-form-group">
                                    <label class="col-form-label search-label mb-0"><b>{{ __('Package height') }}</b></label>
                                    <div class="search-input position-relative">
                                        <input id="shift-height" type="text" class="form-control w-100" name="height"/>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group search-form-group">
                                    <label class="col-form-label search-label mb-0"><b>{{ __('Warehouse') }}</b></label>
                                    <div class="search-input position-relative">
                                        <input type="text" class="form-control w-100" id="shift-warehouse" list="dropdown-modal-area" autocomplete="off" id="shift-warehouse" />
                                        <button type="button" id="shift-warehouse-button" class="btn scan-btn apy-4" data-toggle="modal" data-target="#scan-modal"><i class="fa fa-qrcode font-20"></i></button>
                                    </div>
                                </div>

                                <div class="form-group search-form-group">
                                    <label class="col-form-label search-label mb-0"><b>{{ __('Status') }}</b></label>
                                    <div class="search-input">
                                        <select id="shift-status" name="status" class="form-control w-100" id="shift-status">
                                            @foreach (App\Models\Package::$statusName as $key => $status)
                                                <option value="{{ $key }}">{{ $status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group search-form-group">
                                    <label class="col-form-label search-label mb-0"><b>{{ __('Package number') }}</b></label>
                                    <div class="search-input">
                                        <input type="number" class="form-control w-100" id="shift-package-number" value="0" />
                                    </div>
                                </div>

                                <div class="search-form-group w-100">
                                    <div class="search-label d-none d-sm-block"></div>
                                    <div class="search-input text-center text-sm-left">
                                        <div class="btn btn-success" onclick="addPackageWarehouseLabor()">Add</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <label class="col-form-label search-label mb-0"><input type="radio" name="select-option" class="amr-16 aml-8" value="delete-package" /><b>{{ __('Delete package') }}</b></label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="search-label d-none d-sm-block"></div>
                                <div class="search-input text-center text-sm-left">
                                    <button type="button" id="barcode-scan-button" class="btn btn-info" data-toggle="modal" data-target="#scan-modal">Scan</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('staff.request.savePackage') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-footer" id="shift-table">
                            <div class="table-responsive">
                            <input type="hidden" name="user_request_id" value="{{ $userRequest['id'] }}" />
                            <input type="hidden" name="user_id" value="{{ $userRequest['user_id'] }}" />
                                <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-request-warehouse-labor-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Package code') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Package Unit') }}</th>
                                            <th>{{ __('Unit receive') }}</th>
                                            <th>{{ __('Weight') }}</th>
                                            <th>{{ __('Width') }}</th>
                                            <th>{{ __('Height') }}</th>
                                            <th>{{ __('Length') }}</th>
                                            <th>{{ __('Warehouse Area') }}</th>
                                            <th>{{ __('Delete') }}</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="shift-table-body">

                                    </tbody>
                                </table>
                            </div>
                            @if($userRequest['status'] == App\Models\UserRequest::STATUS_INPROGRESS)
                                <div class="search-form-group w-100">
                                    <input class="btn btn-block btn-success" type="submit" value="{{ __('Save') }}">
                                </div>
                            @endif
                        </div>
                    </form>
                @else
                    <div class="modal-body">
                        <div class="form-group search-form-group">
                            <div class="search-label d-none d-sm-block"></div>
                            <div class="search-input text-center text-sm-left">
                                <button type="button" id="barcode-scan-button" class="btn btn-info" data-toggle="modal" data-target="#scan-modal">Scan</button>
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label mb-0 d-flex align-items-center">
                                <input type="checkbox" class="amr-16 aml-8" id="change-warehouse-area" style="margin-right: 12px;"/>
                                <b>{{ __('Change area') }}</b>
                            </label>
                            <div class="search-input position-relative">
                                <input type="text" class="form-control w-100" id="shift-modal-group-to-area" list="dropdown-modal-area" autocomplete="off" />
                                <button type="button" id="shift-to-warehouse-button" class="btn scan-btn apy-4" data-toggle="modal" data-target="#scan-modal"><i class="fa fa-qrcode font-20"></i></button>
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label mb-0 d-flex align-items-center">
                                <input type="checkbox" class="amr-16 aml-8"  id="change-status" style="margin-right: 12px;"/>
                                <b>{{ __('Change status') }}</b>
                            </label>
                            <div class="search-input">
                                <select id="shift-status" name="status" class="form-control w-100">
                                    @foreach (App\Models\Package::$statusName as $key => $status)
                                        @if(App\Models\Package::$statusName != App\Models\Package::STATUS_OUTBOUND)
                                            <option value="{{ $key }}" @if(App\Models\Package::STATUS_INBOUND == $key) selected @endif>{{ $status }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('staff.request.savePackage') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                        <div class="modal-footer" id="shift-table">
                            <div class="table-responsive">
                            <input type="hidden" name="user_request_id" value="{{ $userRequest['id'] }}" />
                            <input type="hidden" name="user_id" value="{{ $userRequest['user_id'] }}" />
                                <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-request-normal-package-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Package code') }}</th>
                                            <th>{{ __('Warehouse') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('To Warehouse') }}</th>
                                            <th>{{ __('To Status') }}</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="shift-table-body">

                                    </tbody>
                                </table>
                            </div>
                            @if($userRequest['status'] == App\Models\UserRequest::STATUS_INPROGRESS)
                                <div class="search-form-group w-100">
                                    <input class="btn btn-block btn-success" type="submit" value="{{ __('Save') }}">
                                </div>
                            @endif
                        </div>
                    </form>
                @endif
            @else
                @if($userRequest['mRequestType']['name'] == 'add package' && isset($userRequest['is_allow']))
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group search-form-group">
                                    <label class="col-form-label search-label mb-0"><b>{{ __('Warehouse') }}</b></label>
                                    <div class="search-input position-relative">
                                        <input type="text" class="form-control w-100" id="shift-warehouse" list="dropdown-modal-area" autocomplete="off" id="shift-warehouse" />
                                        <button type="button" id="shift-warehouse-button" class="btn scan-btn apy-4" data-toggle="modal" data-target="#scan-modal"><i class="fa fa-qrcode font-20"></i></button>
                                    </div>
                                </div>

                                <div class="search-form-group w-100">
                                    <div class="search-label d-none d-sm-block"></div>
                                    <div class="search-input text-center text-sm-left">
                                        <button type="button" id="barcode-scan-button" class="btn btn-info" data-toggle="modal" data-target="#scan-modal">Scan package</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group search-form-group">
                                    <label class="col-form-label search-label mb-0"><b>{{ __('Warehouse') }}</b></label>
                                    <div class="search-input position-relative">
                                        <input type="text" class="form-control w-100" id="shift-warehouse" list="dropdown-modal-area" autocomplete="off" id="shift-warehouse" />
                                        <button type="button" id="shift-warehouse-button" class="btn scan-btn apy-4" data-toggle="modal" data-target="#scan-modal"><i class="fa fa-qrcode font-20"></i></button>
                                    </div>
                                </div>

                                <div class="form-group search-form-group">
                                    <label class="col-form-label search-label mb-0"><b>{{ __('Status') }}</b></label>
                                    <div class="search-input">
                                        <select id="shift-status" name="status" class="form-control w-100" id="shift-status">
                                            @foreach (App\Models\Package::$statusName as $key => $status)
                                                <option value="{{ $key }}">{{ $status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group search-form-group">
                                    <label class="col-form-label search-label mb-0"><b>{{ __('Package number') }}</b></label>
                                    <div class="search-input">
                                        <input type="number" class="form-control w-100" id="shift-package-number" value="0" />
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group search-form-group">
                                    <label class="col-form-label search-label mb-0"><b>{{ __('Package unit') }}</b></label>
                                    <div class="search-input">
                                        <input type="number" class="form-control w-100" id="shift-unit" value="0" />
                                    </div>
                                </div>

                                <div class="form-group search-form-group">
                                    <label class="col-form-label search-label mb-0"><b>{{ __('Received unit') }}</b></label>
                                    <div class="search-input">
                                        <input type="number" class="form-control w-100" id="shift-received-unit" value="0"/>
                                    </div>
                                </div>

                                <div class="form-group search-form-group">
                                    <label class="col-form-label search-label mb-0"><b>{{ __('Package weight') }}</b></label>
                                    <div class="search-input position-relative">
                                        <input id="shift-weight" type="text" class="form-control w-100" name="weight"/>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group search-form-group">
                                    <label class="col-form-label search-label mb-0"><b>{{ __('Package length') }}</b></label>
                                    <div class="search-input position-relative">
                                        <input id="shift-length" type="text" class="form-control w-100" name="length"/>
                                    </div>
                                </div>

                                <div class="form-group search-form-group">
                                    <label class="col-form-label search-label mb-0"><b>{{ __('Package width') }}</b></label>
                                    <div class="search-input position-relative">
                                        <input id="shift-width" type="number" class="form-control w-100" name="width"/>
                                    </div>
                                </div>

                                <div class="form-group search-form-group">
                                    <label class="col-form-label search-label mb-0"><b>{{ __('Package height') }}</b></label>
                                    <div class="search-input position-relative">
                                        <input id="shift-height" type="text" class="form-control w-100" name="height"/>
                                    </div>
                                </div>

                                <input type="hidden" id="user-weight" />
                                <input type="hidden" id="user-length" />
                                <input type="hidden" id="user-width" />
                                <input type="hidden" id="user-height" />

                                <div class="search-form-group w-100">
                                    <div class="search-label d-none d-sm-block"></div>
                                    <div class="search-input text-center text-sm-left">
                                        <div class="btn btn-info" onclick="addRow('{{ $userRequest['mRequestType']['name'] }}')">Add</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <form action="{{ route('staff.request.savePackage') }}" method="POST" enctype="multipart/form-data">
                @csrf
                    <div class="modal-footer" id="shift-table">
                        <div class="table-responsive">
                        <input type="hidden" name="user_request_id" value="{{ $userRequest['id'] }}" />
                        <input type="hidden" name="user_id" value="{{ $userRequest['user_id'] }}" />
                            <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-request-return-removal-package-table">
                                <thead>
                                    <tr>
                                        @if($userRequest['mRequestType']['name'] == 'add package')
                                            <th>{{ __('Barcode') }}</th>
                                        @endif
                                        <th>{{ __('Warehouse') }}</th>
                                        <th>{{ __('Unit') }}</th>
                                        <th>{{ __('Received') }}</th>
                                        <th>{{ __('Weight') }}</th>
                                        <th>{{ __('Length') }}</th>
                                        <th>{{ __('Width') }}</th>
                                        <th>{{ __('Height') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="shift-table-body">

                                </tbody>
                            </table>
                        </div>
                        @if($userRequest['status'] == App\Models\UserRequest::STATUS_INPROGRESS)
                            <div class="search-form-group w-100">
                                <input class="btn btn-block btn-success" type="submit" value="{{ __('Save') }}">
                            </div>
                        @endif
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

<!-- Modal -->
<div id="saved-package-modal" class="modal mod modal-fullscreen bd-example-scan-lg" tabindex="-2" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
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
                    <form action="{{ route('staff.request.updatePackage') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                        <div class="table-responsive" id="modal-table">
                            <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-request-received-package-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>{{ __('Group') }}</th>
                                        <th>{{ __('Barcode') }}</th>
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
                                            @if($userRequest['status'] == App\Models\UserRequest::STATUS_INPROGRESS)
                                                <td>
                                                    @if($userRequest['mRequestType']['name'] != 'outbound' && !isset($package['deleted_at']))
                                                        <input class="form-control w-100 @error('package.' . $package['id'] . '.barcode') is-invalid @enderror" name="{{ 'package['. $package['id'] . '][barcode]' }}" value="{{ $package['barcode'] }}" />
                                                    @else
                                                        {{ $package['barcode'] }}
                                                    @endif
                                                </td>
                                                <td>{{ $package['unit_number'] }}</td>
                                                <td>
                                                    @if(!in_array($userRequest['mRequestType']['name'], ["add package", "removal", "return"]))
                                                        {{ $package['received_unit_number'] }}
                                                    @else
                                                        <input class="form-control w-100 @error('package.' . $package['id'] . '.received_unit_number') is-invalid @enderror" name="{{ 'package['. $package['id'] . '][received_unit_number]' }}" value="{{ $package['received_unit_number'] }}" />
                                                    @endif
                                                </td>
                                                <td class="position-relative">
                                                    @if($userRequest['mRequestType']['name'] != 'outbound'  && !isset($package['deleted_at']))
                                                        <input class="form-control w-100 @error('package.' . $package['id'] . '.warehouse') is-invalid @enderror" name="{{ 'package['. $package['id'] . '][warehouse]' }}" list="dropdown-modal-warehouse-{{ $i }}" value="{{ $package['warehouseArea']['name'] ?? '' }}" id="saved-warehouse-input-{{$i}}"/>
                                                        <button type="button" id="saved-warehouse-button-{{$i}}" class="btn scan-btn apy-4" data-toggle="modal" data-target="#scan-modal"><i class="fa fa-qrcode font-20"></i></button>
                                                        <datalist id="dropdown-modal-warehouse-{{ $i }}">
                                                            @foreach($warehouseAreas as $area)
                                                            <option value="{{ $area['name'] }}">
                                                            @endforeach
                                                        </datalist>
                                                    @else
                                                        {{ $package['warehouseArea']['name'] ?? '' }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($userRequest['mRequestType']['name'] != 'outbound'  && !isset($package['deleted_at']))
                                                        <select name="{{ 'package['. $package['id'] . '][status]' }}" class="form-control w-100 @error('package.' . $package['id'] . '.status') is-invalid @enderror">
                                                            <option selected></option>
                                                            @foreach (App\Models\Package::$statusName as $key => $status)
                                                                <option value="{{ $key }}"
                                                                @if (isset($package['status']) && $package['status'] == $key)
                                                                            selected
                                                                        @endif
                                                                >{{ $status }}</option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        @if(isset($package['deleted_at']))
                                                            Delete
                                                        @else
                                                            {{ App\Models\Package::$statusName[$package['status']] }}
                                                        @endif
                                                    @endif
                                                </td>
                                                <input type="hidden" name="{{ 'package['. $package['id'] . '][id]' }}" value="{{ $package['id'] }}" />
                                                <input type="hidden" name="type_name" value="{{ $userRequest['mRequestType']['name'] }}" />
                                                <input type="hidden" name="request_id" value="{{ $userRequest['id'] }}" />
                                                <input type="hidden" name="{{ 'package['. $package['id'] . '][unit_number]' }}" value="{{ $package['unit_number'] }}" />
                                                <input type="hidden" name="{{ 'package['. $package['id'] . '][package_group_id]' }}" value="{{ $package['package_group_id'] }}" />
                                            @else
                                                <td>{{ $package['barcode'] }}</td>
                                                <td>{{ $package['unit_number'] }}</td>
                                                <td>{{ $package['received_unit_number'] }}</td>
                                                <td>{{ $package['warehouseArea']['name'] ?? '' }}</td>
                                                <td>{{ App\Models\Package::$statusName[$package['status']] }}</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if($userRequest['status'] == App\Models\UserRequest::STATUS_INPROGRESS && $userRequest['mRequestType']['name'] != 'outbound')
                                <div class="search-form-group">
                                    <input class="btn btn-block btn-success" type="submit" value="{{ __('Save') }}">
                                </div>
                            @endif
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
  @php
        $packageSaved = $userRequest['packages'] ?? '';

        $packageSavedList = explode(',', $packageSaved);
  @endphp
  <script>
    const packagesConvert = @php echo json_encode($packages) @endphp;
    const allPackages = @php echo json_encode($allPackages) @endphp;
    const packages = packagesConvert.data
    const packageSaved = @php echo json_encode($packageSavedList) @endphp;

    const requestUnit = ['return', 'removal' , 'add package']
    const requestReturn = ['return', 'removal' ]
    const requestType =  '@php echo $userRequest['mRequestType']['name'] @endphp'

    let warehouseAreas = @php echo json_encode($warehouseAreas) @endphp;
    let warehouses = @php echo json_encode($warehouses) @endphp;
    const packageStatusList = @php echo json_encode(App\Models\Package::$statusName) @endphp;

    let barcodeList = []
    let totalAdded = 0;

    if(!requestUnit.includes(requestType) && requestType != 'outbound' && requestType != 'warehouse labor') {
        createSuggestBlock(document.getElementById("shift-modal-group-to-area"), warehouses, 'dropdown-modal-area');
        $('#group-table').hide()
    }

    if(requestUnit.includes(requestType)) {
        createSuggestBlock(document.getElementById("shift-warehouse"), warehouses, 'dropdown-modal-area');
        $('#shift-table').hide()
    }

    const warehouseInputUpdates = document.getElementsByClassName("warehouse-input-update")

    let codeReader;
    let selectedDeviceId;

    for(let i = 0; i < warehouseInputUpdates.length; i++) {
        createSuggestBlock(warehouseInputUpdates[i], warehouses);
    }
    // End autocomplete

    // Set scanner
    window.addEventListener('load', function () {

      try {
        codeReader = new window.zxing.BrowserMultiFormatReader()
        codeReader.getVideoInputDevices()
        .then((videoInputDevices) => {
            if (videoInputDevices.length < 1) {
                console.log('No video devices found');
                codeReader = null;
                return;
            }
            selectedDeviceId = videoInputDevices[0].deviceId;
            $('#scan-modal').on('hidden.coreui.modal', function (e) {
                codeReader.reset();
            })
            if(requestUnit.includes(requestType)) {
                document.getElementById('shift-warehouse-button').addEventListener('click', () => {
                    codeReader.decodeFromVideoDevice(selectedDeviceId, 'video', (result, err) => {
                        if (result) {
                            const areaName = warehouseAreas.find(area =>{
                                return area.barcode == result.text
                            });

                            if(areaName) {
                                if(areaName['is_full'] == 1) {
                                    createFlash([{type: 'error', content: 'This area is full !'}])
                                    $(`#shift-warehouse`).val('')
                                } else {
                                    $(`#shift-warehouse`).val(areaName.name)
                                }
                            } else {
                                createFlash([{type: 'error', content: 'Area code not correct !'}])
                            }

                            $('#scan-modal').modal('hide');
                            codeReader.reset();
                        }
                        if (err && !(err instanceof window.zxing.NotFoundException)) {
                            console.log(err);
                            $('#scan-modal').modal('hide');
                            codeReader.reset();
                        }
                    })
                })
                if(requestReturn.includes(requestType)) {
                    document.getElementById('barcode-scan-check').addEventListener('click', () => {
                        codeReader.decodeFromVideoDevice(selectedDeviceId, 'video', (result, err) => {
                            if (result) {
                                scanCheckingPackage(result.text)

                                $('#scan-modal').modal('hide');
                                codeReader.reset();
                            }
                            if (err && !(err instanceof window.zxing.NotFoundException)) {
                                console.log(err);
                                $('#scan-modal').modal('hide');
                                codeReader.reset();
                            }
                        })
                    })
                }
                if(requestType == "add package") {
                    document.getElementById('barcode-scan-button').addEventListener('click', () => {
                        codeReader.decodeFromVideoDevice(selectedDeviceId, 'video', (result, err) => {
                            if (result) {
                                scanAddPackage(result.text)

                                $('#scan-modal').modal('hide');
                                codeReader.reset();
                            }
                            if (err && !(err instanceof window.zxing.NotFoundException)) {
                                console.log(err);
                                $('#scan-modal').modal('hide');
                                codeReader.reset();
                            }
                        })
                    })
                }
            } else {
                if(requestType == 'outbound') {
                    document.getElementById('barcode-scan-outbound-button').addEventListener('click', () => {
                        codeReader.decodeFromVideoDevice(selectedDeviceId, 'video', (result, err) => {
                            if (result) {
                                if(barcodeList.includes(result.text)) {
                                    createFlash([{type: 'error', content: 'This package is in'}])
                                } else {
                                    scanBarcodeOutbound(result.text)
                                }

                                $('#scan-modal').modal('hide');
                                codeReader.reset();
                            }
                            if (err && !(err instanceof window.zxing.NotFoundException)) {
                                console.log(err);
                                $('#scan-modal').modal('hide');
                                codeReader.reset();
                            }
                        })
                    })
                } else {
                    if(requestType != 'warehouse labor') {
                        document.getElementById('shift-to-warehouse-button').addEventListener('click', () => {
                            codeReader.decodeFromVideoDevice(selectedDeviceId, 'video', (result, err) => {
                                if (result) {
                                    const areaName = warehouseAreas.find(area =>{
                                        return area.barcode == result.text
                                    });

                                    if(areaName) {
                                        if(areaName['is_full'] == 1) {
                                            createFlash([{type: 'error', content: 'This area is full !'}])
                                            $(`#shift-modal-group-to-area`).val('')
                                        } else {
                                            $(`#shift-modal-group-to-area`).val(areaName.name)
                                        }
                                    } else {
                                        createFlash([{type: 'error', content: 'Area code not correct !'}])
                                    }

                                    $('#scan-modal').modal('hide');
                                    codeReader.reset();
                                }
                                if (err && !(err instanceof window.zxing.NotFoundException)) {
                                    console.log(err);
                                    $('#scan-modal').modal('hide');
                                    codeReader.reset();
                                }
                            })
                        })
                    } else {
                        document.getElementById('shift-warehouse-button').addEventListener('click', () => {
                            codeReader.decodeFromVideoDevice(selectedDeviceId, 'video', (result, err) => {
                                if (result) {
                                    const areaName = warehouseAreas.find(area =>{
                                        return area.barcode == result.text
                                    });

                                    if(areaName) {
                                        if(areaName['is_full'] == 1) {
                                            createFlash([{type: 'error', content: 'This area is full !'}])
                                            $(`#shift-warehouse`).val('')
                                        } else {
                                            $(`#shift-warehouse`).val(areaName.name)
                                        }
                                    } else {
                                        createFlash([{type: 'error', content: 'Area code not correct !'}])
                                    }

                                    $('#scan-modal').modal('hide');
                                    codeReader.reset();
                                }
                                if (err && !(err instanceof window.zxing.NotFoundException)) {
                                    console.log(err);
                                    $('#scan-modal').modal('hide');
                                    codeReader.reset();
                                }
                            })
                        })
                    }

                    document.getElementById('barcode-scan-button').addEventListener('click', () => {
                        codeReader.decodeFromVideoDevice(selectedDeviceId, 'video', (result, err) => {
                        if (result) {
                            if(barcodeList.includes(result.text)) {
                                createFlash([{type: 'error', content: 'This package is in'}])
                            } else {
                                if(requestType == 'warehouse labor') {
                                    scanBarcodeWarehouseLabor(result.text)
                                } else {
                                    console.log("result.text", result.text)
                                    scanBarcode(result.text)
                                }
                            }
                            $('#scan-modal').modal('hide');
                            codeReader.reset();
                        }
                        if (err && !(err instanceof window.zxing.NotFoundException)) {
                            console.log(err);
                            $('#scan-modal').modal('hide');
                            codeReader.reset();
                        }
                        })
                    })
                }
            }

            if(packageSaved.length > 0) {
                for(let i = 0; i < packageSaved.length; i++) {
                    try {
                        document.getElementById(`saved-warehouse-button-${i}`).addEventListener('click', () => {
                            codeReader.decodeFromVideoDevice(selectedDeviceId, 'video', (result, err) => {
                                if (result) {
                                    const areaName = warehouseAreas.find(area =>{
                                        return area.barcode == result.text
                                    });

                                    if(areaName) {
                                        if(areaName['is_full'] == 1) {
                                            createFlash([{type: 'error', content: 'This area is full !'}])
                                            $(`#saved-warehouse-input-${i}`).val('')
                                        } else {
                                            $(`#saved-warehouse-input-${i}`).val(areaName.name)
                                        }
                                    } else {
                                        createFlash([{type: 'error', content: 'Area code not correct !'}])
                                    }

                                    $('#scan-modal').modal('hide');
                                    codeReader.reset();
                                }
                                if (err && !(err instanceof window.zxing.NotFoundException)) {
                                    console.log(err);
                                    $('#scan-modal').modal('hide');
                                    codeReader.reset();
                                }
                            })
                        })
                    } catch(e) {}
                }
            }
        }).catch((err) => { console.log(err)})
      } catch(err){
        console.log(err)
      }
    })
    // End set scanner

    function scanAddPackage(barcode) {
        let packageIndex = allPackages.findIndex(element => element.barcode == barcode)
        if(packageIndex > -1 && !barcodeList.includes(barcode)) {
            let package = allPackages[packageIndex]
            if(package.status != 2) {
                barcodeList.push(package.barcode)

                let areaInfo;
                const areaName = $('#shift-warehouse').val()

                if(areaName) {
                    areaInfo = warehouseAreas.find(area =>{
                        return area.name == areaName
                    });

                    if(!areaInfo) {
                        $('#shift-warehouse').addClass('is-invalid')
                        createFlash([{type: 'error', content: 'Warehouse Area not existed'}])
                        loading(false)

                        return
                    }

                    if(areaInfo.is_full == 1) {
                        $('#shift-warehouse').addClass('is-invalid')
                        createFlash([{type: 'error', content: 'This Area is full'}])
                        loading(false)

                        return
                    }
                }

                const element = $('#shift-table-body')
                let count = $('#shift-table-body tr').length;
                $('#shift-table').show()
                const row = `
                    <tr id="shift-table-row-${count}">
                        <td>${package.barcode}</td>
                        <td>${areaInfo ? areaInfo.name : 'Not in warehouse area'}</td>
                        <td>${package.unit_number}</td>
                        <td>${package.unit_number}</td>
                        <td>${package.weight}</td>
                        <td>${package.length}</td>
                        <td>${package.width}</td>
                        <td>${package.height}</td>
                        <td>${packageStatusList[package.status]}</td>
                        <td>
                            <div class="btn btn-danger" onclick="removeRow(${count}, 1, ${package.barcode})">Remove</button>
                        </td>
                        <input type="hidden" name="request_package_id" value="${package.package_group_id}" />
                        <input type="hidden" name="id" value="${package.id}" />
                        <input type="hidden" name="package[${count}][unit_number]" value="${parseInt(unit_number)}" />
                        <input type="hidden" name="package[${count}][received_unit_number]" value="${parseInt(unit_number)}" id="package-received-unit-${count}" />
                        <input type="hidden" name="package[${count}][warehouse_area_id]" value="${areaInfo ? areaInfo.name : ''}" />
                        <input type="hidden" name="package[${count}][id]" value="${package.id}" />
                        <input type="hidden" name="package[${count}][status]" value="${status}" />
                        <input type="hidden" name="package[${count}][weight]" value="${package.weight ? parseFloat(package.weight) : ''}" />
                        <input type="hidden" name="package[${count}][length]" value="${package.length ? parseFloat(package.length) : ''}" />
                        <input type="hidden" name="package[${count}][width]" value="${package.width ? parseFloat(package.width) : ''}" />
                        <input type="hidden" name="package[${count}][height]" value="${package.height ? parseFloat(package.height) : ''}" />

                        <input type="hidden" name="package[${count}][weight_user]" value="${package.weight ? parseFloat(package.weight) : ''}" />
                        <input type="hidden" name="package[${count}][length_user]" value="${package.length ? parseFloat(package.length) : ''}" />
                        <input type="hidden" name="package[${count}][width_user]" value="${package.width ? parseFloat(package.width) : ''}" />
                        <input type="hidden" name="package[${count}][height_user]" value="${package.height ? parseFloat(package.height) : ''}" />

                        <input type="hidden" name="package[${count}][save]" value="true" />
                    </tr>
                `

                element.append(row)
            } else {
                createFlash([{type: 'error', content: 'Package is incorrect'}])
                $('#scan-modal').modal('hide');
                codeReader.reset();
            }

        } else {
            createFlash([{type: 'error', content: 'Package is incorrect'}])
            $('#scan-modal').modal('hide');
            codeReader.reset();
        }
    }

    function previewPDF(file) {
        $("#preview-barcode").find("embed").remove();

        const { jsPDF } = window.jspdf;
        const splitFile = file.split('.');
        const fileType = splitFile[splitFile.length - 1];
        const validImageTypes = ['gif', 'jpeg', 'png', 'tiff', 'jpg', 'heif'];

        let imgSrc;
        if (validImageTypes.includes(fileType)) {
            let doc = new jsPDF("p", "mm", "a4");

            let width = doc.internal.pageSize.getWidth();
            let height = doc.internal.pageSize.getHeight();
            doc.addImage(file, 'JPEG',  0, 0, width, height);
            imgSrc = doc.output('bloburl');

        } else {
            imgSrc = file
        }


        let embed = "<embed src="+ imgSrc +" frameborder='0' width='100%' height='500px' type='application/pdf' class='preview-pdf'>"
        $("#preview-barcode").append(embed)
    }

    function updateRequest(id, status) {
        //loading()
        $.ajax({
            type: 'POST',
            url: "{{ route('staff.request.update') }}",
            data: {
                id,
                status,
                _token: '{{csrf_token()}}'
            },
            success:function(data) {
                let url = "{{ route('staff.request.detail', ['id' => 'id']) }}"
                url = url.replace('id', id);
                window.location.href = url
            },
            error: function() {
                //loading(false);
                alert('Something wrong! Please contact admin for more information!')
            }
        });
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

    let unit_number;
    let currentId;
    let currentGroup;
    let remainNumber;
    let userId = 0;
    let selectedPackages = [];
    let size = {}
    const requestId = @php echo $userRequest['id'] @endphp;

    function setValueForModal(id, unit, user_id, number, groupId, width, length, height, weight) {
        unit_number = unit
        userId = user_id
        if(!currentId || (currentId && currentId != id)) {
            barcodeList = []
            selectedPackages = []
            currentId = id
            $('#group-table').find('#modal-table').remove()
            totalAdded = 0
            remainNumber = number
            if(currentId) {
                $('#shift-table-body').children().remove()
                $(`#shift-table`).hide()
            }

            currentGroup = groupId

            if(requestType == 'add package') {
                $('#shift-weight').val(weight)
                $('#shift-length').val(length)
                $('#shift-width').val(width)
                $('#shift-height').val(height)
                //
                $('#user-weight').val(weight)
                $('#user-length').val(length)
                $('#user-width').val(width)
                $('#user-height').val(height)
                //
                $('#shift-unit').val(unit)
                $('#shift-received-unit').val(unit)
                size = {
                    weight,
                    length,
                    width,
                    height
                }
            }

            if(requestType == 'warehouse labor') {
                $('#shift-unit').val(unit)
                $('#shift-received-unit').val(unit)
            }
        }
    }

    let index = 0;

    function addRow(type) {
        loading()
        const packageNumber = $('#shift-package-number').val()

        if(isNaN(packageNumber)) {
            createFlash([{type: 'error', content: 'Please enter number'}])
            $('#shift-package-number').addClass('is-invalid')
            loading(false)

            return
        }

        if(packageNumber <= 0) {
            createFlash([{type: 'error', content: 'Please enter greater than 0'}])
            $('#shift-package-number').addClass('is-invalid')
            loading(false)

            return
        }

        const unit = $('#shift-unit').val()

        if(isNaN(unit)) {
            createFlash([{type: 'error', content: 'Please enter number'}])
            $('#shift-unit').addClass('is-invalid')
            loading(false)

            return
        }

        if(unit <= 0) {
            createFlash([{type: 'error', content: 'Please enter greater than 0'}])
            $('#shift-unit').addClass('is-invalid')
            loading(false)

            return
        }

        const receivedUnit = $('#shift-received-unit').val()

        if(isNaN(receivedUnit)) {
            createFlash([{type: 'error', content: 'Please enter number'}])
            $('#shift-received-unit').addClass('is-invalid')
            loading(false)

            return
        }

        if(receivedUnit <= 0) {
            createFlash([{type: 'error', content: 'Please enter greater than 0'}])
            $('#shift-received-unit').addClass('is-invalid')
            loading(false)

            return
        }

        if(parseInt(receivedUnit) > parseInt(unit)) {
            createFlash([{type: 'error', content: 'Received number must not be greater than package unit'}])
            $('#shift-received-unit').addClass('is-invalid')
            loading(false)

            return
        }

        if(type == 'add package') {
            if((totalAdded + packageNumber) > remainNumber ) {
                createFlash([{type: 'error', content: 'Out of limit'}])
                $('#shift-package-number').addClass('is-invalid')
                loading(false)

                return
            }

            totalAdded = totalAdded + packageNumber


        } else {
            if(totalAdded + (receivedUnit * packageNumber) > remainNumber) {
                createFlash([{type: 'error', content: 'Out of limit'}])
                $('#shift-received-unit').addClass('is-invalid')
                $('#shift-package-number').addClass('is-invalid')
                loading(false)

                return
            }

            totalAdded = totalAdded + (receivedUnit * packageNumber)
        }

        const weight = $('#shift-weight').val()
        const length = $('#shift-length').val()
        const width = $('#shift-width').val()
        const height = $('#shift-height').val()

        const uWeight = $('#user-weight').val()
        const uLength = $('#user-length').val()
        const uWidth = $('#user-width').val()
        const uHeight = $('#user-height').val()

        if(weight && isNaN(weight)) {
            createFlash([{type: 'error', content: 'Please enter number'}])
            $('#shift-weight').addClass('is-invalid')
            loading(false)

            return
        }

        if(weight && weight <= 0) {
            createFlash([{type: 'error', content: 'Please enter greater than 0'}])
            $('#shift-weight').addClass('is-invalid')
            loading(false)

            return
        }

        if(length && isNaN(length)) {
            createFlash([{type: 'error', content: 'Please enter number'}])
            $('#shift-length').addClass('is-invalid')
            loading(false)

            return
        }

        if(length && length <= 0) {
            createFlash([{type: 'error', content: 'Please enter greater than 0'}])
            $('#shift-length').addClass('is-invalid')
            loading(false)

            return
        }

        if(width && isNaN(width)) {
            createFlash([{type: 'error', content: 'Please enter number'}])
            $('#shift-width').addClass('is-invalid')
            loading(false)

            return
        }

        if(width && width <= 0) {
            createFlash([{type: 'error', content: 'Please enter greater than 0'}])
            $('#shift-width').addClass('is-invalid')
            loading(false)

            return
        }

        if(height && isNaN(height)) {
            createFlash([{type: 'error', content: 'Please enter number'}])
            $('#shift-height').addClass('is-invalid')
            loading(false)

            return
        }

        if(height && height <= 0) {
            createFlash([{type: 'error', content: 'Please enter greater than 0'}])
            $('#shift-height').addClass('is-invalid')
            loading(false)

            return
        }


        const areaName = $('#shift-warehouse').val()
        let areaInfo;

        if(areaName) {
            areaInfo = warehouseAreas.find(area =>{
                return area.name == areaName
            });

            if(!areaInfo) {
                $('#shift-warehouse').addClass('is-invalid')
                createFlash([{type: 'error', content: 'Warehouse Area not existed'}])
                loading(false)

                return
            }

            if(areaInfo.is_full == 1) {
                $('#shift-warehouse').addClass('is-invalid')
                createFlash([{type: 'error', content: 'This Area is full'}])
                loading(false)

                return
            }
        }
        const rowCount = $('#shift-table-body tr').length;
        if(rowCount >= 0) {
            $('#shift-table').show()
        }

        const status =  $('#shift-status  :selected').val()
        const statusName = $('#shift-status  :selected').text()
        const element = $('#shift-table-body')

        for(let i = 0; i < packageNumber; i++) {
            index = index + 1
            const row = `
                <tr id="shift-table-row-${index}">
                    <td>${areaInfo ? areaInfo.name : 'Not in warehouse area'}</td>
                    <td>${unit}</td>
                    <td>${receivedUnit}</td>
                    <td>${weight}</td>
                    <td>${length}</td>
                    <td>${width}</td>
                    <td>${height}</td>
                    <td>${statusName}</td>
                    <td>
                        <div class="btn btn-danger" onclick="removeRow(${index}, ${type == 'add package' ? 1 : 2}, null)">Remove</button>
                    </td>
                    <input type="hidden" name="request_package_id" value="${currentId}" />
                    <input type="hidden" name="package_group_id" value="${currentGroup}" />
                    <input type="hidden" name="package[${index}][unit_number]" value="${parseInt(unit)}" />
                    <input type="hidden" name="package[${index}][received_unit_number]" value="${parseInt(receivedUnit)}" id="package-received-unit-${index}" />
                    <input type="hidden" name="package[${index}][warehouse_area_id]" value="${areaInfo ? areaInfo.name : ''}" />
                    <input type="hidden" name="package[${index}][status]" value="${status}" />
                    <input type="hidden" name="package[${index}][weight]" value="${weight ? parseFloat(weight) : ''}" />
                    <input type="hidden" name="package[${index}][length]" value="${length ? parseFloat(length) : ''}" />
                    <input type="hidden" name="package[${index}][width]" value="${width ? parseFloat(width) : ''}" />
                    <input type="hidden" name="package[${index}][height]" value="${height ? parseFloat(height) : ''}" />

                    <input type="hidden" name="package[${index}][weight_user]" value="${uWeight ? parseFloat(uWeight) : ''}" />
                    <input type="hidden" name="package[${index}][length_user]" value="${uLength ? parseFloat(uLength) : ''}" />
                    <input type="hidden" name="package[${index}][width_user]" value="${uWidth ? parseFloat(uWidth) : ''}" />
                    <input type="hidden" name="package[${index}][height_user]" value="${uHeight ? parseFloat(uHeight) : ''}" />

                    <input type="hidden" name="package[${index}][save]" value="true" />
                </tr>
            `

            element.append(row)
        }

        $('#shift-unit').removeClass('is-invalid')
        $('#shift-received-unit').removeClass('is-invalid')
        $('#shift-warehouse').removeClass('is-invalid')
        $('#shift-weight').removeClass('is-invalid')
        $('#shift-package-number').removeClass('is-invalid')
        $('#shift-weight').removeClass('is-invalid')
        $('#shift-length').removeClass('is-invalid')
        $('#shift-width').removeClass('is-invalid')
        $('#shift-height').removeClass('is-invalid')

        $('#shift-package-number').val(0)
        $('#shift-warehouse').val('')
        if(type == 'add package') {
            $('#shift-weight').val(size.weight)
            $('#shift-length').val(size.length)
            $('#shift-width').val(size.width)
            $('#shift-height').val(size.height)
        } else {
            $('#shift-unit').val(0)
            $('#shift-received-unit').val(0)
            $('#shift-weight').val(null)
            $('#shift-length').val(null)
            $('#shift-width').val(null)
            $('#shift-height').val(null)
        }
        $("#shift-status").val(0);

        loading(false)
    }

    function removeRow(index, type, barcode) {
        loading()

        const row = $(`#shift-table-row-${index}`)
        const rowCount = $('#shift-table-body tr').length;
        let oldValue;

        switch(type) {
            case 0:
                const i = barcodeList.indexOf(barcode);
                if (i > -1) {
                    barcodeList.splice(i, 1);
                }
                totalAdded = totalAdded - 1
            break

            case 1:
                const x = barcodeList.indexOf(barcode);
                if (x > -1) {
                    barcodeList.splice(x, 1);
                }
                totalAdded = totalAdded - 1
            break

            case 2:
                oldValue = $(`#package-received-unit-${index}`).val()
                totalAdded = totalAdded - oldValue
            break
        }


        if(rowCount <= 1) {
            $('#shift-table').hide()
        }

        row.remove()
        loading(false)
    }

    async function getPackage(barcode) {
        let result;
        await $.ajax({
            type: 'POST',
            url: "{{ route('staff.request.getPackage') }}",
            data: {
                user_id: userId,
                package_group_id: currentGroup,
                unit_number,
                barcode,
                _token: '{{csrf_token()}}'
            },
            success:function(data) {
                result = data
            },
            error: function(e) {
                loading(false);
                alert('Something wrong! Please contact admin for more information!')
            }
        });

        return result
    }

    async function checkPackage(barcode) {
        let result;
        await $.ajax({
            type: 'POST',
            url: "{{ route('staff.request.checkPackage') }}",
            data: {
                user_id: userId,
                package_group_id: currentGroup,
                barcode,
                _token: '{{csrf_token()}}'
            },
            success:function(data) {
                result = data
            },
            error: function(e) {
                loading(false);
                alert('Something wrong! Please contact admin for more information!')
            }
        });

        return result
    }

    async function scanBarcode(barcode) {
        loading()
        if(totalAdded == remainNumber) {
            createFlash([{type: 'error', content: 'Packages are enough'}])
            loading(false);

            return
        }
        console.log(barcode);
        const data = await getPackage(barcode)
        console.log(data);
        let statusId  = null
        let statusName = "No change"
        let warehouseName = "No change"

        if(data) {
            const rowCount = $('#shift-table-body tr').length;
            if(rowCount >= 0) {
                $('#shift-table').show()
            }

            if($('#change-status:checked').length > 0) {
                statusId =  $('#shift-status  :selected').val()
                statusName = $('#shift-status  :selected').text()
            }

            if($('#change-warehouse-area:checked').length > 0) {
                warehouseName = $('#shift-modal-group-to-area').val()
            }

            groupList = data
            const row = `
                <tr id="shift-table-row-${data.id}">
                    <td>${data.barcode}</td>
                    <td>${data.warehouse_area ? data.warehouse_area.name : "Not in warehouse"}</td>
                    <td>${packageStatusList[data.status]}</td>
                    <td>${warehouseName}</td>
                    <td>${statusName}</td>
                    <td>
                        <div class="btn btn-danger" onclick="removeRow(${data.id}, 0, '${barcode}')">Remove</button>
                    </td>
                    <input type="hidden" name="request_package_id" value="${currentId}" />
                    <input type="hidden" name="package[${data.id}][id]" value="${data.id}"/>
                    <input type="hidden" name="package[${data.id}][status]" value="${statusId}"/>
                    <input type="hidden" name="package[${data.id}][warehouse_area_name]" value="${warehouseName != 'No change' ? warehouseName : null}" />
                </tr>
            `

            $('#shift-table-body').append(row)
            barcodeList.push(barcode)
            totalAdded = totalAdded + 1
        } else {
            createFlash([{type: 'error', content: 'Package code is not correct or compatible'}])
        }

        loading(false);
    }

    async function scanBarcodeWarehouseLabor(barcode) {
        loading()
        if(totalAdded == remainNumber) {
            createFlash([{type: 'error', content: 'Packages are enough'}])
            loading(false);

            return
        }

        const option = $('input[name="select-option"]:checked').val();
        if(option == 'change-detail') {
            const unit = $('#shift-unit').val()

            if(isNaN(unit)) {
                createFlash([{type: 'error', content: 'Please enter number'}])
                $('#shift-unit').addClass('is-invalid')
                loading(false)

                return
            }

            if(unit <= 0) {
                createFlash([{type: 'error', content: 'Please enter greater than 0'}])
                $('#shift-unit').addClass('is-invalid')
                loading(false)

                return
            }

            const receivedUnit = $('#shift-received-unit').val()

            if(isNaN(receivedUnit)) {
                createFlash([{type: 'error', content: 'Please enter number'}])
                $('#shift-received-unit').addClass('is-invalid')
                loading(false)

                return
            }

            if(receivedUnit <= 0) {
                createFlash([{type: 'error', content: 'Please enter greater than 0'}])
                $('#shift-received-unit').addClass('is-invalid')
                loading(false)

                return
            }

            if(parseInt(receivedUnit) > parseInt(unit)) {
                createFlash([{type: 'error', content: 'Received number must not be greater than package unit'}])
                $('#shift-received-unit').addClass('is-invalid')
                loading(false)

                return
            }
        }

        const data = await getPackage(barcode)

        if(data) {
            const rowCount = $('#shift-table-body tr').length;
            if(rowCount >= 0) {
                $('#shift-table').show()
            }

            let weight = data.weight
            let width = data.width
            let height = data.height
            let length = data.length
            let packageUnit = data.unit_number
            let unitReceived = data.received_unit_number
            let warehouseArea = data.warehouse_area && data.warehouse_area.name ? data.warehouse_area.name : ''
            let checked = false

            if(option == 'change-detail') {
                weight = $('#shift-weight').val() > 0 ? $('#shift-weight').val() : weight
                width = $('#shift-width').val() > 0 ? $('#shift-width').val() : width
                height = $('#shift-height').val() > 0 ? $('#shift-height').val() : height
                length = $('#shift-length').val() > 0 ? $('#shift-length').val() : length
                packageUnit = $('#shift-unit').val() > 0 ? $('#shift-unit').val() : packageUnit
                unitReceived = $('#shift-received-unit').val() > 0 ? $('#shift-received-unit').val() : unitReceived
            }

            if(option == 'delete-package') {
                checked = true
            }

            groupList = data
            const row = `
                <tr id="shift-table-row-${data.id}">
                    <td>${data.barcode}</td>
                    <td>
                       ${packageStatusList[data.status]}
                    </td>
                    <td>
                        <input type="text" class="form-control w-100" name="package[${data.id}][unit_number]" value="${parseInt(packageUnit)}" />
                    </td>
                    <td>
                        <input type="text" class="form-control w-100" name="package[${data.id}][received_unit_number]" value="${parseInt(unitReceived)}" />
                    </td>
                    <td>
                        <input type="text" class="form-control w-100" name="package[${data.id}][weight]" value="${weight ? parseFloat(weight) : ''}" />
                    </td>
                    <td>
                        <input type="text" class="form-control w-100" name="package[${data.id}][width]" value="${width ? parseFloat(width) : ''}" />
                    </td>
                    <td>
                        <input type="text" class="form-control w-100" name="package[${data.id}][height]" value="${height ? parseFloat(height) : ''}" />
                    </td>
                    <td>
                        <input type="text" class="form-control w-100" name="package[${data.id}][length]" value="${length ? parseFloat(length) : ''}" />
                    </td>
                    <td>
                        ${warehouseArea}
                    </td>
                    <td>
                        <input type="checkbox" name="package[${data.id}][delete]" ${checked ? 'checked' : ''} />
                    </td>
                    <td>
                        <div class="btn btn-danger" onclick="removeRow(${data.id}, 0, '${barcode}')">Remove</button>
                    </td>
                    <input type="hidden" name="request_package_id" value="${currentId}" />
                    <input type="hidden" name="package[${data.id}][id]" value="${data.id}"/>
                    <input type="hidden" name="package[${data.id}][warehouse]" value="${warehouseArea}"/>
                </tr>
            `

            $('#shift-table-body').append(row)
            barcodeList.push(barcode)
            totalAdded = totalAdded +

            $('#shift-unit').removeClass('is-invalid')
            $('#shift-received-unit').removeClass('is-invalid')
        } else {
            createFlash([{type: 'error', content: 'Package code is not correct or compatible'}])
        }

        loading(false);
    }

    function addPackageWarehouseLabor() {
        loading()
        const packageNumber = $('#shift-package-number').val()

        if(isNaN(packageNumber)) {
            createFlash([{type: 'error', content: 'Please enter number'}])
            $('#shift-package-number').addClass('is-invalid')
            loading(false)

            return
        }

        if(packageNumber <= 0) {
            createFlash([{type: 'error', content: 'Please enter greater than 0'}])
            $('#shift-package-number').addClass('is-invalid')
            loading(false)

            return
        }

        const unit = $('#shift-unit').val()

        if(isNaN(unit)) {
            createFlash([{type: 'error', content: 'Please enter number'}])
            $('#shift-unit').addClass('is-invalid')
            loading(false)

            return
        }

        if(unit <= 0) {
            createFlash([{type: 'error', content: 'Please enter greater than 0'}])
            $('#shift-unit').addClass('is-invalid')
            loading(false)

            return
        }

        const receivedUnit = $('#shift-received-unit').val()

        if(isNaN(receivedUnit)) {
            createFlash([{type: 'error', content: 'Please enter number'}])
            $('#shift-received-unit').addClass('is-invalid')
            loading(false)

            return
        }

        if(receivedUnit <= 0) {
            createFlash([{type: 'error', content: 'Please enter greater than 0'}])
            $('#shift-received-unit').addClass('is-invalid')
            loading(false)

            return
        }

        if(parseInt(receivedUnit) > parseInt(unit)) {
            createFlash([{type: 'error', content: 'Received number must not be greater than package unit'}])
            $('#shift-received-unit').addClass('is-invalid')
            loading(false)

            return
        }

        const weight = $('#shift-weight').val()
        const length = $('#shift-length').val()
        const width = $('#shift-width').val()
        const height = $('#shift-height').val()

        if(weight && isNaN(weight)) {
            createFlash([{type: 'error', content: 'Please enter number'}])
            $('#shift-weight').addClass('is-invalid')
            loading(false)

            return
        }

        if(weight && weight <= 0) {
            createFlash([{type: 'error', content: 'Please enter greater than 0'}])
            $('#shift-weight').addClass('is-invalid')
            loading(false)

            return
        }

        if(length && isNaN(length)) {
            createFlash([{type: 'error', content: 'Please enter number'}])
            $('#shift-length').addClass('is-invalid')
            loading(false)

            return
        }

        if(length && length <= 0) {
            createFlash([{type: 'error', content: 'Please enter greater than 0'}])
            $('#shift-length').addClass('is-invalid')
            loading(false)

            return
        }

        if(width && isNaN(width)) {
            createFlash([{type: 'error', content: 'Please enter number'}])
            $('#shift-width').addClass('is-invalid')
            loading(false)

            return
        }

        if(width && width <= 0) {
            createFlash([{type: 'error', content: 'Please enter greater than 0'}])
            $('#shift-width').addClass('is-invalid')
            loading(false)

            return
        }

        if(height && isNaN(height)) {
            createFlash([{type: 'error', content: 'Please enter number'}])
            $('#shift-height').addClass('is-invalid')
            loading(false)

            return
        }

        if(height && height <= 0) {
            createFlash([{type: 'error', content: 'Please enter greater than 0'}])
            $('#shift-height').addClass('is-invalid')
            loading(false)

            return
        }


        const areaName = $('#shift-warehouse').val()
        let areaInfo = null;

        if(areaName) {
            areaInfo = warehouseAreas.find(area =>{
                return area.name == areaName
            });

            if(!areaInfo) {
                $('#shift-warehouse').addClass('is-invalid')
                createFlash([{type: 'error', content: 'Warehouse Area not existed'}])
                loading(false)

                return
            }

            if(areaInfo.is_full == 1) {
                $('#shift-warehouse').addClass('is-invalid')
                createFlash([{type: 'error', content: 'This Area is full'}])
                loading(false)

                return
            }
        }
        const rowCount = $('#shift-table-body tr').length;
        if(rowCount >= 0) {
            $('#shift-table').show()
        }

        const status =  $('#shift-status  :selected').val()
        const statusName = $('#shift-status  :selected').text()
        const element = $('#shift-table-body')

        for(let i = 0; i < packageNumber; i++) {
            index = index + 1
            const row = `
            <tr id="shift-table-row-new${index}">
                    <td>New package</td>
                    <td>
                        ${statusName}
                    </td>
                    <td>
                        <input type="text" class="form-control w-100" name="package[new${index}][unit_number]" value="${parseInt(unit)}" />
                    </td>
                    <td>
                        <input type="text" class="form-control w-100" name="package[new${index}][received_unit_number]" value="${parseInt(receivedUnit)}" />
                    </td>
                    <td>
                        <input type="text" class="form-control w-100" name="package[new${index}][weight]" value="${weight ? parseFloat(weight) : ''}" />
                    </td>
                    <td>
                        <input type="text" class="form-control w-100" name="package[new${index}][width]" value="${width ? parseFloat(width) : ''}" />
                    </td>
                    <td>
                        <input type="text" class="form-control w-100" name="package[new${index}][height]" value="${height ? parseFloat(height) : ''}" />
                    </td>
                    <td>
                        <input type="text" class="form-control w-100" name="package[new${index}][length]" value="${length ? parseFloat(length) : ''}" />
                    </td>
                    <td>
                        ${areaName}
                    </td>
                    <td>

                    </td>
                    <td>
                        <div class="btn btn-danger" onclick="removeRow('new${index}', 1, '')">Remove</button>
                    </td>
                    <input type="hidden" name="request_package_id" value="${currentId}" />
                    <input type="hidden" name="package_group_id" value="${currentGroup}" />
                    <input type="hidden" name="package[new${index}][warehouse]" value="${areaName}"/>
                    <input type="hidden" name="package[new${index}][status]" value="${status}"/>
                    <input type="hidden" name="package[new${index}][warehouse_area_name]" value="${areaName ? areaName : ''}" />
                </tr>
            `

            element.append(row)
        }

        $('#shift-unit').removeClass('is-invalid')
        $('#shift-received-unit').removeClass('is-invalid')
        $('#shift-warehouse').removeClass('is-invalid')
        $('#shift-weight').removeClass('is-invalid')
        $('#shift-package-number').removeClass('is-invalid')
        $('#shift-weight').removeClass('is-invalid')
        $('#shift-length').removeClass('is-invalid')
        $('#shift-width').removeClass('is-invalid')
        $('#shift-height').removeClass('is-invalid')

        $('#shift-package-number').val(0)
        $('#shift-warehouse').val('')
        $('#shift-weight').val(null)
        $('#shift-length').val(null)
        $('#shift-width').val(null)
        $('#shift-height').val(null)
        $("#shift-status").val(0);

        loading(false)
    }

    async function scanCheckingPackage(barcode) {
        const data = await checkPackage(barcode)

        if(data) {
            createFlash([{content: 'Package code is useable'}])
        } else {
            createFlash([{type: 'error', content: 'Package code is not correct or compatible'}])
        }
    }

    async function scanBarcodeOutbound(barcode) {
        loading()
        if(totalAdded == remainNumber) {
            createFlash([{type: 'error', content: 'Packages are enough'}])
            loading(false);

            return
        }

        const data = await getPackage(barcode)

        if(data) {
            const rowCount = $('#shift-table-body tr').length;
            if(rowCount >= 0) {
                $('#shift-table').show()
            }

            groupList = data
            const row = `
                <tr id="shift-table-row-${data.id}">
                    <td>${data.barcode}</td>
                    <td>${data.warehouse_area ? data.warehouse_area.name : "Not in warehouse"}</td>
                    <td>${packageStatusList[data.status]}</td>
                    <td>
                        <div class="btn btn-danger" onclick="removeRow(${data.id}, 0, '${barcode}')">Remove</button>
                    </td>
                    <input type="hidden" name="request_package_id" value="${currentId}" />
                    <input type="hidden" name="package[${data.id}][id]" value="${data.id}"/>
                </tr>
            `

            $('#shift-table-body').append(row)
            barcodeList.push(barcode)
            totalAdded = totalAdded + 1
        } else {
            createFlash([{type: 'error', content: 'Package code is not correct or compatible'}])
        }

        loading(false);
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
@endpush
