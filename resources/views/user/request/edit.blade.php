@extends('layouts.user')

@section('breadcrumb')
    @include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('dashboard')
        ],
        [
            'text' => 'Request',
            'url' => route('requests.index')
        ],
        [
            'text' => $userRequest['id'],
            'url' => route('requests.show', ['userRequestId' => $userRequest->id])
        ],
        [
            'text' => 'Edit'
        ]
    ]
])
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
    // $oldRequestTypeId = old('m_request_type_id');
    // $hasOldRequestTypeId = $oldRequestTypeId != null;

    // $sizeOptionRequests = ['add package', 'removal', 'return'];
    // $hideSizeOption = true;
    // if (
    //     (!$hasOldRequestTypeId && in_array($type, $sizeOptionRequests))
    //         || ($hasOldRequestTypeId && isset($requestTypes[$oldRequestTypeId]) && in_array($requestTypes[$oldRequestTypeId], $sizeOptionRequests))
    // ) {
    //     $hideSizeOption = false;
    // }

    // $hideAddPackage = true;
    // if (
    //     (!$hasOldRequestTypeId && $type == "add package")
    //         || ($hasOldRequestTypeId && isset($requestTypes[$oldRequestTypeId]) && $requestTypes[$oldRequestTypeId] == "add package")
    // ) {
    //     $hideAddPackage = false;
    // }

    // $hideRemoval = true;
    // if (
    //     (!$hasOldRequestTypeId && $type == "removal")
    //         || ($hasOldRequestTypeId && isset($requestTypes[$oldRequestTypeId]) && $requestTypes[$oldRequestTypeId] == "removal")
    // ) {
    //     $hideRemoval = false;
    // }

    // $hideReturn = true;
    // if (
    //     (!$hasOldRequestTypeId && $type == "return")
    //         || ($hasOldRequestTypeId && isset($requestTypes[$oldRequestTypeId]) && $requestTypes[$oldRequestTypeId] == "return")
    // ) {
    //     $hideReturn = false;
    // }

    // $unitOptionRequests = ['relabel', 'repack', 'outbound', 'warehouse labor'];
    // $hideUnit = true;
    // if (
    //     (!$hasOldRequestTypeId && in_array($type, $unitOptionRequests))
    //         || ($hasOldRequestTypeId && isset($requestTypes[$oldRequestTypeId]) && in_array($requestTypes[$oldRequestTypeId], $unitOptionRequests))
    // ) {
    //     $hideUnit = false;
    // }

@endphp

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-header">
            <h2 class="mb-0">Edit Request</h2>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('requests.update') }}" class="form-horizontal" role="form" enctype="multipart/form-data">
                @method('put')
                @csrf

                {{-- Request Type --}}
                <div class="form-group search-form-group">
                    <label for="m_request_type_id" class="search-label col-form-label"><b>{{ __('Type') }}</b></label>
                    <div class="search-input search-radio">
                        {{ ucfirst($userRequest->mRequestType->name) }}

                        {{-- <select id="m_request_type_id" name="m_request_type_id" class="form-control w-75">
                            <option selected disabled></option>
                            @foreach ($requestTypes as $id => $type)
                                <option value="{{ $id }}"
                                    @if ((old('m_request_type_id') != null && old('m_request_type_id') == $id)
                                            || (old('m_request_type_id') == null && $userRequest->m_request_type_id == $id))
                                        selected="selected"
                                    @endif
                                >{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('m_request_type_id'))
                            <p class="text-danger mb-0">
                                {{ $errors->first('m_request_type_id') }}
                            </p>
                        @endif --}}
                    </div>
                </div>


                {{-- Note --}}
                <div class="form-group search-form-group">
                    <label for="note" class="col-form-label search-label"><b>{{ __('Note') }}</b></label>
                    <div class="search-input search-radio">
                        {{ $userRequest->note }}
                        {{-- <textarea name="note" id="note" class="form-control">{{ old('note', $userRequest->note) }}</textarea> --}}
                    </div>
                </div>


                {{-- Size option --}}
                <div id="size_radio_option"
                    {{-- @if($hideSizeOption)
                        style="display: none;"
                    @endif --}}
                >
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Size Type') }}</b></label>
                        <div class="search-input search-radio">
                            {{ App\Models\UserRequest::$sizeName[App\Models\UserRequest::SIZE_INCH] }}
                        </div>
                        {{-- <div class="search-input search-radio">
                            @foreach (App\Models\UserRequest::$sizeName as $value => $name)
                                <div class="form-check d-inline-flex mr-3">
                                    <input class="form-check-input"
                                        {{ old('size_type', App\Models\UserRequest::SIZE_INCH) == $value ? 'checked' : '' }}
                                        type="radio" name="size_type" id="{{ 'size_type' . $value }}" value="{{ $value }}"
                                    >
                                    <label class="form-check-label" for="{{ 'size_type' . $value }}">{{ $name }}</label>
                                </div>
                            @endforeach

                            @if ($errors->has('size_type'))
                                <p class="text-danger mb-0">
                                    {{ $errors->first('size_type') }}
                                </p>
                            @endif
                        </div> --}}
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Weight Type') }}</b></label>
                        <div class="search-input search-radio">
                            {{ App\Models\UserRequest::$weightName[App\Models\UserRequest::WEIGHT_POUND] }}
                        </div>
                        {{-- <div class="search-input search-radio">
                            @foreach (App\Models\UserRequest::$weightName as $value => $name)
                                <div class="form-check d-inline-flex mr-3">
                                    <input class="form-check-input"
                                        @if (old('weight_type') != null)
                                            {{ old('weight_type') == $value ? 'checked' : '' }}
                                        @else
                                            {{ $value == App\Models\UserRequest::WEIGHT_POUND ? 'checked' : '' }}
                                        @endif
                                        type="radio" name="weight_type" id="{{ 'weight_type' . $value }}" value="{{ $value }}"
                                    >
                                    <label class="form-check-label" for="{{ 'weight_type' . $value }}">{{ $name }}</label>
                                </div>
                            @endforeach
                            @if ($errors->has('weight_type'))
                                <p class="text-danger mb-0">
                                    {{ $errors->first('weight_type') }}
                                </p>
                            @endif
                        </div> --}}
                    </div>
                </div>

                <div class="amb-32 apy-8 addition-form">
                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <input type="hidden"
                                    name="user_request_id"
                                    value="{{ $userRequestId }}"
                            >
                            @if ($errors->has('user_request_id'))
                                <p class="text-danger mb-0">
                                    {{ $errors->first('user_request_id') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Add package option --}}
                <div id="new_package_option"
                    {{-- @if($hideAddPackage)
                        style="display: none;"
                    @endif --}}
                >
                    {{-- Content --}}
                    <div id="add_package_option_content">
                        {{-- Old input --}}
                        @foreach ($userRequest->requestPackageGroups as $rpgId => $rpg)
                            @if ($rpg->packageGroup == null)
                                @continue
                            @endif

                            <div class="amb-32 apy-8 addition-form" id="{{ 'package_group_'.$rpg->id }}">
                                <div id="{{ 'package_group_form_'.$rpg->id }}">
                                    <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                                        <h3 class="amb-4">
                                            {{ __('Package Group') }} : {{ $rpg->packageGroup->name }}
                                        </h3>
                                        {{-- <button class="btn btn-danger btn-sm apx-16" onclick="deleteGroup(`{{ 'package_group_' . $rpgId }}`)">
                                            <i class="fa fa-trash font-14"></i>
                                        </button> --}}
                                    </div>

                                    <input type="hidden"
                                        name="{{ 'package_group[' . $rpg->id . '][rpgId]' }}"
                                        value="{{ $rpg->id }}"
                                    >
                                    @if ($errors->has('package_group.' . $rpg->id . '.rpgId'))
                                        <p class="text-danger mb-0">
                                            {{ $errors->first('package_group.' . $rpg->id . '.rpgId') }}
                                        </p>
                                    @endif

                                    {{-- Package Group Select --}}
                                    <div class="row amb-20 amx-n4">
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                            <b>{{ __('Package Group Name') }}</b>
                                            <div class="form-control">
                                                {{ $rpg->packageGroup->name ?? '' }}
                                            </div>
                                        </div>

                                        {{-- <div class="col-12 col-md-5 col-xl-4 apx-4">
                                            <select id="{{ 'package_group_select_' . $rpgId }}" name="{{ 'package_group['. $rpgId . '][id]' }}" class="form-control pg-select">
                                                <option selected>{{ __('Select Package Group (*)') }}</option>
                                                @foreach ($packageGroups as $packageGroup)
                                                    <option value="{{ $packageGroup['id'] }}"
                                                        data-width="{{ $packageGroup['unit_width'] }}" data-weight="{{ $packageGroup['unit_weight'] }}"
                                                        data-height="{{ $packageGroup['unit_height'] }}" data-length="{{ $packageGroup['unit_length'] }}"
                                                        @if ($rpg->package_group_id == $packageGroup['id'])
                                                            selected="selected"
                                                            @php
                                                                $dataPackageGroup[$groupId] = $packageGroup;
                                                            @endphp
                                                        @endif
                                                    >{{ $packageGroup['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div> --}}
                                    </div>

                                    {{-- Package Group Info --}}
                                    <div id="{{ 'package_group_select_' . $rpg->id . '_content' }}">
                                        <div class="row amb-20 amx-n4">
                                            <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                <b>{{ __('Package Group Unit Width') }}</b>
                                                <div class="form-control">
                                                    {{ $rpg->packageGroup->unit_width ?? '' }}
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                <b>{{ __('Package Group Unit Weight') }}</b>
                                                <div class="form-control">
                                                    {{ $rpg->packageGroup->unit_weight ?? '' }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row amb-20 amx-n4">
                                            <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                <b>{{ __('Package Group Unit Height') }}</b>
                                                <div class="form-control">
                                                    {{ $rpg->packageGroup->unit_height ?? '' }}
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                <b>{{ __('Package Group Unit Length') }}</b>
                                                <div class="form-control">
                                                    {{ $rpg->packageGroup->unit_length ?? '' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Package Info --}}
                                    <div class="amb-20">
                                        <b>{{ __('Package information') }}</b>
                                        <div id="{{ 'package_info_'. $rpg->id }}">
                                            @foreach ($rpg->requestPackages as $k => $v)
                                                <div id="{{ 'package_info_' . $rpg->id . '_' . $v->id }}" class="row amx-n4 amb-8">
                                                    <div class="col-10 col-xl-8 apx-4">
                                                        <b>{{ __('Package') }}</b>
                                                    </div>

                                                    <input type="hidden"
                                                        name="{{ 'package_group[' . $rpg->id . '][info][' . $v->id . '][rpId]' }}"
                                                        value="{{ $v['id'] }}"
                                                    >
                                                    @if ($errors->has('package_group.' . $rpg->id . '.info.' . $v->id . '.rpId'))
                                                        <div class="col-10 col-xl-8 apx-4">
                                                            <p class="text-danger mb-0">
                                                                {{ $errors->first('package_group.' . $rpg->id . '.info.' . $v->id . '.rpId') }}
                                                            </p>
                                                        </div>
                                                    @endif

                                                    <div class="col-10 col-xl-8 apx-4">
                                                        <div class="row amx-n4">
                                                            <div class="col-12 col-md-6 apx-4 amb-8">
                                                                <input type="number" class="form-control"
                                                                    name="{{ 'package_group[' . $rpg->id . '][info][' . $v->id. '][package_width]' }}"
                                                                    value="{{ old('package_group')[$rpg->id]['info'][$v->id]['package_width'] ?? $v['width'] ?? '' }}" placeholder="Package Width" step="any" min="0" />
                                                            </div>
                                                            <div class="col-12 col-md-6 apx-4 amb-8">
                                                                <input type="number" class="form-control"
                                                                    name="{{ 'package_group[' . $rpg->id . '][info][' . $v->id . '][package_weight]' }}"
                                                                    value="{{ old('package_group')[$rpg->id]['info'][$v->id]['package_weight'] ?? $v['weight'] ?? '' }}" placeholder="Package Weight" step="any" min="0" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if ($errors->has('package_group.' . $rpg->id . '.info.' . $v->id . '.package_width'))
                                                        <div class="col-10 col-xl-8 apx-4">
                                                            <p class="text-danger mb-0">
                                                                {{ $errors->first('package_group.' . $rpg->id . '.info.' . $v->id . '.package_width') }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                    @if ($errors->has('package_group.' . $rpg->id . '.info.' . $v->id . '.package_weight'))
                                                        <div class="col-10 col-xl-8 apx-4">
                                                            <p class="text-danger mb-0">
                                                                {{ $errors->first('package_group.' . $rpg->id . '.info.' . $v->id . '.package_weight') }}
                                                            </p>
                                                        </div>
                                                    @endif

                                                    <div class="col-10 col-xl-8 apx-4">
                                                        <div class="row amx-n4">
                                                            <div class="col-12 col-md-6 apx-4 amb-8">
                                                                <input type="number" class="form-control"
                                                                    name="{{ 'package_group[' . $rpg->id . '][info][' . $v->id . '][package_height]' }}"
                                                                    value="{{ old('package_group')[$rpg->id]['info'][$v->id]['package_height'] ?? $v['height'] ?? '' }}" placeholder="Package Height" step="any" min="0" />
                                                            </div>
                                                            <div class="col-12 col-md-6 apx-4 amb-8">
                                                                <input type="number" class="form-control"
                                                                    name="{{ 'package_group[' . $rpg->id . '][info][' . $v->id . '][package_length]' }}"
                                                                    value="{{ old('package_group')[$rpg->id]['info'][$v->id]['package_length'] ?? $v['length'] ?? '' }}" placeholder="Package Length" step="any" min="0" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if ($errors->has('package_group.' . $rpg->id . '.info.' . $v->id . '.package_height'))
                                                        <div class="col-10 col-xl-8 apx-4">
                                                            <p class="text-danger mb-0">
                                                                {{ $errors->first('package_group.' . $rpg->id . '.info.' . $v->id . '.package_height') }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                    @if ($errors->has('package_group.' . $rpg->id . '.info.' . $v->id . '.package_length'))
                                                        <div class="col-10 col-xl-8 apx-4">
                                                            <p class="text-danger mb-0">
                                                                {{ $errors->first('package_group.' . $rpg->id . '.info.' . $v->id . '.package_length') }}
                                                            </p>
                                                        </div>
                                                    @endif

                                                    <div class="col-10 col-xl-8 apx-4">
                                                        <div class="row amx-n4">
                                                            <div class="col-12 col-md-6 apx-4 amb-8">
                                                                <input type="number" class="form-control"
                                                                    name="{{ 'package_group[' . $rpg->id . '][info][' . $v->id . '][unit_number]' }}"
                                                                    value="{{ old('package_group')[$rpg->id]['info'][$v->id]['unit_number'] ?? $v['unit_number'] ?? '' }}" placeholder="Number Unit per Package (*)" step="any" min="0" />
                                                            </div>
                                                            <div class="col-12 col-md-6 apx-4 amb-8">
                                                                <input type="number" class="form-control"
                                                                    name="{{ 'package_group[' . $rpg->id . '][info][' . $v->id . '][package_number]' }}"
                                                                    value="{{ old('package_group')[$rpg->id]['info'][$v->id]['package_number'] ?? $v['package_number'] ?? '' }}" placeholder="Number package (*)" step="any" min="0" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if ($errors->has('package_group.' . $rpg->id . '.info.' . $v->id . '.unit_number'))
                                                        <div class="col-10 col-xl-8 apx-4">
                                                            <p class="text-danger mb-0">
                                                                {{ $errors->first('package_group.' . $rpg->id . '.info.' . $v->id . '.unit_number') }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                    @if ($errors->has('package_group.' . $rpg->id . '.info.' . $v->id . '.package_number'))
                                                        <div class="col-10 col-xl-8 apx-4">
                                                            <p class="text-danger mb-0">
                                                                {{ $errors->first('package_group.' . $rpg->id . '.info.' . $v->id . '.package_number') }}
                                                            </p>
                                                        </div>
                                                    @endif

                                                    {{-- <div class="col d-flex align-items-center apx-12 amb-8">
                                                        <i class="fa fa-close atext-gray-500 font-20 pointer line-height-1" onclick="deletePackageInfo(this)"></i>
                                                    </div> --}}

                                                    <div  class="col-10 col-xl-8 apx-4"><hr></div>
                                                </div>
                                            @endforeach
                                        </div>

                                        {{-- <button type="button" class="btn btn-secondary apx-24 add-btn" onclick="addPackageInfo({{ $rpgId }})">
                                            {{ __('Add') }}
                                        </button> --}}
                                    </div>

                                    {{-- Tracking URL --}}
                                    @if (count($rpg->requestPackageTrackings))
                                        <div class="amb-20">
                                            <b>{{ __('Tracking urls') }}</b>
                                            <div id="{{ 'package_group_track_' . $rpg->id}}">
                                                @foreach ($rpg->requestPackageTrackings as $k => $v)
                                                    <div class="row amb-8 amx-n4">
                                                        <div class="col-10 col-xl-8 apx-4">
                                                            {{ $v->tracking_url ?? '' }}

                                                            {{-- <input type="text" class="form-control" name="{{ 'package_group[' . $rpgId . '][tracking_url][]' }}"
                                                                placeholder="Tracking url" value="{{ $v->tracking_url ?? '' }}"
                                                            /> --}}
                                                        </div>

                                                        {{-- <div class="col-10 col-xl-8 apx-4">
                                                            <input type="text" class="form-control" name="{{ 'package_group[' . $rpgId . '][tracking_url][]' }}"
                                                                placeholder="Tracking url" value="{{ $v->tracking_url ?? '' }}"
                                                            />
                                                        </div>
                                                        <div class="d-flex align-items-center apx-12">
                                                            <i class="fa fa-close atext-gray-500 font-20 pointer" onclick="deletePackage(this)"></i>
                                                        </div> --}}
                                                    </div>
                                                @endforeach
                                            </div>

                                            {{-- <button type="button" class="btn btn-secondary apx-24 add-btn" onclick="addTrack('package_group', {{ $rpgId }})">
                                                {{ __('Add') }}
                                            </button> --}}
                                        </div>
                                    @endif

                                    {{-- <div class="row amb-20 amx-n4">
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                            <input type="file" accept="image/*" hidden id="{{ 'file_unit_upload_pg_' . $rpgId }}" name="{{ 'package_group[' . $rpgId . '][file_unit][]' }}" multiple
                                                class="btn-primary form-control" onchange="displayFileName(`{{ 'file_unit_upload_pg_' . $rpgId }}`, `{{ 'file_unit_selected_pg_' . $rpgId }}`)">
                                            <div class="btn btn-info w-100" onclick="uploadImage(`{{ 'file_unit_upload_pg_' . $rpgId }}`)"> Upload unit image</div>
                                            <span id="{{ 'file_unit_selected_pg_' . $rpgId }}">No file selected</span>
                                            <div id="{{ 'file_unit_error_pg_' . $rpgId }}" class="text-danger mb-0"></div>
                                        </div>
                                    </div> --}}

                                    {{-- Image URL --}}
                                    @if (count($rpg->requestPackageImages))
                                        <div id="{{ 'old_file_unit_selected_pg_' . $rpg->id }}">
                                            <b>{{ __('Old Unit Images') }}</b>
                                            @foreach ($rpg->requestPackageImages as $k => $image)
                                                <div class="row amb-8 amx-n4">
                                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                        <img  width="177" height="110" src="{{ asset($image->image_url) }}" alt="Package image" class="img-fluid">
                                                    </div>
                                                    {{-- <div class="d-flex align-items-center apx-12">
                                                        <i class="fa fa-close atext-gray-500 font-20 pointer" onclick="deletePackage(this)"></i>
                                                    </div>

                                                        Keep Old Image
                                                    <input type="hidden" value="{{ $image->id }}"" name="{{ 'package_group[' . $rpg->id . '][image][]' }}"> --}}
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Action Button --}}
                    {{-- <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Group Action') }}</b></label>
                        <div class="search-input">
                            <button type="button" class="btn btn-secondary apx-16 amr-8" onclick="addGroup()">
                                {{ __('Add Group') }}
                            </button>
                            <button type="button" class="btn btn-success apx-16 amr-8" onclick="addNewGroup()">
                                {{ __('Create New Group') }}
                            </button>
                            @if ($errors->has('package_group'))
                                <p class="text-danger mb-0">
                                    {{ $errors->first('package_group') }}
                                </p>
                            @endif
                            @if ($errors->has('new_package_group'))
                                <p class="text-danger mb-0">
                                    {{ $errors->first('new_package_group') }}
                                </p>
                            @endif
                        </div>
                    </div> --}}
                </div>



                {{-- Removal option --}}
                {{-- <div id="removal_option"
                    @if($hideRemoval)
                        style="display: none;"
                    @endif
                >
                    <div id="removal_option_content">
                        @if (old('removal_group') != null || old('removal_new_group') != null)
                            @if (old('removal_group') != null)
                                @foreach (old('removal_group') as $groupId => $group)
                                    <div class="amb-32 apy-8 addition-form" id="{{ 'removal_group_'.$groupId }}">
                                        <div id="{{ 'removal_group_form_'.$groupId }}">
                                            <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                                                <h3 class="amb-4">{{ __('Removal Group') }}</h3>
                                                <button class="btn btn-danger btn-sm apx-16" onclick="deleteGroup(`{{ 'removal_group_' . $groupId }}`)">
                                                    <i class="fa fa-trash font-14"></i>
                                                </button>
                                            </div>

                                            <input type="hidden"
                                                name="{{ 'removal_group[' . $groupId . '][rpgId]' }}"
                                                value="{{ $group['rpgId'] ?? null }}"
                                            >
                                            @if ($errors->has('removal_group.' . $groupId . '.rpgId'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('removal_group.' . $groupId . '.rpgId') }}
                                                </p>
                                            @endif

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                    <select id="{{ 'removal_group_select_' . $groupId }}" name="{{ 'removal_group['. $groupId . '][id]' }}" class="form-control pg-select">
                                                        <option selected>{{ __('Select Package Group (*)') }}</option>
                                                        @foreach ($packageGroups as $packageGroup)
                                                            <option value="{{ $packageGroup['id'] }}"
                                                                data-width="{{ $packageGroup['unit_width'] }}" data-weight="{{ $packageGroup['unit_weight'] }}"
                                                                data-height="{{ $packageGroup['unit_height'] }}" data-length="{{ $packageGroup['unit_length'] }}"
                                                                @if (isset($group['id']) && $group['id'] == $packageGroup['id'])
                                                                    selected="selected"
                                                                    @php
                                                                        $dataRemovalGroup[$groupId] = $packageGroup;
                                                                    @endphp
                                                                @endif
                                                            >{{ $packageGroup['name'] }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            @if ($errors->has('removal_group.' . $groupId . '.id'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('removal_group.' . $groupId . '.id') }}
                                                </p>
                                            @endif

                                            <div id="{{ 'removal_group_select_' . $groupId . '_content' }}">
                                                @if (isset($dataRemovalGroup[$groupId]))
                                                    <div class="row amb-20 amx-n4">
                                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                            <b>{{ __('Package Group Unit Width') }}</b>
                                                            <div class="form-control">
                                                                {{ $dataRemovalGroup[$groupId]['unit_width'] ?? '' }}
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                            <b>{{ __('Package Group Unit Weight') }}</b>
                                                            <div class="form-control">
                                                                {{ $dataRemovalGroup[$groupId]['unit_weight'] ?? '' }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row amb-20 amx-n4">
                                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                            <b>{{ __('Package Group Unit Height') }}</b>
                                                            <div class="form-control">
                                                                {{ $dataRemovalGroup[$groupId]['unit_height'] ?? '' }}
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                            <b>{{ __('Package Group Unit Length') }}</b>
                                                            <div class="form-control">
                                                                {{ $dataRemovalGroup[$groupId]['unit_length'] ?? '' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                    <input type="number" class="form-control" name="{{ 'removal_group[' . $groupId . '][unit_number]' }}"
                                                        placeholder="Number Unit (*)" value="{{ $group['unit_number'] ?? '' }}" step="any" min="0"
                                                    />
                                                </div>
                                            </div>
                                            @if ($errors->has('removal_group.' . $groupId . '.unit_number'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('removal_group.' . $groupId . '.unit_number') }}
                                                </p>
                                            @endif

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                    <input id="{{ 'removal_unit_file_barcode_' . $groupId }}" type="file" hidden
                                                        class="file-select"
                                                        data-id="{{ 'removal_unit_barcode_' . $groupId }}"
                                                        name="{{ 'removal_group['. $groupId . '][file_unit_barcode]' }}" accept="image/*,application/pdf"
                                                    >
                                                    <div id="{{ 'removal_unit_barcode_' . $groupId . '_img' }}">No file selected</div>
                                                    <div class="btn btn-info w-100" onclick="uploadBarcodeImage(`{{ 'removal_unit_file_barcode_' . $groupId }}`)">
                                                        Upload Unit code file
                                                    </div>
                                                    <div id="{{ 'removal_unit_barcode_' . $groupId . '_error' }}" class="text-danger mb-0"></div>
                                                </div>
                                            </div>
                                            @if ($errors->has('removal_group.' . $groupId . '.file_unit_barcode'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('removal_group.' . $groupId . '.file_unit_barcode') }}
                                                </p>
                                            @endif

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                    <input id="{{ 'removal_unit_barcode_' . $groupId }}" type="text" class="form-control w-100"
                                                        name="{{ 'removal_group['. $groupId . '][unit_barcode]' }}" placeholder="Unit QR code" value="{{ $group['unit_barcode'] ?? '' }}">
                                                    <button type="button"  class="btn scan-btn apy-4 group-start-button" data-id="{{ 'removal_unit_barcode_' . $groupId }}" data-toggle="modal" data-target="#scan-modal">
                                                        <i class="fa fa-qrcode font-20"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @if ($errors->has('removal_group.' . $groupId . '.unit_barcode'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('removal_group.' . $groupId . '.unit_barcode') }}
                                                </p>
                                            @endif

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                    <input type="file" accept="image/*" hidden id="{{ 'removal_file_unit_upload_' . $groupId }}" name="{{ 'removal_group[' . $groupId . '][file_unit][]' }}" multiple
                                                        class="btn-primary form-control" onchange="displayFileName(`{{ 'removal_file_unit_upload_' . $groupId }}`, `{{ 'removal_file_unit_selected_' . $groupId }}`)">
                                                    <div class="btn btn-info w-100" onclick="uploadImage(`{{ 'removal_file_unit_upload_' . $groupId }}`)"> Upload unit image</div>
                                                    <span id="{{ 'removal_file_unit_selected_' . $groupId }}">No file selected</span>
                                                    <div id="{{ 'removal_file_unit_error_' . $groupId }}" class="text-danger mb-0"></div>
                                                </div>
                                            </div>
                                            @if ($errors->has('removal_group.' . $groupId . '.file_unit'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('removal_group.' . $groupId . '.file_unit') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            @if (old('removal_new_group') != null)
                                @foreach (old('removal_new_group') as $groupId => $group)
                                    <div class="amb-32 apy-8 addition-form" id="{{ 'removal_new_group_'.$groupId }}">
                                        <div id="{{ 'removal_new_group_form_'.$groupId }}">
                                            <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                                                <h3 class="amb-4">{{ __('New Removal Group') }}</h3>
                                                <button class="btn btn-danger btn-sm apx-16" onclick="deleteGroup(`{{ 'removal_new_group_' . $groupId }}`)">
                                                    <i class="fa fa-trash font-14"></i>
                                                </button>
                                            </div>

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                    <input type="text" class="form-control" name="{{ 'removal_new_group[' . $groupId . '][name]' }}"
                                                        placeholder="Package Group Name (*)" value="{{ $group['name'] ?? '' }}"
                                                    />
                                                </div>
                                            </div>
                                            @if ($errors->has('removal_new_group.' . $groupId . '.name'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('removal_new_group.' . $groupId . '.name') }}
                                                </p>
                                            @endif

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <b>{{ __('Package Group Unit Width') }}</b>
                                                    <input type="number" class="form-control" name="{{ 'removal_new_group[' . $groupId . '][unit_width]' }}" placeholder="Package Group Unit Width" value="{{ $group['unit_width'] ?? '' }}" step="any" min="0"
                                                    />
                                                    @if ($errors->has('removal_new_group.' . $groupId . '.unit_width'))
                                                        <p class="text-danger mb-0">
                                                            {{ $errors->first('removal_new_group.' . $groupId . '.unit_width') }}
                                                        </p>
                                                    @endif
                                                </div>
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <b>{{ __('Package Group Unit Weight') }}</b>
                                                    <input type="number" class="form-control" name="{{ 'removal_new_group[' . $groupId . '][unit_weight]' }}" placeholder="Package Group Unit Weight" value="{{ $group['unit_weight'] ?? '' }}" step="any" min="0"
                                                    />
                                                    @if ($errors->has('removal_new_group.' . $groupId . '.unit_weight'))
                                                        <p class="text-danger mb-0">
                                                            {{ $errors->first('removal_new_group.' . $groupId . '.unit_weight') }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <b>{{ __('Package Group Unit Height') }}</b>
                                                    <input type="number" class="form-control" name="{{ 'removal_new_group[' . $groupId . '][unit_height]' }}" placeholder="Package Group Unit Height" value="{{ $group['unit_height'] ?? '' }}" step="any" min="0"
                                                    />
                                                    @if ($errors->has('removal_new_group.' . $groupId . '.unit_height'))
                                                        <p class="text-danger mb-0">
                                                            {{ $errors->first('removal_new_group.' . $groupId . '.unit_height') }}
                                                        </p>
                                                    @endif
                                                </div>
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <b>{{ __('Package Group Unit Length') }}</b>
                                                    <input type="number" class="form-control" name="{{ 'removal_new_group[' . $groupId . '][unit_length]' }}" placeholder="Package Group Unit Length" value="{{ $group['unit_length'] ?? '' }}" step="any" min="0"
                                                    />
                                                    @if ($errors->has('removal_new_group.' . $groupId . '.unit_length'))
                                                        <p class="text-danger mb-0">
                                                            {{ $errors->first('removal_new_group.' . $groupId . '.unit_length') }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                    <input type="number" class="form-control" name="{{ 'removal_new_group[' . $groupId . '][unit_number]' }}"
                                                        placeholder="Number Unit (*)" value="{{ $group['unit_number'] ?? '' }}" step="any" min="0"
                                                    />
                                                </div>
                                            </div>
                                            @if ($errors->has('removal_new_group.' . $groupId . '.unit_number'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('removal_new_group.' . $groupId . '.unit_number') }}
                                                </p>
                                            @endif

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                    <input id="{{ 'removal_new_unit_file_barcode_' . $groupId }}" type="file" hidden
                                                        class="file-select"
                                                        data-id="{{ 'removal_new_unit_barcode_' . $groupId }}"
                                                        name="{{ 'removal_new_group['. $groupId . '][file_unit_barcode]' }}" accept="image/*,application/pdf"
                                                    >
                                                    <div id="{{ 'removal_new_unit_barcode_' . $groupId . '_img' }}">No file selected</div>
                                                    <div class="btn btn-info w-100" onclick="uploadBarcodeImage(`{{ 'removal_new_unit_file_barcode_' . $groupId }}`)">
                                                        Upload Unit code file
                                                    </div>
                                                    <div id="{{ 'removal_new_unit_barcode_' . $groupId . '_error' }}" class="text-danger mb-0"></div>
                                                </div>
                                            </div>
                                            @if ($errors->has('removal_new_group.' . $groupId . '.file_unit_barcode'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('removal_new_group.' . $groupId . '.file_unit_barcode') }}
                                                </p>
                                            @endif

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                    <input id="{{ 'removal_new_unit_barcode_' . $groupId }}" type="text" class="form-control w-100"
                                                        name="{{ 'removal_new_group['. $groupId . '][unit_barcode]' }}" placeholder="Unit QR code" value="{{ $group['unit_barcode'] ?? '' }}">
                                                    <button type="button"  class="btn scan-btn apy-4 group-start-button" data-id="{{ 'removal_new_unit_barcode_' . $groupId }}" data-toggle="modal" data-target="#scan-modal">
                                                        <i class="fa fa-qrcode font-20"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @if ($errors->has('removal_new_group.' . $groupId . '.unit_barcode'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('removal_new_group.' . $groupId . '.unit_barcode') }}
                                                </p>
                                            @endif

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                    <input id="{{ 'removal_new_group_file_barcode_' . $groupId }}" type="file" hidden
                                                        class="file-select"
                                                        data-id="{{ 'removal_new_barcode_' . $groupId }}"
                                                        name="{{ 'removal_new_group['. $groupId . '][file_barcode]' }}" accept="image/*,application/pdf"
                                                    >
                                                    <div id="{{ 'removal_new_barcode_' . $groupId . '_img' }}">No file selected</div>
                                                    <div class="btn btn-info w-100" onclick="uploadBarcodeImage(`{{ 'removal_new_group_file_barcode_' . $groupId }}`)">
                                                        Upload New Package Group QR code file
                                                    </div>
                                                    <div id="{{ 'removal_new_barcode_' . $groupId . '_error' }}" class="text-danger mb-0"></div>
                                                </div>
                                            </div>

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                    <input id="{{ 'removal_new_barcode_' . $groupId }}" type="text" class="form-control w-100"
                                                        name="{{ 'removal_new_group['. $groupId . '][barcode]' }}" placeholder="New Package Group QR code" value="{{ $group['barcode'] ?? '' }}">
                                                    <button type="button"  class="btn scan-btn apy-4 group-start-button" data-id="{{ 'removal_new_barcode_' . $groupId }}" data-toggle="modal" data-target="#scan-modal">
                                                        <i class="fa fa-qrcode font-20"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @if ($errors->has('removal_new_group.' . $groupId . '.barcode'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('removal_new_group.' . $groupId . '.barcode') }}
                                                </p>
                                            @endif

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                    <input type="file" accept="image/*" hidden id="{{ 'removal_new_file_unit_upload_' . $groupId }}" name="{{ 'removal_new_group[' . $groupId . '][file_unit][]' }}" multiple
                                                        class="btn-primary form-control" onchange="displayFileName(`{{ 'removal_new_file_unit_upload_' . $groupId }}`, `{{ 'removal_new_file_unit_selected_' . $groupId }}`)">
                                                    <div class="btn btn-info w-100" onclick="uploadImage(`{{ 'removal_new_file_unit_upload_' . $groupId }}`)"> Upload unit image</div>
                                                    <span id="{{ 'removal_new_file_unit_selected_' . $groupId }}">No file selected</span>
                                                    <div id="{{ 'removal_new_file_unit_error_' . $groupId }}" class="text-danger mb-0"></div>
                                                </div>
                                            </div>
                                            @if ($errors->has('removal_new_group.' . $groupId . '.file_unit'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('removal_new_group.' . $groupId . '.file_unit') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @else
                            @foreach ($userRequest->requestPackageGroups as $groupId => $rpg)
                                <div class="amb-32 apy-8 addition-form" id="{{ 'removal_group_'.$groupId }}">
                                    <div id="{{ 'removal_group_form_'.$groupId }}">
                                        <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                                            <h3 class="amb-4">{{ __('Removal Group') }}</h3>
                                            <button class="btn btn-danger btn-sm apx-16" onclick="deleteGroup(`{{ 'removal_group_' . $groupId }}`)">
                                                <i class="fa fa-trash font-14"></i>
                                            </button>
                                        </div>

                                        <input type="hidden"
                                            name="{{ 'removal_group[' . $groupId . '][rpgId]' }}"
                                            value="{{ $rpg->id }}"
                                        >


                                        <div class="row amb-20 amx-n4">
                                            <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                <select id="{{ 'removal_group_select_' . $groupId }}" name="{{ 'removal_group['. $groupId . '][id]' }}" class="form-control pg-select">
                                                    <option selected>{{ __('Select Package Group (*)') }}</option>
                                                    @foreach ($packageGroups as $packageGroup)
                                                        <option value="{{ $packageGroup['id'] }}"
                                                            data-width="{{ $packageGroup['unit_width'] }}" data-weight="{{ $packageGroup['unit_weight'] }}"
                                                            data-height="{{ $packageGroup['unit_height'] }}" data-length="{{ $packageGroup['unit_length'] }}"
                                                            @if ($rpg->package_group_id == $packageGroup['id'])
                                                                selected="selected"
                                                                @php
                                                                    $dataRemovalGroup[$groupId] = $packageGroup;
                                                                @endphp
                                                            @endif
                                                        >{{ $packageGroup['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div id="{{ 'removal_group_select_' . $groupId . '_content' }}">
                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <b>{{ __('Package Group Unit Width') }}</b>
                                                    <div class="form-control">
                                                        {{ $rpg->packageGroup->unit_width ?? '' }}
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <b>{{ __('Package Group Unit Weight') }}</b>
                                                    <div class="form-control">
                                                        {{ $rpg->packageGroup->unit_weight ?? '' }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <b>{{ __('Package Group Unit Height') }}</b>
                                                    <div class="form-control">
                                                        {{ $rpg->packageGroup->unit_height ?? '' }}
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <b>{{ __('Package Group Unit Length') }}</b>
                                                    <div class="form-control">
                                                        {{ $rpg->packageGroup->unit_length ?? '' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row amb-20 amx-n4">
                                            <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                <input type="number" class="form-control" name="{{ 'removal_group[' . $groupId . '][unit_number]' }}"
                                                    placeholder="Number Unit (*)" value="{{ $rpg->requestPackages[0]->unit_number ?? '' }}" step="any" min="0"
                                                />
                                            </div>
                                        </div>

                                        <div class="row amb-20 amx-n4">
                                            <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                <input id="{{ 'removal_unit_file_barcode_' . $groupId }}" type="file" hidden
                                                    class="file-select"
                                                    data-id="{{ 'removal_unit_barcode_' . $groupId }}"
                                                    name="{{ 'removal_group['. $groupId . '][file_unit_barcode]' }}" accept="image/*,application/pdf"
                                                >
                                                <div id="{{ 'removal_unit_barcode_' . $groupId . '_img' }}">No file selected</div>
                                                <div class="btn btn-info w-100" onclick="uploadBarcodeImage(`{{ 'removal_unit_file_barcode_' . $groupId }}`)">
                                                    Upload Unit code file
                                                </div>
                                                <div id="{{ 'removal_unit_barcode_' . $groupId . '_error' }}" class="text-danger mb-0"></div>
                                            </div>
                                        </div>

                                        <div class="row amb-20 amx-n4">
                                            <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                <input id="{{ 'removal_unit_barcode_' . $groupId }}" type="text" class="form-control w-100"
                                                    name="{{ 'removal_group['. $groupId . '][unit_barcode]' }}" placeholder="Unit QR code" value="{{ $rpg['barcode'] ?? '' }}">
                                                <button type="button"  class="btn scan-btn apy-4 group-start-button" data-id="{{ 'removal_unit_barcode_' . $groupId }}" data-toggle="modal" data-target="#scan-modal">
                                                    <i class="fa fa-qrcode font-20"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="row amb-20 amx-n4">
                                            <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                <input type="file" accept="image/*" hidden id="{{ 'removal_file_unit_upload_' . $groupId }}" name="{{ 'removal_group[' . $groupId . '][file_unit][]' }}" multiple
                                                    class="btn-primary form-control" onchange="displayFileName(`{{ 'removal_file_unit_upload_' . $groupId }}`, `{{ 'removal_file_unit_selected_' . $groupId }}`)">
                                                <div class="btn btn-info w-100" onclick="uploadImage(`{{ 'removal_file_unit_upload_' . $groupId }}`)"> Upload unit image</div>
                                                <span id="{{ 'removal_file_unit_selected_' . $groupId }}">No file selected</span>
                                                <div id="{{ 'removal_file_unit_error_' . $groupId }}" class="text-danger mb-0"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Group Action') }}</b></label>
                        <div class="search-input">
                            <button type="button" class="btn btn-secondary apx-16 amr-8" onclick="addRemovalGroup()">
                                {{ __('Add Group') }}
                            </button>
                            <button type="button" class="btn btn-success apx-16 amr-8" onclick="addRemovalNewGroup()">
                                {{ __('Create New Group') }}
                            </button>
                            @if ($errors->has('removal_group'))
                                <p class="text-danger mb-0">
                                    {{ $errors->first('removal_group') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div> --}}

                {{-- Return option --}}
                {{-- <div id="return_option"
                    @if($hideReturn)
                        style="display: none;"
                    @endif
                >
                    <div id="return_option_content">
                        @if (old('return_group') != null || old('return_new_group') != null)
                            @if (old('return_group') != null)
                                @foreach (old('return_group') as $groupId => $group)
                                    <div class="amb-32 apy-8 addition-form" id="{{ 'return_group_'.$groupId }}">
                                        <div id="{{ 'return_group_form_'.$groupId }}">
                                            <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                                                <h3 class="amb-4">{{ __('Return Group') }}</h3>
                                                <button class="btn btn-danger btn-sm apx-16" onclick="deleteGroup(`{{ 'return_group_' . $groupId }}`)">
                                                    <i class="fa fa-trash font-14"></i>
                                                </button>
                                            </div>

                                            <input type="hidden"
                                                name="{{ 'return_group[' . $groupId . '][rpgId]' }}"
                                                value="{{ $group['rpgId'] ?? null }}"
                                            >
                                            @if ($errors->has('return_group.' . $groupId . '.rpgId'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('return_group.' . $groupId . '.rpgId') }}
                                                </p>
                                            @endif

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                    <select id="{{ 'return_group_select_' . $groupId }}" name="{{ 'return_group['. $groupId . '][id]' }}" class="form-control pg-select">
                                                        <option selected>{{ __('Select Package Group (*)') }}</option>
                                                        @foreach ($packageGroups as $packageGroup)
                                                            <option value="{{ $packageGroup['id'] }}"
                                                                data-width="{{ $packageGroup['unit_width'] }}" data-weight="{{ $packageGroup['unit_weight'] }}"
                                                                data-height="{{ $packageGroup['unit_height'] }}" data-length="{{ $packageGroup['unit_length'] }}"
                                                                @if (isset($group['id']) && $group['id'] == $packageGroup['id'])
                                                                    selected="selected"
                                                                    @php
                                                                        $dataReturnGroup[$groupId] = $packageGroup;
                                                                    @endphp
                                                                @endif
                                                            >{{ $packageGroup['name'] }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            @if ($errors->has('return_group.' . $groupId . '.id'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('return_group.' . $groupId . '.id') }}
                                                </p>
                                            @endif

                                            <div id="{{ 'return_group_select_' . $groupId . '_content' }}">
                                                @if (isset($dataReturnGroup[$groupId]))
                                                    <div class="row amb-20 amx-n4">
                                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                            <b>{{ __('Package Group Unit Width') }}</b>
                                                            <div class="form-control">
                                                                {{ $dataReturnGroup[$groupId]['unit_width'] ?? '' }}
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                            <b>{{ __('Package Group Unit Weight') }}</b>
                                                            <div class="form-control">
                                                                {{ $dataReturnGroup[$groupId]['unit_weight'] ?? '' }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row amb-20 amx-n4">
                                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                            <b>{{ __('Package Group Unit Height') }}</b>
                                                            <div class="form-control">
                                                                {{ $dataReturnGroup[$groupId]['unit_height'] ?? '' }}
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                            <b>{{ __('Package Group Unit Length') }}</b>
                                                            <div class="form-control">
                                                                {{ $dataReturnGroup[$groupId]['unit_length'] ?? '' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                    <input type="number" class="form-control" name="{{ 'return_group[' . $groupId . '][unit_number]' }}"
                                                        placeholder="Number Unit (*)" value="{{ $group['unit_number'] ?? '' }}" step="any" min="0"
                                                    />
                                                </div>
                                            </div>
                                            @if ($errors->has('return_group.' . $groupId . '.unit_number'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('return_group.' . $groupId . '.unit_number') }}
                                                </p>
                                            @endif

                                            <div class="amb-20">
                                                <b>{{ __('Tracking urls') }}</b>
                                                <div id="{{ 'return_group_track_' . $groupId}}">
                                                    @if (isset($group['tracking_url']) && $group['tracking_url'] != null)
                                                        @foreach ($group['tracking_url'] as $k => $v)
                                                            <div class="row amb-8 amx-n4">
                                                                <div class="col-10 col-xl-8 apx-4">
                                                                    <input type="text" class="form-control" name="{{ 'return_group[' . $groupId . '][tracking_url][]' }}"
                                                                        placeholder="Tracking url" value="{{ $v ?? '' }}"
                                                                    />
                                                                </div>
                                                                <div class="d-flex align-items-center apx-12">
                                                                    <i class="fa fa-close atext-gray-500 font-20 pointer" onclick="deletePackage(this)"></i>
                                                                </div>
                                                            </div>
                                                            @if ($errors->has('return_group.' . $groupId . '.tracking_url.' . $k))
                                                                <p class="text-danger mb-0">
                                                                    {{ $errors->first('return_group.' . $groupId . '.tracking_url.' . $k) }}
                                                                </p>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </div>

                                                <button type="button" class="btn btn-secondary apx-24 add-btn" onclick="addTrack('return_group', {{ $groupId }})">
                                                    {{ __('Add') }}
                                                </button>
                                            </div>

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                    <input id="{{ 'return_new_unit_file_barcode_' . $groupId }}" type="file" hidden
                                                        class="file-select"
                                                        data-id="{{ 'return_new_unit_barcode_' . $groupId }}"
                                                        name="{{ 'return_group['. $groupId . '][file_unit_barcode]' }}" accept="image/*,application/pdf"
                                                    >
                                                    <div id="{{ 'return_new_unit_barcode_' . $groupId . '_img' }}">No file selected</div>
                                                    <div class="btn btn-info w-100" onclick="uploadBarcodeImage(`{{ 'return_new_unit_file_barcode_' . $groupId }}`)">
                                                        Upload Unit code file
                                                    </div>
                                                    <div id="{{ 'return_new_unit_barcode_' . $groupId . '_error' }}" class="text-danger mb-0"></div>
                                                </div>
                                            </div>
                                            @if ($errors->has('return_group.' . $groupId . '.file_unit_barcode'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('return_group.' . $groupId . '.file_unit_barcode') }}
                                                </p>
                                            @endif

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                    <input id="{{ 'return_new_unit_barcode_' . $groupId }}" type="text" class="form-control w-100"
                                                        name="{{ 'return_group['. $groupId . '][unit_barcode]' }}" placeholder="Unit QR code" value="{{ $group['unit_barcode'] ?? '' }}">
                                                    <button type="button"  class="btn scan-btn apy-4 group-start-button" data-id="{{ 'return_new_unit_barcode_' . $groupId }}" data-toggle="modal" data-target="#scan-modal">
                                                        <i class="fa fa-qrcode font-20"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @if ($errors->has('return_group.' . $groupId . '.unit_barcode'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('return_group.' . $groupId . '.unit_barcode') }}
                                                </p>
                                            @endif

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                    <input type="file" accept="image/*" hidden id="{{ 'return_file_unit_upload_' . $groupId }}" name="{{ 'return_group[' . $groupId . '][file_unit][]' }}" multiple
                                                        class="btn-primary form-control" onchange="displayFileName(`{{ 'return_file_unit_upload_' . $groupId }}`, `{{ 'return_file_unit_selected_' . $groupId }}`)">
                                                    <div class="btn btn-info w-100" onclick="uploadImage(`{{ 'return_file_unit_upload_' . $groupId }}`)"> Upload unit image</div>
                                                    <span id="{{ 'return_file_unit_selected_' . $groupId }}">No file selected</span>
                                                    <div id="{{ 'return_file_unit_error_' . $groupId }}" class="text-danger mb-0"></div>
                                                </div>
                                            </div>
                                            @if ($errors->has('return_group.' . $groupId . '.file_unit'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('return_group.' . $groupId . '.file_unit') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            @if (old('return_new_group') != null)
                                @foreach (old('return_new_group') as $groupId => $group)
                                    <div class="amb-32 apy-8 addition-form" id="{{ 'return_new_group_'.$groupId }}">
                                        <div id="{{ 'return_new_group_form_'.$groupId }}">
                                            <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                                                <h3 class="amb-4">{{ __('New Return Group') }}</h3>
                                                <button class="btn btn-danger btn-sm apx-16" onclick="deleteGroup(`{{ 'return_new_group_' . $groupId }}`)">
                                                    <i class="fa fa-trash font-14"></i>
                                                </button>
                                            </div>

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                    <input type="text" class="form-control" name="{{ 'return_new_group[' . $groupId . '][name]' }}"
                                                        placeholder="Package Group Name (*)" value="{{ $group['name'] ?? '' }}"
                                                    />
                                                </div>
                                            </div>
                                            @if ($errors->has('return_new_group.' . $groupId . '.name'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('return_new_group.' . $groupId . '.name') }}
                                                </p>
                                            @endif

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <b>{{ __('Package Group Unit Width') }}</b>
                                                    <input type="number" class="form-control" name="{{ 'return_new_group[' . $groupId . '][unit_width]' }}" placeholder="Package Group Unit Width" value="{{ $group['unit_width'] ?? '' }}" step="any" min="0"
                                                    />
                                                    @if ($errors->has('return_new_group.' . $groupId . '.unit_width'))
                                                        <p class="text-danger mb-0">
                                                            {{ $errors->first('return_new_group.' . $groupId . '.unit_width') }}
                                                        </p>
                                                    @endif
                                                </div>
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <b>{{ __('Package Group Unit Weight') }}</b>
                                                    <input type="number" class="form-control" name="{{ 'return_new_group[' . $groupId . '][unit_weight]' }}" placeholder="Package Group Unit Weight" value="{{ $group['unit_weight'] ?? '' }}" step="any" min="0"
                                                    />
                                                    @if ($errors->has('return_new_group.' . $groupId . '.unit_weight'))
                                                        <p class="text-danger mb-0">
                                                            {{ $errors->first('return_new_group.' . $groupId . '.unit_weight') }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <b>{{ __('Package Group Unit Height') }}</b>
                                                    <input type="number" class="form-control" name="{{ 'return_new_group[' . $groupId . '][unit_height]' }}" placeholder="Package Group Unit Height" value="{{ $group['unit_height'] ?? '' }}" step="any" min="0"
                                                    />
                                                    @if ($errors->has('return_new_group.' . $groupId . '.unit_height'))
                                                        <p class="text-danger mb-0">
                                                            {{ $errors->first('return_new_group.' . $groupId . '.unit_height') }}
                                                        </p>
                                                    @endif
                                                </div>
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <b>{{ __('Package Group Unit Length') }}</b>
                                                    <input type="number" class="form-control" name="{{ 'return_new_group[' . $groupId . '][unit_length]' }}" placeholder="Package Group Unit Length" value="{{ $group['unit_length'] ?? '' }}" step="any" min="0"
                                                    />
                                                    @if ($errors->has('return_new_group.' . $groupId . '.unit_length'))
                                                        <p class="text-danger mb-0">
                                                            {{ $errors->first('return_new_group.' . $groupId . '.unit_length') }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                    <input type="number" class="form-control" name="{{ 'return_new_group[' . $groupId . '][unit_number]' }}"
                                                        placeholder="Number Unit (*)" value="{{ $group['unit_number'] ?? '' }}" step="any" min="0"
                                                    />
                                                </div>
                                            </div>
                                            @if ($errors->has('return_new_group.' . $groupId . '.unit_number'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('return_new_group.' . $groupId . '.unit_number') }}
                                                </p>
                                            @endif

                                            <div class="amb-20">
                                                <b>{{ __('Tracking urls') }}</b>
                                                <div id="{{ 'return_new_group_track_' . $groupId}}">
                                                    @if (isset($group['tracking_url']) && $group['tracking_url'] != null)
                                                        @foreach ($group['tracking_url'] as $k => $v)
                                                            <div class="row amb-8 amx-n4">
                                                                <div class="col-10 col-xl-8 apx-4">
                                                                    <input type="text" class="form-control" name="{{ 'return_new_group[' . $groupId . '][tracking_url][]' }}" placeholder="Tracking url" value="{{ $v ?? '' }}"
                                                                    />
                                                                </div>
                                                                <div class="d-flex align-items-center apx-12">
                                                                    <i class="fa fa-close atext-gray-500 font-20 pointer" onclick="deletePackage(this)"></i>
                                                                </div>
                                                            </div>
                                                            @if ($errors->has('return_new_group.' . $groupId . '.tracking_url.' . $k))
                                                                <p class="text-danger mb-0">
                                                                    {{ $errors->first('return_new_group.' . $groupId . '.tracking_url.' . $k) }}
                                                                </p>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </div>

                                                <button type="button" class="btn btn-secondary apx-24 add-btn" onclick="addTrack('return_new_group', {{ $groupId }})">
                                                    {{ __('Add') }}
                                                </button>
                                            </div>

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                    <input id="{{ 'new_return_new_unit_file_barcode_' . $groupId }}" type="file" hidden
                                                        class="file-select"
                                                        data-id="{{ 'new_return_new_unit_barcode_' . $groupId }}"
                                                        name="{{ 'return_new_group['. $groupId . '][file_unit_barcode]' }}" accept="image/*,application/pdf"
                                                    >
                                                    <div id="{{ 'new_return_new_unit_barcode_' . $groupId . '_img' }}">No file selected</div>
                                                    <div class="btn btn-info w-100" onclick="uploadBarcodeImage(`{{ 'new_return_new_unit_file_barcode_' . $groupId }}`)">
                                                        Upload Unit code file
                                                    </div>
                                                    <div id="{{ 'new_return_new_unit_barcode_' . $groupId . '_error' }}" class="text-danger mb-0"></div>
                                                </div>
                                            </div>
                                            @if ($errors->has('return_new_group.' . $groupId . '.file_unit_barcode'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('return_new_group.' . $groupId . '.file_unit_barcode') }}
                                                </p>
                                            @endif

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                    <input id="{{ 'new_return_new_unit_barcode_' . $groupId }}" type="text" class="form-control w-100"
                                                        name="{{ 'return_new_group['. $groupId . '][unit_barcode]' }}" placeholder="Unit QR code" value="{{ $group['unit_barcode'] ?? '' }}">
                                                    <button type="button"  class="btn scan-btn apy-4 group-start-button" data-id="{{ 'new_return_new_unit_barcode_' . $groupId }}" data-toggle="modal" data-target="#scan-modal">
                                                        <i class="fa fa-qrcode font-20"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @if ($errors->has('return_new_group.' . $groupId . '.unit_barcode'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('return_new_group.' . $groupId . '.unit_barcode') }}
                                                </p>
                                            @endif

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                    <input id="{{ 'return_new_group_file_barcode_' . $groupId }}" type="file" hidden
                                                        class="file-select"
                                                        data-id="{{ 'new_return_new_barcode_' . $groupId }}"
                                                        name="{{ 'return_new_group['. $groupId . '][file_barcode]' }}" accept="image/*,application/pdf"
                                                    >
                                                    <div id="{{ 'new_return_new_barcode_' . $groupId . '_img' }}">No file selected</div>
                                                    <div class="btn btn-info w-100" onclick="uploadBarcodeImage(`{{ 'return_new_group_file_barcode_' . $groupId }}`)">
                                                        Upload New Package Group QR code file
                                                    </div>
                                                    <div id="{{ 'new_return_new_barcode_' . $groupId . '_error' }}" class="text-danger mb-0"></div>
                                                </div>
                                            </div>

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                    <input id="{{ 'new_return_new_barcode_' . $groupId }}" type="text" class="form-control w-100"
                                                        name="{{ 'return_new_group['. $groupId . '][barcode]' }}" placeholder="New Package Group QR code" value="{{ $group['barcode'] ?? '' }}">
                                                    <button type="button"  class="btn scan-btn apy-4 group-start-button" data-id="{{ 'new_return_new_barcode_' . $groupId }}" data-toggle="modal" data-target="#scan-modal">
                                                        <i class="fa fa-qrcode font-20"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @if ($errors->has('return_new_group.' . $groupId . '.barcode'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('return_new_group.' . $groupId . '.barcode') }}
                                                </p>
                                            @endif

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                    <input type="file" accept="image/*" hidden id="{{ 'return_new_file_unit_upload_' . $groupId }}" name="{{ 'return_new_group[' . $groupId . '][file_unit][]' }}" multiple
                                                        class="btn-primary form-control" onchange="displayFileName(`{{ 'return_new_file_unit_upload_' . $groupId }}`, `{{ 'return_new_file_unit_selected_' . $groupId }}`)">
                                                    <div class="btn btn-info w-100" onclick="uploadImage(`{{ 'return_new_file_unit_upload_' . $groupId }}`)"> Upload unit image</div>
                                                    <span id="{{ 'return_new_file_unit_selected_' . $groupId }}">No file selected</span>
                                                    <div id="{{ 'return_new_file_unit_error_' . $groupId }}" class="text-danger mb-0"></div>
                                                </div>
                                            </div>
                                            @if ($errors->has('return_new_group.' . $groupId . '.file_unit'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('return_new_group.' . $groupId . '.file_unit') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @else
                            @foreach ($userRequest->requestPackageGroups as $groupId => $rpg)
                                <div class="amb-32 apy-8 addition-form" id="{{ 'return_group_'.$groupId }}">
                                    <div id="{{ 'return_group_form_'.$groupId }}">
                                        <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                                            <h3 class="amb-4">{{ __('Return Group') }}</h3>
                                            <button class="btn btn-danger btn-sm apx-16" onclick="deleteGroup(`{{ 'return_group_' . $groupId }}`)">
                                                <i class="fa fa-trash font-14"></i>
                                            </button>
                                        </div>

                                        <input type="hidden"
                                            name="{{ 'return_group[' . $groupId . '][rpgId]' }}"
                                            value="{{ $rpg->id }}"
                                        >

                                        <div class="row amb-20 amx-n4">
                                            <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                <select id="{{ 'return_group_select_' . $groupId }}" name="{{ 'return_group['. $groupId . '][id]' }}" class="form-control pg-select">
                                                    <option selected>{{ __('Select Package Group (*)') }}</option>
                                                    @foreach ($packageGroups as $packageGroup)
                                                        <option value="{{ $packageGroup['id'] }}"
                                                            data-width="{{ $packageGroup['unit_width'] }}" data-weight="{{ $packageGroup['unit_weight'] }}"
                                                            data-height="{{ $packageGroup['unit_height'] }}" data-length="{{ $packageGroup['unit_length'] }}"
                                                            @if ($rpg->package_group_id == $packageGroup['id'])
                                                                selected="selected"
                                                                @php
                                                                    $dataReturnGroup[$groupId] = $packageGroup;
                                                                @endphp
                                                            @endif
                                                        >{{ $packageGroup['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>


                                        // Package Group Info
                                        <div id="{{ 'return_group_select_' . $groupId . '_content' }}">
                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <b>{{ __('Package Group Unit Width') }}</b>
                                                    <div class="form-control">
                                                        {{ $rpg->packageGroup->unit_width ?? '' }}
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <b>{{ __('Package Group Unit Weight') }}</b>
                                                    <div class="form-control">
                                                        {{ $rpg->packageGroup->unit_weight ?? '' }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <b>{{ __('Package Group Unit Height') }}</b>
                                                    <div class="form-control">
                                                        {{ $rpg->packageGroup->unit_height ?? '' }}
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <b>{{ __('Package Group Unit Length') }}</b>
                                                    <div class="form-control">
                                                        {{ $rpg->packageGroup->unit_length ?? '' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row amb-20 amx-n4">
                                            <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                <input type="number" class="form-control" name="{{ 'return_group[' . $groupId . '][unit_number]' }}"
                                                    placeholder="Number Unit (*)" value="{{ $rpg->requestPackages[0]->unit_number ?? '' }}" step="any" min="0"
                                                />
                                            </div>
                                        </div>


                                        // Tracking URL
                                        <div class="amb-20">
                                            <b>{{ __('Tracking urls') }}</b>
                                            <div id="{{ 'return_group_track_' . $groupId}}">
                                                @foreach ($rpg->requestPackageTrackings as $k => $v)
                                                    <div class="row amb-8 amx-n4">
                                                        <div class="col-10 col-xl-8 apx-4">
                                                            <input type="text" class="form-control" name="{{ 'return_group[' . $groupId . '][tracking_url][]' }}"
                                                                placeholder="Tracking url" value="{{ $v->tracking_url ?? '' }}"
                                                            />
                                                        </div>
                                                        <div class="d-flex align-items-center apx-12">
                                                            <i class="fa fa-close atext-gray-500 font-20 pointer" onclick="deletePackage(this)"></i>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <button type="button" class="btn btn-secondary apx-24 add-btn" onclick="addTrack('return_group', {{ $groupId }})">
                                                {{ __('Add') }}
                                            </button>
                                        </div>


                                        // Unit barcode
                                        <div class="row amb-20 amx-n4">
                                            <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                <input id="{{ 'return_new_unit_file_barcode_' . $groupId }}" type="file" hidden
                                                    class="file-select"
                                                    data-id="{{ 'return_new_unit_barcode_' . $groupId }}"
                                                    name="{{ 'return_group['. $groupId . '][file_unit_barcode]' }}" accept="image/*,application/pdf"
                                                >
                                                <div id="{{ 'return_new_unit_barcode_' . $groupId . '_img' }}">No file selected</div>
                                                <div class="btn btn-info w-100" onclick="uploadBarcodeImage(`{{ 'return_new_unit_file_barcode_' . $groupId }}`)">
                                                    Upload Unit code file
                                                </div>
                                                <div id="{{ 'return_new_unit_barcode_' . $groupId . '_error' }}" class="text-danger mb-0"></div>
                                            </div>
                                        </div>

                                        <div class="row amb-20 amx-n4">
                                            <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                <input id="{{ 'return_new_unit_barcode_' . $groupId }}" type="text" class="form-control w-100"
                                                    name="{{ 'return_group['. $groupId . '][unit_barcode]' }}" placeholder="Unit QR code" value="{{ $rpg['barcode'] ?? '' }}">
                                                <button type="button"  class="btn scan-btn apy-4 group-start-button" data-id="{{ 'return_new_unit_barcode_' . $groupId }}" data-toggle="modal" data-target="#scan-modal">
                                                    <i class="fa fa-qrcode font-20"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="row amb-20 amx-n4">
                                            <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                <input type="file" accept="image/*" hidden id="{{ 'return_file_unit_upload_' . $groupId }}" name="{{ 'return_group[' . $groupId . '][file_unit][]' }}" multiple
                                                    class="btn-primary form-control" onchange="displayFileName(`{{ 'return_file_unit_upload_' . $groupId }}`, `{{ 'return_file_unit_selected_' . $groupId }}`)">
                                                <div class="btn btn-info w-100" onclick="uploadImage(`{{ 'return_file_unit_upload_' . $groupId }}`)"> Upload unit image</div>
                                                <span id="{{ 'return_file_unit_selected_' . $groupId }}">No file selected</span>
                                                <div id="{{ 'return_file_unit_error_' . $groupId }}" class="text-danger mb-0"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Group Action') }}</b></label>
                        <div class="search-input">
                            <button type="button" class="btn btn-secondary apx-16 amr-8" onclick="addReturnGroup()">
                                {{ __('Add Group') }}
                            </button>
                            <button type="button" class="btn btn-success apx-16 amr-8" onclick="addReturnNewGroup()">
                                {{ __('Create New Group') }}
                            </button>
                            @if ($errors->has('return_group'))
                                <p class="text-danger mb-0">
                                    {{ $errors->first('return_group') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div> --}}


                {{-- Other option --}}
                {{-- <div id="other_option"
                    @if(!isset($requestTypes[old('m_request_type_id')]) || $requestTypes[old('m_request_type_id')] != "warehouse labor")
                        style="display: none;"
                    @endif
                >
                    <div class="form-group search-form-group">
                        <label for="option" class="col-form-label search-label"><b>{{ __('Option') }}</b></label>
                        <div class="search-input">
                            <select id="option" name="option" class="form-control w-75">
                                <option selected>{{ __('Select Option') }}</option>
                                @foreach (App\Models\UserRequest::$optionName as $key => $option)
                                    <option value="{{ $key }}"
                                        @if (old('option') != null && old('option') == $key)
                                            selected="selected"
                                        @endif
                                    >{{ $option }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('option'))
                                <p class="text-danger mb-0">
                                    {{ $errors->first('option') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div> --}}


                {{-- TODO: unit package option --}}
                {{-- <div id="unit_package_option"
                    @if($hideUnit)
                        style="display: none;"
                    @endif
                >
                    <div id="unit_package_option_content" class="amt-8">
                        @if (old('unit_group') != null)
                            @foreach (old('unit_group') as $groupId => $group)
                                @if (isset($unitPackageGroups[$group['id']]))
                                    <div class="amb-32 apy-8 addition-form" id="{{ 'unit_group_'.$groupId }}">
                                        <div id="{{ 'unit_group_form_'.$groupId }}">
                                            <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                                                <h3 class="amb-4">{{ __('Package Group') }}</h3>
                                                <button class="btn btn-danger btn-sm apx-16" onclick="deleteGroup(`{{ 'unit_group_' . $groupId }}`)">
                                                    <i class="fa fa-trash font-14"></i>
                                                </button>
                                            </div>

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                    <select id="{{ 'unit_group_select_' . $groupId }}" name="{{ 'unit_group['. $groupId . '][id]' }}"  data-id="{{ $groupId }}" class="form-control pg-select">
                                                        <option selected>{{ __('Select Package Group (*)') }}</option>
                                                        @foreach ($unitPackageGroups as $packageGroup)
                                                            <option value="{{ $packageGroup['id'] }}"
                                                                data-width="{{ $packageGroup['unit_width'] }}" data-weight="{{ $packageGroup['unit_weight'] }}"
                                                                data-height="{{ $packageGroup['unit_height'] }}" data-length="{{ $packageGroup['unit_length'] }}"
                                                                data-packages="{{ json_encode($packageGroup['packages']) }}"
                                                                @if (isset($group['id']) && $group['id'] == $packageGroup['id'])
                                                                    selected="selected"
                                                                    @php
                                                                        $dataUnitGroup[$groupId] = $packageGroup;
                                                                    @endphp
                                                                @endif
                                                            >{{ $packageGroup['name'] }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            @if ($errors->has('unit_group.' . $groupId . '.id'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('unit_group.' . $groupId . '.id') }}
                                                </p>
                                            @endif

                                            <div id="{{ 'unit_group_select_' . $groupId . '_content' }}">
                                                @if (isset($dataUnitGroup[$groupId]))
                                                    <div class="row amb-20 amx-n4">
                                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                            <b>{{ __('Package Group Unit Width') }}</b>
                                                            <div class="form-control">
                                                                {{ $dataUnitGroup[$groupId]['unit_width'] ?? '' }}
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                            <b>{{ __('Package Group Unit Weight') }}</b>
                                                            <div class="form-control">
                                                                {{ $dataUnitGroup[$groupId]['unit_weight'] ?? '' }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row amb-20 amx-n4">
                                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                            <b>{{ __('Package Group Unit Height') }}</b>
                                                            <div class="form-control">
                                                                {{ $dataUnitGroup[$groupId]['unit_height'] ?? '' }}
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                            <b>{{ __('Package Group Unit Length') }}</b>
                                                            <div class="form-control">
                                                                {{ $dataUnitGroup[$groupId]['unit_length'] ?? '' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="amb-20">
                                                    <b>{{ __('Stored package information') }}</b>
                                                    <div id="{{ 'unit_package_info_' . $groupId }}">
                                                        @if (isset($group['info']))
                                                            @foreach ($group['info'] as $unitInfoId => $info)
                                                                <div id="{{ 'unit_package_info_' . $groupId . '_' . $unitInfoId }}">
                                                                    <div class="row amb-20 amx-n4">
                                                                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                                            <select id="unit_group_select_${id}" name="{{ 'unit_group[' . $groupId . '][info][' . $unitInfoId . '][unit_number]' }}" class="form-control">
                                                                                <option value="-1">{{ __('Select Package (*)') }}</option>
                                                                                @foreach ($unitPackageGroups[$group['id']]['packages'] as $kp => $package)
                                                                                    <option value="{{ $package['unit_number'] }}"
                                                                                        @if (isset($package['unit_number']) && isset($info['unit_number']) && $package['unit_number'] == $info['unit_number'])
                                                                                            selected="selected"
                                                                                        @endif
                                                                                    >
                                                                                        {{ 'Package has ' . $package['unit_number'] . ' Unit (Max number package = ' . $package['total']. ')' }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                            @if ($errors->has('unit_group.' . $groupId . '.info.' . $unitInfoId . '.unit_number'))
                                                                                <p class="text-danger mb-0">
                                                                                    {{ $errors->first('unit_group.' . $groupId . '.info.' . $unitInfoId . '.unit_number') }}
                                                                                </p>
                                                                            @endif
                                                                        </div>

                                                                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                                            <input type="number" class="form-control" name="{{ 'unit_group[' . $groupId . '][info][' . $unitInfoId . '][package_number]' }}"
                                                                                placeholder="Number Package (*)" step="any" min="1" value="{{ $info['package_number'] ?? '' }}"
                                                                            />
                                                                            @if ($errors->has('unit_group.' . $groupId . '.info.' . $unitInfoId . '.package_number'))
                                                                                <p class="text-danger mb-0">
                                                                                    {{ $errors->first('unit_group.' . $groupId . '.info.' . $unitInfoId . '.package_number') }}
                                                                                </p>
                                                                            @endif
                                                                        </div>

                                                                        <div class="d-flex align-items-center apx-12">
                                                                            <i class="fa fa-close atext-gray-500 font-20 pointer" onclick="deleteUnitPackageInfo(this)"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                    </div>

                                                    <button type="button" class="btn btn-secondary apx-24 add-btn" onclick="addUnitPackageInfo(`{{ $groupId }}`)">
                                                        {{ __('Add') }}
                                                    </button>
                                                    @if ($errors->has('unit_group.' . $groupId . '.info'))
                                                        <p class="text-danger mb-0">
                                                            {{ $errors->first('unit_group.' . $groupId . '.info') }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>

                                            @if ($hasOldRequest && in_array($requestTypes[$oldRequest], ['relabel', 'outbound', 'warehouse labor']))
                                                <div class="row amb-20 amx-n4">
                                                    <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                        <input id="{{ 'unit_group_file_barcode_' . $groupId }}" type="file" hidden
                                                            class="file-select"
                                                            data-id="{{ 'unit_group_barcode_' . $groupId }}"
                                                            name="{{ 'unit_group['. $groupId . '][file_barcode]' }}" accept="image/*,application/pdf"
                                                        >
                                                        <div id="{{ 'unit_group_barcode_' . $groupId . '_img' }}">No file selected</div>
                                                        <div class="btn btn-info w-100" onclick="uploadBarcodeImage(`{{ 'unit_group_file_barcode_' . $groupId }}`)">
                                                            Upload code file
                                                        </div>
                                                        <div id="{{ 'unit_group_barcode_' . $groupId . '_error' }}" class="text-danger mb-0"></div>
                                                    </div>
                                                </div>

                                                <div class="row amb-20 amx-n4">
                                                    <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                        <input id="{{ 'unit_group_barcode_' . $groupId }}" type="text" class="form-control w-100"
                                                            name="{{ 'unit_group['. $groupId . '][barcode]' }}" placeholder="QR code" value="{{ $group['barcode'] ?? '' }}">
                                                        <button type="button"  class="btn scan-btn apy-4 group-start-button" data-id="{{ 'unit_group_barcode_' . $groupId }}" data-toggle="modal" data-target="#scan-modal">
                                                            <i class="fa fa-qrcode font-20"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                @if ($errors->has('unit_group.' . $groupId . '.barcode'))
                                                    <p class="text-danger mb-0">
                                                        {{ $errors->first('unit_group.' . $groupId . '.barcode') }}
                                                    </p>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="amb-32 apy-8 addition-form">
                                        @if ($errors->has('unit_group.' . $groupId . '.id'))
                                            <p class="text-danger mb-0">
                                                {{ $errors->first('unit_group.' . $groupId . '.id') }}
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        @endif

                        @if (old('unit_group') == null)
                            @foreach ($userRequest->requestPackageGroups as $groupId => $group)
                                <div class="amb-32 apy-8 addition-form" id="{{ 'unit_group_'. '$groupId' }}">
                                    <div id="{{ 'unit_group_form_'. '$groupId' }}">
                                        <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                                            <h3 class="amb-4">{{ __('Package Group') }}</h3>
                                            <button class="btn btn-danger btn-sm apx-16" onclick="deleteGroup(`{{ 'unit_group_' . '$groupId' }}`)">
                                                <i class="fa fa-trash font-14"></i>
                                            </button>
                                        </div>

                                        <div class="row amb-20 amx-n4">
                                            <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                <select id="{{ 'unit_group_select_' . '$groupId' }}" name="{{ 'unit_group['. '$groupId' . '][id]' }}"  data-id="{{ '$groupId' }}" class="form-control pg-select">
                                                    <option selected>{{ __('Select Package Group (*)') }}</option>
                                                    @foreach ($packageGroups as $packageGroup)
                                                        <option value="{{ $packageGroup['id'] }}"
                                                            data-width="{{ $packageGroup['unit_width'] }}" data-weight="{{ $packageGroup['unit_weight'] }}"
                                                            data-height="{{ $packageGroup['unit_height'] }}" data-length="{{ $packageGroup['unit_length'] }}"
                                                            data-packages="{{ json_encode($packageGroup['packages']) }}"
                                                            @if ($group->package_group_id == $packageGroup['id'])
                                                                selected="selected"
                                                                @php
                                                                    $dataUnitGroup[$groupId] = $packageGroup;
                                                                @endphp
                                                            @endif
                                                        >{{ $packageGroup['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div id="{{ 'unit_group_select_' . $groupId . '_content' }}">
                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <b>{{ __('Package Group Unit Width') }}</b>
                                                    <div class="form-control">
                                                        {{ $group->packageGroup->unit_width ?? '' }}
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <b>{{ __('Package Group Unit Weight') }}</b>
                                                    <div class="form-control">
                                                        {{ $group->packageGroup->unit_weight ?? '' }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <b>{{ __('Package Group Unit Height') }}</b>
                                                    <div class="form-control">
                                                        {{ $group->packageGroup->unit_height ?? '' }}
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <b>{{ __('Package Group Unit Length') }}</b>
                                                    <div class="form-control">
                                                        {{ $group->packageGroup->unit_length ?? '' }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="amb-20">
                                                <b>{{ __('Stored package information') }}</b>
                                                <div id="{{ 'unit_package_info_' . '$groupId' }}">
                                                    @foreach ($group->requestPackages as $unitInfoId => $requestPackage)
                                                        <div id="{{ 'unit_package_info_' . $groupId . '_' . $unitInfoId }}">
                                                        <div>
                                                            <div class="row amb-20 amx-n4">
                                                                <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                                    <select id="unit_group_select_${id}" name="{{ 'unit_group[' . $groupId . '][info][' . $unitInfoId . '][unit_number]' }}" class="form-control">
                                                                        <option value="-1">
                                                                            {{ 'Package has ' . $requestPackage['unit_number'] . ' Unit' }}
                                                                        </option>
                                                                        @foreach ($unitPackageGroups[$group->package_group_id]['packages'] as $kp => $package)
                                                                            <option value="{{ $package['unit_number'] }}"
                                                                                @if (isset($package['unit_number']) && isset($info['unit_number']) && $package['unit_number'] == $info['unit_number'])
                                                                                    selected="selected"
                                                                                @endif
                                                                            >
                                                                                {{ 'Package has ' . $package['unit_number'] . ' Unit (Max number package = ' . $package['total']. ')' }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                                    <input type="number" class="form-control" name="{{ 'unit_group[' . $groupId . '][info][' . $unitInfoId . '][package_number]' }}"
                                                                        placeholder="Number Package (*)" step="any" min="1" value="{{ $requestPackage->package_number ?? '' }}"
                                                                    />
                                                                </div>

                                                                <div class="d-flex align-items-center apx-12">
                                                                    <i class="fa fa-close atext-gray-500 font-20 pointer" onclick="deleteUnitPackageInfo(this)"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <button type="button" class="btn btn-secondary apx-24 add-btn" onclick="addUnitPackageInfo(`{{ $groupId }}`)">
                                                    {{ __('Add') }}
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Group Action') }}</b></label>
                        <div class="search-input">
                            <button type="button" class="btn btn-secondary apx-16 amr-8" onclick="addUnitGroup()">
                                {{ __('Add Group') }}
                            </button>
                            @if ($errors->has('unit_group'))
                                <p class="text-danger mb-0">
                                    {{ $errors->first('unit_group') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div> --}}

                {{-- Submit Edit button --}}
                <div class="search-form-group">
                    <div class="search-label d-none d-sm-block"></div>
                    <div class="search-input text-center text-sm-left">
                        <button class="btn btn-primary" type="submit">{{ __('Edit Request') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{--
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

<!-- QR code -->
<img id="imgshow" height="200" hidden> --}}

@endsection


{{-- @section('scripts')
<script>

let id = 0;
    let npgId = 0;
    let pInfo = npInfo = 0;
    let removalId = newRemovalId = 0;
    let returnId = newReturnId = 0;
    let unitId = unitInfoId = 0;
    const maxFileNumber = 3;

    // Toggle Data
    function toggleOption(requestType) {
        if (requestType == 'warehouse labor') {
            $("#other_option").show();
        } else {
            $("#other_option").hide();
        }
    }

    function toggleBarcodeFile(requestType) {
        if (['relabel', 'outbound', 'warehouse labor'].includes(requestType)) {
            $("#required_file").show();
        } else {
            $("#required_file").hide();
        }
    }

    function toggleAddPackage(requestType) {
        if (requestType == 'add package') {
            $("#new_package_option").show();
        } else {
            $("#new_package_option").hide();
        }

        if (['relabel', 'repack', 'outbound', 'warehouse labor'].includes(requestType)) {
            $("#unit_package_option").show();
        } else {
            $("#unit_package_option").hide();
        }
    }

    function toggleRemoval(requestType) {
        if (requestType == 'removal') {
            $("#removal_option").show();
        } else {
            $("#removal_option").hide();
        }
    }

    function toggleReturn(requestType) {
        if (requestType == 'return') {
            $("#return_option").show();
        } else {
            $("#return_option").hide();
        }
    }

    function toggleContent() {
        $(`#note`).val('');
        $(`#add_package_option_content`).empty();
        $(`#removal_option_content`).empty();
        $(`#return_option_content`).empty();
        $(`#unit_package_option_content`).empty();
    }

    function toggleSizeRadio(requestType) {
        if (['add package', 'removal', 'return'].includes(requestType)) {
            $("#size_radio_option").show();
        } else {
            $("#size_radio_option").hide();
        }
    }


    // ADD GROUP
    function addGroup() {
        while ($(`#package_group_${id}`).length) {
            id += 1;
        }

        $('#add_package_option_content').append(`
            <div class="amb-32 apy-8 addition-form" id="package_group_${id}">
                <div id="package_group_form_${id}">
                    <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                        <h3 class="amb-4">{{ __('Package Group') }}</h3>
                        <button class="btn btn-danger btn-sm apx-16" onclick="deleteGroup('package_group_${id}')">
                            <i class="fa fa-trash font-14"></i>
                        </button>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <select id="package_group_select_${id}" name="package_group[${id}][id]" class="form-control pg-select">
                                <option selected>{{ __('Select Package Group (*)') }}</option>
                                @foreach ($packageGroups as $packageGroup)
                                    <option value="{{ $packageGroup['id'] }}"
                                        data-width="{{ $packageGroup['unit_width'] }}" data-weight="{{ $packageGroup['unit_weight'] }}"
                                        data-height="{{ $packageGroup['unit_height'] }}" data-length="{{ $packageGroup['unit_length'] }}"
                                    >
                                        {{ $packageGroup['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="package_group_select_${id}_content"></div>

                    <div class="amb-20">
                        <b>{{ __('Package information') }}</b>
                        <div id="package_info_${id}"></div>

                        <button type="button" class="btn btn-secondary apx-24 add-btn" onclick="addPackageInfo(${id})">
                            {{ __('Add') }}
                        </button>
                    </div>

                    <div class="amb-20">
                        <b>{{ __('Tracking urls') }}</b>
                        <div id="package_group_track_${id}"></div>

                        <button type="button" class="btn btn-secondary apx-24 add-btn" onclick="addTrack('package_group', ${id})">
                            {{ __('Add') }}
                        </button>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <input type="file" accept="image/*" hidden id="file_unit_upload_pg_${id}" name="package_group[${id}][file_unit][]" multiple
                                class="btn-primary form-control" onchange="displayFileName('file_unit_upload_pg_${id}', 'file_unit_selected_pg_${id}')">
                            <div class="btn btn-info w-100" onclick="uploadImage('file_unit_upload_pg_${id}')">Upload unit image</div>
                            <span id="file_unit_selected_pg_${id}">No file selected</span>
                            <div id="file_unit_error_pg_${id}" class="text-danger mb-0"></div>
                        </div>
                    </div>
                </div>
            </div>
        `);
    }

    function addPackageInfo(id) {
        while ($(`#package_info_${id}_${pInfo}`).length) {
            pInfo += 1;
        }

        $('#package_info_' + id).append(`
            <div id="package_info_${id}_${pInfo}" class="row amx-n4 amb-8">
                <div class="col-10 col-xl-8 apx-4">
                    <b>{{ __('Package') }}</b>
                </div>

                <div class="col-10 col-xl-8 apx-4">
                    <div class="row amx-n4">
                        <div class="col-12 col-md-6 apx-4 amb-8">
                            <input type="number" class="form-control" name="package_group[${id}][info][${pInfo}][package_width]" placeholder="Package Width" step="any" min="0" />
                        </div>
                        <div class="col-12 col-md-6 apx-4 amb-8">
                            <input type="number" class="form-control" name="package_group[${id}][info][${pInfo}][package_weight]" placeholder="Package Weight" step="any" min="0" />
                        </div>
                    </div>
                </div>

                <div class="col-10 col-xl-8 apx-4">
                    <div class="row amx-n4">
                        <div class="col-12 col-md-6 apx-4 amb-8">
                            <input type="number" class="form-control" name="package_group[${id}][info][${pInfo}][package_height]" placeholder="Package Height" step="any" min="0" />
                        </div>
                        <div class="col-12 col-md-6 apx-4 amb-8">
                            <input type="number" class="form-control" name="package_group[${id}][info][${pInfo}][package_length]" placeholder="Package Length" step="any" min="0" />
                        </div>
                    </div>
                </div>

                <div class="col-10 col-xl-8 apx-4">
                    <div class="row amx-n4">
                        <div class="col-12 col-md-6 apx-4 amb-8">
                            <input type="number" class="form-control" name="package_group[${id}][info][${pInfo}][unit_number]" placeholder="Number Unit per Package (*)" step="any" min="0" />
                        </div>
                        <div class="col-12 col-md-6 apx-4 amb-8">
                            <input type="number" class="form-control" name="package_group[${id}][info][${pInfo}][package_number]" placeholder="Number package (*)" step="any" min="0" />
                        </div>
                    </div>
                </div>

                <div class="col d-flex align-items-center apx-12 amb-8">
                    <i class="fa fa-close atext-gray-500 font-20 pointer line-height-1" onclick="deletePackageInfo(this)"></i>
                </div>

                <div  class="col-10 col-xl-8 apx-4"><hr></div>
            </div>
        `)
    }

    function deletePackageInfo(e) {
        $(e).parent().parent().remove()
    }

    // ADD NEW GROUP
    function addNewGroup() {
        while ($(`#new_package_group_${npgId}`).length) {
            npgId += 1;
        }

        $('#add_package_option_content').append(`
            <div class="amb-32 apy-8 addition-form" id="new_package_group_${npgId}">
                <div id="new_package_group_form_${npgId}">
                    <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                        <h3 class="amb-4">{{ __('New Package Group') }}</h3>
                        <button class="btn btn-danger btn-sm apx-16" onclick="deleteGroup('new_package_group_${npgId}')">
                            <i class="fa fa-trash font-14"></i>
                        </button>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <input type="text" class="form-control" name="new_package_group[${npgId}][name]"
                                placeholder="Package Group Name (*)"
                            />
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <b>{{ __('Package Group Unit Width') }}</b>
                            <input type="number" class="form-control" name="new_package_group[${npgId}][unit_width]" placeholder="Package Group Unit Width" step="any" min="0"
                            />
                        </div>
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <b>{{ __('Package Group Unit Weight') }}</b>
                            <input type="number" class="form-control" name="new_package_group[${npgId}][unit_weight]" placeholder="Package Group Unit Weight" step="any" min="0"
                            />
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <b>{{ __('Package Group Unit Height') }}</b>
                            <input type="number" class="form-control" name="new_package_group[${npgId}][unit_height]" placeholder="Package Group Unit Height" step="any" min="0"
                            />
                        </div>
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <b>{{ __('Package Group Unit Length') }}</b>
                            <input type="number" class="form-control" name="new_package_group[${npgId}][unit_length]" placeholder="Package Group Unit Length" step="any" min="0"
                            />
                        </div>
                    </div>

                    <div class="amb-20">
                        <b>{{ __('Package information') }}</b>
                        <div id="new_package_info_${npgId}"></div>

                        <button type="button" class="btn btn-secondary apx-24 add-btn" onclick="addNewPackageInfo(${npgId})">
                            {{ __('Add') }}
                        </button>
                    </div>

                    <div class="amb-20">
                        <b>{{ __('Tracking urls') }}</b>
                        <div id="new_package_group_track_${npgId}"></div>

                        <button type="button" class="btn btn-secondary apx-24 add-btn" onclick="addTrack('new_package_group', ${npgId})">
                            {{ __('Add') }}
                        </button>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <input id="new_package_group_file_barcode_${npgId}" type="file" hidden
                                class="file-select"
                                data-id="new_package_group_barcode_${npgId}"
                                name="new_package_group[${npgId}][file_barcode]" accept="image/*,application/pdf"
                            >
                            <div id="{{ 'new_package_group_barcode_${npgId}_img' }}">No file selected</div>
                            <div class="btn btn-info w-100" onclick="uploadBarcodeImage('new_package_group_file_barcode_${npgId}')">
                                Upload New Package Group QR code file
                            </div>
                            <div id="new_package_group_barcode_${npgId}_error" class="text-danger mb-0"></div>
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <input id="new_package_group_barcode_${npgId}" type="text" placeholder="New Package Group QR code"
                                class="form-control w-100" name="new_package_group[${npgId}][barcode]"
                            >
                            <button type="button"  class="btn scan-btn apy-4 group-start-button" data-id="new_package_group_barcode_${npgId}" data-toggle="modal" data-target="#scan-modal">
                                <i class="fa fa-qrcode font-20"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <input type="file" accept="image/*" hidden id="file_unit_upload_${npgId}" name="new_package_group[${npgId}][file_unit][]" multiple
                                class="btn-primary form-control" onchange="displayFileName('file_unit_upload_${npgId}', 'file_unit_selected_${npgId}')">
                            <div class="btn btn-info w-100" onclick="uploadImage('file_unit_upload_${npgId}')"> Upload unit image</div>
                            <span id="file_unit_selected_${npgId}">No file selected</span>
                            <div id="file_unit_error_${npgId}" class="text-danger mb-0"></div>
                        </div>
                    </div>
                </div>
            </div>
        `);
    }

    function addNewPackageInfo(id) {
        while ($(`#new_package_info_${id}_${npInfo}`).length) {
            npInfo += 1;
        }

        $('#new_package_info_' + id).append(`
            <div id="new_package_info_${id}_${npInfo}" class="row amx-n4 amb-8">
                <div class="col-10 col-xl-8 apx-4">
                    <b>{{ __('Package') }}</b>
                </div>

                <div class="col-10 col-xl-8 apx-4">
                    <div class="row amx-n4">
                        <div class="col-12 col-md-6 apx-4 amb-8">
                            <input type="number" class="form-control" name="new_package_group[${id}][info][${npInfo}][package_width]" placeholder="Package Width" step="any" min="0" />
                        </div>
                        <div class="col-12 col-md-6 apx-4 amb-8">
                            <input type="number" class="form-control" name="new_package_group[${id}][info][${npInfo}][package_weight]" placeholder="Package Weight" step="any" min="0" />
                        </div>
                    </div>
                </div>

                <div class="col-10 col-xl-8 apx-4">
                    <div class="row amx-n4">
                        <div class="col-12 col-md-6 apx-4 amb-8">
                            <input type="number" class="form-control" name="new_package_group[${id}][info][${npInfo}][package_height]" placeholder="Package Height" step="any" min="0" />
                        </div>
                        <div class="col-12 col-md-6 apx-4 amb-8">
                            <input type="number" class="form-control" name="new_package_group[${id}][info][${npInfo}][package_length]" placeholder="Package Length" step="any" min="0" />
                        </div>
                    </div>
                </div>

                <div class="col-10 col-xl-8 apx-4">
                    <div class="row amx-n4">
                        <div class="col-12 col-md-6 apx-4 amb-8">
                            <input type="number" class="form-control" name="new_package_group[${id}][info][${npInfo}][unit_number]" placeholder="Number Unit per Package (*)" step="any" min="0" />
                        </div>
                        <div class="col-12 col-md-6 apx-4 amb-8">
                            <input type="number" class="form-control" name="new_package_group[${id}][info][${npInfo}][package_number]" placeholder="Number package (*)" step="any" min="0" />
                        </div>
                    </div>
                </div>

                <div class="col d-flex align-items-center apx-12 amb-8">
                    <i class="fa fa-close atext-gray-500 font-20 pointer line-height-1" onclick="deletePackageInfo(this)"></i>
                </div>

                <div  class="col-10 col-xl-8 apx-4"><hr></div>
            </div>
        `)
    }

    function deleteGroup(id) {
       $(`#${id}`).remove();
    }


    function deletePackage(e) {
       $(e).parent().parent().remove();
    }

    function uploadImage(id) {
        $(`#${id}`).click();
    }

    function uploadBarcodeImage(targetId) {
        $(`#${targetId}`).click();
    }

    function addRemovalGroup() {
        while ($(`#removal_group_${removalId}`).length) {
            removalId += 1;
        }

        $('#removal_option_content').append(`
            <div class="amb-32 apy-8 addition-form" id="removal_group_${removalId}">
                <div id="removal_group_form_${removalId}">
                    <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                        <h3 class="amb-4">{{ __('Removal Group') }}</h3>
                        <button class="btn btn-danger btn-sm apx-16" onclick="deleteGroup('removal_group_${removalId}')">
                            <i class="fa fa-trash font-14"></i>
                        </button>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <select id="removal_group_select_${removalId}" name="removal_group[${removalId}][id]" class="form-control pg-select">
                                <option selected>{{ __('Select Package Group (*)') }}</option>
                                @foreach ($packageGroups as $packageGroup)
                                    <option value="{{ $packageGroup['id'] }}"
                                        data-width="{{ $packageGroup['unit_width'] }}" data-weight="{{ $packageGroup['unit_weight'] }}"
                                        data-height="{{ $packageGroup['unit_height'] }}" data-length="{{ $packageGroup['unit_length'] }}"
                                    >
                                        {{ $packageGroup['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="removal_group_select_${removalId}_content"></div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <input type="number" class="form-control" name="removal_group[${removalId}][unit_number]"
                                placeholder="Number Unit (*)" step="any" min="0"
                            />
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <input id="removal_unit_file_barcode_${removalId}" type="file" hidden
                                class="file-select"
                                data-id="removal_unit_barcode_${removalId}"
                                name="removal_group[${removalId}][file_unit_barcode]" accept="image/*,application/pdf"
                            >
                            <div id="{{ 'removal_unit_barcode_${removalId}_img' }}">No file selected</div>
                            <div class="btn btn-info w-100" onclick="uploadBarcodeImage('removal_unit_file_barcode_${removalId}')">
                                Upload Unit code file
                            </div>
                            <div id="removal_unit_barcode_${removalId}_error" class="text-danger mb-0"></div>
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <input id="removal_unit_barcode_${removalId}" type="text" placeholder="Unit QR code"
                                class="form-control w-100" name="removal_group[${removalId}][unit_barcode]"
                            >
                            <button type="button" class="btn scan-btn apy-4 group-start-button" data-id="removal_unit_barcode_${removalId}" data-toggle="modal" data-target="#scan-modal">
                                <i class="fa fa-qrcode font-20"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <input type="file" accept="image/*" hidden id="removal_file_unit_upload_${removalId}" name="removal_group[${removalId}][file_unit][]" multiple
                                class="btn-primary form-control" onchange="displayFileName('removal_file_unit_upload_${removalId}', 'removal_file_unit_selected_${removalId}')">
                            <div class="btn btn-info w-100" onclick="uploadImage('removal_file_unit_upload_${removalId}')"> Upload unit image</div>
                            <span id="removal_file_unit_selected_${removalId}">No file selected</span>
                            <div id="removal_file_unit_error_${removalId}" class="text-danger mb-0"></div>
                        </div>
                    </div>
                </div>
            </div>
        `);
    }

    function addRemovalNewGroup() {
        while ($(`#removal_new_group_${newRemovalId}`).length) {
            newRemovalId += 1;
        }

        $('#removal_option_content').append(`
            <div class="amb-32 apy-8 addition-form" id="removal_new_group_${newRemovalId}">
                <div id="removal_new_group_form_${newRemovalId}">
                    <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                        <h3 class="amb-4">{{ __('New Removal Group') }}</h3>
                        <button class="btn btn-danger btn-sm apx-16" onclick="deleteGroup('removal_new_group_${newRemovalId}')">
                            <i class="fa fa-trash font-14"></i>
                        </button>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <input type="text" class="form-control" name="removal_new_group[${newRemovalId}][name]"
                                placeholder="Package Group Name (*)"
                            />
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <b>{{ __('Package Group Unit Width') }}</b>
                            <input type="number" class="form-control" name="removal_new_group[${newRemovalId}][unit_width]" placeholder="Package Group Unit Width" step="any" min="0"
                            />
                        </div>
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <b>{{ __('Package Group Unit Weight') }}</b>
                            <input type="number" class="form-control" name="removal_new_group[${newRemovalId}][unit_weight]" placeholder="Package Group Unit Weight" step="any" min="0"
                            />
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <b>{{ __('Package Group Unit Height') }}</b>
                            <input type="number" class="form-control" name="removal_new_group[${newRemovalId}][unit_height]" placeholder="Package Group Unit Height" step="any" min="0"
                            />
                        </div>
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <b>{{ __('Package Group Unit Length') }}</b>
                            <input type="number" class="form-control" name="removal_new_group[${newRemovalId}][unit_length]" placeholder="Package Group Unit Length" step="any" min="0"
                            />
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <input type="number" class="form-control" name="removal_new_group[${newRemovalId}][unit_number]"
                                placeholder="Number Unit (*)" step="any" min="0"
                            />
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <input id="removal_new_unit_file_barcode_${newRemovalId}" type="file" hidden
                                class="file-select"
                                data-id="removal_new_unit_barcode_${newRemovalId}"
                                name="removal_new_group[${newRemovalId}][file_unit_barcode]" accept="image/*,application/pdf"
                            >
                            <div id="{{ 'removal_new_unit_barcode_${newRemovalId}_img' }}">No file selected</div>
                            <div class="btn btn-info w-100" onclick="uploadBarcodeImage('removal_new_unit_file_barcode_${newRemovalId}')">
                                Upload Unit code file
                            </div>
                            <div id="removal_new_unit_barcode_${newRemovalId}_error" class="text-danger mb-0"></div>
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <input id="removal_new_unit_barcode_${newRemovalId}" type="text" placeholder="Unit QR code"
                                class="form-control w-100" name="removal_new_group[${newRemovalId}][unit_barcode]"
                            >
                            <button type="button" class="btn scan-btn apy-4 group-start-button" data-id="removal_new_unit_barcode_${newRemovalId}" data-toggle="modal" data-target="#scan-modal">
                                <i class="fa fa-qrcode font-20"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <input id="removal_new_group_file_barcode_${newRemovalId}" type="file" hidden
                                class="file-select"
                                data-id="removal_new_barcode_${newRemovalId}"
                                name="removal_new_group[${newRemovalId}][file_barcode]" accept="image/*,application/pdf"
                            >
                            <div id="{{ 'removal_new_barcode_${newRemovalId}_img' }}">No file selected</div>
                            <div class="btn btn-info w-100" onclick="uploadBarcodeImage('removal_new_group_file_barcode_${newRemovalId}')">
                                Upload New Package Group QR code file
                            </div>
                            <div id="removal_new_barcode_${newRemovalId}_error" class="text-danger mb-0"></div>
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <input id="removal_new_barcode_${newRemovalId}" type="text" placeholder="New Package Group QR code"
                                class="form-control w-100" name="removal_new_group[${newRemovalId}][barcode]"
                            >
                            <button type="button" class="btn scan-btn apy-4 group-start-button" data-id="removal_new_barcode_${newRemovalId}" data-toggle="modal" data-target="#scan-modal">
                                <i class="fa fa-qrcode font-20"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <input type="file" accept="image/*" hidden id="removal_new_file_unit_upload_${newRemovalId}" name="removal_new_group[${newRemovalId}][file_unit][]" multiple
                                class="btn-primary form-control" onchange="displayFileName('removal_new_file_unit_upload_${newRemovalId}', 'removal_new_file_unit_selected_${newRemovalId}')">
                            <div class="btn btn-info w-100" onclick="uploadImage('removal_new_file_unit_upload_${newRemovalId}')"> Upload unit image</div>
                            <span id="removal_new_file_unit_selected_${newRemovalId}">No file selected</span>
                            <div id="removal_new_file_unit_error_${newRemovalId}" class="text-danger mb-0"></div>
                        </div>
                    </div>
                </div>
            </div>
        `);
    }

    function addReturnGroup() {
        while ($(`#return_group_${returnId}`).length) {
            returnId += 1;
        }

        $('#return_option_content').append(`
            <div class="amb-32 apy-8 addition-form" id="return_group_${returnId}">
                <div id="return_group_form_${returnId}">
                    <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                        <h3 class="amb-4">{{ __('Return Group') }}</h3>
                        <button class="btn btn-danger btn-sm apx-16" onclick="deleteGroup('return_group_${returnId}')">
                            <i class="fa fa-trash font-14"></i>
                        </button>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <select id="return_group_select_${returnId}" name="return_group[${returnId}][id]" class="form-control pg-select">
                                <option selected>{{ __('Select Package Group (*)') }}</option>
                                @foreach ($packageGroups as $packageGroup)
                                    <option value="{{ $packageGroup['id'] }}"
                                        data-width="{{ $packageGroup['unit_width'] }}" data-weight="{{ $packageGroup['unit_weight'] }}"
                                        data-height="{{ $packageGroup['unit_height'] }}" data-length="{{ $packageGroup['unit_length'] }}"
                                    >
                                        {{ $packageGroup['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="return_group_select_${returnId}_content"></div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <input type="number" class="form-control" name="return_group[${returnId}][unit_number]"
                                placeholder="Number Unit (*)" step="any" min="0"
                            />
                        </div>
                    </div>

                    <div class="amb-20">
                        <b>{{ __('Tracking urls') }}</b>
                        <div id="return_group_track_${returnId}"></div>

                        <button type="button" class="btn btn-secondary apx-24 add-btn" onclick="addTrack('return_group', ${returnId})">
                            {{ __('Add') }}
                        </button>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <input id="return_new_unit_file_barcode_${returnId}" type="file" hidden
                                class="file-select"
                                data-id="return_new_unit_barcode_${returnId}"
                                name="return_group[${returnId}][file_unit_barcode]" accept="image/*,application/pdf"
                            >
                            <div id="{{ 'return_new_unit_barcode_${returnId}_img' }}">No file selected</div>
                            <div class="btn btn-info w-100" onclick="uploadBarcodeImage('return_new_unit_file_barcode_${returnId}')">
                                Upload Unit code file
                            </div>
                            <div id="return_new_unit_barcode_${returnId}_error" class="text-danger mb-0"></div>
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <input id="return_new_unit_barcode_${returnId}" type="text" placeholder="Unit QR code"
                                class="form-control w-100" name="return_group[${returnId}][unit_barcode]"
                            >
                            <button type="button" class="btn scan-btn apy-4 group-start-button" data-id="return_new_unit_barcode_${returnId}" data-toggle="modal" data-target="#scan-modal">
                                <i class="fa fa-qrcode font-20"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <input type="file" accept="image/*" hidden id="return_file_unit_upload_${returnId}" name="return_group[${returnId}][file_unit][]" multiple
                                class="btn-primary form-control" onchange="displayFileName('return_file_unit_upload_${returnId}', 'return_file_unit_selected_${returnId}')">
                            <div class="btn btn-info w-100" onclick="uploadImage('return_file_unit_upload_${returnId}')"> Upload unit image</div>
                            <span id="return_file_unit_selected_${returnId}">No file selected</span>
                            <div id="return_file_unit_error_${returnId}" class="text-danger mb-0"></div>
                        </div>
                    </div>
                </div>
            </div>
        `);
    }

    function addReturnNewGroup() {
        while ($(`#return_new_group_${newReturnId}`).length) {
            newReturnId += 1;
        }

        $('#return_option_content').append(`
            <div class="amb-32 apy-8 addition-form" id="return_new_group_${newReturnId}">
                <div id="return_new_group_form_${newReturnId}">
                    <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                        <h3 class="amb-4">{{ __('New Return Group') }}</h3>
                        <button class="btn btn-danger btn-sm apx-16" onclick="deleteGroup('return_new_group_${newReturnId}')">
                            <i class="fa fa-trash font-14"></i>
                        </button>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <input type="text" class="form-control" name="return_new_group[${newReturnId}][name]" placeholder="Package Group Name (*)"
                            />
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <b>{{ __('Package Group Unit Width') }}</b>
                            <input type="number" class="form-control" name="return_new_group[${newReturnId}][unit_width]" placeholder="Package Group Unit Width" step="any" min="0"
                            />
                        </div>
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <b>{{ __('Package Group Unit Weight') }}</b>
                            <input type="number" class="form-control" name="return_new_group[${newReturnId}][unit_weight]" placeholder="Package Group Unit Weight" step="any" min="0"
                            />
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <b>{{ __('Package Group Unit Height') }}</b>
                            <input type="number" class="form-control" name="return_new_group[${newReturnId}][unit_height]" placeholder="Package Group Unit Height" step="any" min="0"
                            />
                        </div>
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <b>{{ __('Package Group Unit Length') }}</b>
                            <input type="number" class="form-control" name="return_new_group[${newReturnId}][unit_length]" placeholder="Package Group Unit Length" step="any" min="0"
                            />
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <input type="number" class="form-control" name="return_new_group[${newReturnId}][unit_number]" placeholder="Number Unit (*)" step="any" min="0"
                            />
                        </div>
                    </div>

                    <div class="amb-20">
                        <b>{{ __('Tracking urls') }}</b>
                        <div id="return_new_group_track_${newReturnId}"></div>

                        <button type="button" class="btn btn-secondary apx-24 add-btn" onclick="addTrack('return_new_group', ${newReturnId})">
                            {{ __('Add') }}
                        </button>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <input id="new_return_new_unit_file_barcode_${newReturnId}" type="file" hidden
                                class="file-select"
                                data-id="new_return_new_unit_barcode_${newReturnId}"
                                name="return_new_group[${newReturnId}][file_unit_barcode]" accept="image/*,application/pdf"
                            >
                            <div id="{{ 'new_return_new_unit_barcode_${newReturnId}_img' }}">No file selected</div>
                            <div class="btn btn-info w-100" onclick="uploadBarcodeImage('new_return_new_unit_file_barcode_${newReturnId}')">
                                Upload Unit code file
                            </div>
                            <div id="new_return_new_unit_barcode_${newReturnId}_error" class="text-danger mb-0"></div>
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <input id="new_return_new_unit_barcode_${newReturnId}" type="text" placeholder="Unit QR code"
                                class="form-control w-100" name="return_new_group[${newReturnId}][unit_barcode]"
                            >
                            <button type="button" class="btn scan-btn apy-4 group-start-button" data-id="new_return_new_unit_barcode_${newReturnId}" data-toggle="modal" data-target="#scan-modal">
                                <i class="fa fa-qrcode font-20"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <input id="return_new_group_file_barcode_${newReturnId}" type="file" hidden
                                class="file-select"
                                data-id="new_return_new_barcode_${newReturnId}"
                                name="return_new_group[${newReturnId}][file_barcode]" accept="image/*,application/pdf"
                            >
                            <div id="{{ 'new_return_new_barcode_${newReturnId}_img' }}">No file selected</div>
                            <div class="btn btn-info w-100" onclick="uploadBarcodeImage('return_new_group_file_barcode_${newReturnId}')">
                                Upload New Package Group QR code file
                            </div>
                            <div id="new_return_new_barcode_${newReturnId}_error" class="text-danger mb-0"></div>
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <input id="new_return_new_barcode_${newReturnId}" type="text" placeholder="New Package Group QR code"
                                class="form-control w-100" name="return_new_group[${newReturnId}][barcode]"
                            >
                            <button type="button" class="btn scan-btn apy-4 group-start-button" data-id="new_return_new_barcode_${newReturnId}" data-toggle="modal" data-target="#scan-modal">
                                <i class="fa fa-qrcode font-20"></i>
                            </button>
                        </div>
                    </div>


                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <input type="file" accept="image/*" hidden id="return_new_file_unit_upload_${newReturnId}" name="return_new_group[${newReturnId}][file_unit][]" multiple
                                class="btn-primary form-control" onchange="displayFileName('return_new_file_unit_upload_${newReturnId}', 'return_new_file_unit_selected_${newReturnId}')">
                            <div class="btn btn-info w-100" onclick="uploadImage('return_new_file_unit_upload_${newReturnId}')"> Upload unit image</div>
                            <span id="return_new_file_unit_selected_${newReturnId}">No file selected</span>
                            <div id="return_new_file_unit_error_${newReturnId}" class="text-danger mb-0"></div>
                        </div>
                    </div>
                </div>
            </div>
        `);
    }

    function addTrack(prefix, id) {
        $(`#${prefix}_track_${id}`).append(`
            <div class="row amb-8 amx-n4">
                <div class="col-10 col-xl-8 apx-4">
                    <input type="text" class="form-control" name="${prefix}[${id}][tracking_url][]"
                        placeholder="Tracking url"
                    />
                </div>
                <div class="d-flex align-items-center apx-12">
                    <i class="fa fa-close atext-gray-500 font-20 pointer" onclick="deletePackage(this)"></i>
                </div>
            </div>
        `);
    }

    function displayFileName(input, target) {
        const fileNumber = $(`#${input}`)[0].files.length;
        if (fileNumber > maxFileNumber) {
            alert(`You can select max ${maxFileNumber} images`);
            // $(`#${input}`).val('');
            $(`#${target}`).text(`You can select max ${maxFileNumber} images`);
        } else {
            // $(`#${target}`).text($(`#${input}`)[0].files[0].name);
            $(`#${target}`).text(`You have selected ${fileNumber} images`);

            $(`#old_${target}`).empty();
        }
    }

    function addUnitGroup() {
        while ($(`#unit_group_${unitId}`).length) {
            unitId += 1;
        }

        const requestTypeSelect = $('#m_request_type_id').children("option:selected").text();
        let contentBarcode = '';
        if (['relabel', 'outbound', 'warehouse labor'].includes(requestTypeSelect)) {
            contentBarcode = `
                <div class="row amb-20 amx-n4">
                    <div class="col-12 col-md-5 col-xl-4 apx-4">
                        <input id="unit_group_file_barcode_${unitId}" type="file" hidden
                            class="file-select"
                            data-id="unit_group_barcode_${unitId}"
                            name="unit_group[${unitId}][file_barcode]" accept="image/*,application/pdf"
                        >
                        <div id="{{ 'unit_group_barcode_${unitId}_img' }}">No file selected</div>
                        <div class="btn btn-info w-100" onclick="uploadBarcodeImage('unit_group_file_barcode_${unitId}')">
                            Upload code file
                        </div>
                        <div id="unit_group_barcode_${unitId}_error" class="text-danger mb-0"></div>
                    </div>
                </div>

                <div class="row amb-20 amx-n4">
                    <div class="col-12 col-md-5 col-xl-4 apx-4">
                        <input id="unit_group_barcode_${unitId}" type="text" placeholder="QR code"
                            class="form-control w-100" name="unit_group[${unitId}][barcode]"
                        >
                        <button type="button" class="btn scan-btn apy-4 group-start-button" data-id="unit_group_barcode_${unitId}" data-toggle="modal" data-target="#scan-modal">
                            <i class="fa fa-qrcode font-20"></i>
                        </button>
                    </div>
                </div>
            `
        }

        $('#unit_package_option_content').append(`
            <div class="amb-32 apy-8 addition-form" id="unit_group_${unitId}">
                <div id="unit_group_form_${unitId}">
                    <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                        <h3 class="amb-4">{{ __('Package Group') }}</h3>
                        <button class="btn btn-danger btn-sm apx-16" onclick="deleteGroup('unit_group_${unitId}')">
                            <i class="fa fa-trash font-14"></i>
                        </button>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <select id="unit_group_select_${unitId}" name="unit_group[${unitId}][id]" data-id="${unitId}" class="form-control pg-select">
                                <option selected>{{ __('Select Package Group (*)') }}</option>
                                @foreach ($unitPackageGroups as $packageGroup)
                                    <option value="{{ $packageGroup['id'] }}"
                                        data-width="{{ $packageGroup['unit_width'] }}" data-weight="{{ $packageGroup['unit_weight'] }}"
                                        data-height="{{ $packageGroup['unit_height'] }}" data-length="{{ $packageGroup['unit_length'] }}"
                                        data-packages="{{ json_encode($packageGroup['packages']) }}"
                                    >
                                        {{ $packageGroup['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="unit_group_select_${unitId}_content"></div>

                    ${contentBarcode}
                </div>
            </div>
        `);
    }

    let unitData = {};
    function initOld() {

    }

    function addUnitPackageInfo(id) {
        if (unitData[id] == undefined) {
            return;
        }

        let optionContent = '';
        for(const package of unitData[id]) {
            optionContent += `
                <option value="${package['unit_number']}">Package has ${package['unit_number']} Unit (Max number package = ${package['total']})</option>
            `;
        }

        while ($(`#unit_package_info_${id}_${unitInfoId}`).length) {
            unitInfoId += 1;
        }

        $(`#unit_package_info_${id}`).append(`
            <div id="unit_package_info_${id}_${unitInfoId}">
                <div class="row amb-20 amx-n4">
                    <div class="col-12 col-md-5 col-xl-4 apx-4">
                        <select id="unit_group_select_${id}" name="unit_group[${id}][info][${unitInfoId}][unit_number]" class="form-control">
                            <option value="-1">{{ __('Select Package (*)') }}</option>
                            ${optionContent}
                        </select>
                    </div>

                    <div class="col-12 col-md-5 col-xl-4 apx-4">
                        <input type="number" class="form-control" name="unit_group[${id}][info][${unitInfoId}][package_number]"
                            placeholder="Number Package (*)" step="any" min="1"
                        />
                    </div>

                    <div class="d-flex align-items-center apx-12">
                        <i class="fa fa-close atext-gray-500 font-20 pointer" onclick="deleteUnitPackageInfo(this)"></i>
                    </div>
                </div>
            </div>
        `);
    }

    function deleteUnitPackageInfo(e) {
       $(e).parent().parent().parent().remove();
    }

    let requestTypeSelect;

// ===============

$(document).ready(function () {
    initOld();

    $('#m_request_type_id').on('change', function() {
        $requestType = $(this).children("option:selected").text();
        toggleOption($requestType);
        toggleBarcodeFile($requestType);
        toggleAddPackage($requestType);
        toggleRemoval($requestType);
        toggleReturn($requestType);
        toggleSizeRadio($requestType);
        toggleContent();
    });

    $(document).on('change', '.pg-select', function() {
        const id = $(this).attr('id');
        const data = $(this).children("option:selected").data();

        if (Object.keys(data).length) {
            const unitGroupId = $(this).data('id');
            // unitData[unitGroupId] = data['packages'];

            let packageContent = '';
            if (id.startsWith('unit_group_select')) {
                packageContent = `
                    <div class="amb-20">
                        <b>{{ __('Stored package information') }}</b>
                        <div id="unit_package_info_${unitGroupId}"></div>

                        <button type="button" class="btn btn-secondary apx-24 add-btn" onclick="addUnitPackageInfo('${unitGroupId}')">
                            {{ __('Add') }}
                        </button>
                    </div>
                `;
            }

            $(`#${id}_content`).html(`
                <div class="row amb-20 amx-n4">
                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                        <b>{{ __('Package Group Unit Width') }}</b>
                        <div class="form-control">
                            ${data['width'] ? data['width'] : '' }
                        </div>
                    </div>
                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                        <b>{{ __('Package Group Unit Weight') }}</b>
                        <div class="form-control">
                            ${data['weight'] ? data['weight'] : '' }
                        </div>
                    </div>
                </div>

                <div class="row amb-20 amx-n4">
                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                        <b>{{ __('Package Group Unit Height') }}</b>
                        <div class="form-control">
                            ${data['height'] ? data['height'] : '' }
                        </div>
                    </div>
                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                        <b>{{ __('Package Group Unit Length') }}</b>
                        <div class="form-control">
                            ${data['length'] ? data['length'] : '' }
                        </div>
                    </div>
                </div>

                ${packageContent}
            `);
        } else {
            $(`#${id}_content`).empty();
        }
    });

    $('#file_code_btn').click(function() {
        $('#file_code').click()
    });

    $('#file_code').change(function() {
        $('#selected_filename').text($('#file_code')[0].files[0].name);
    });

    window.addEventListener('load', function () {
        try {
            let selectedDeviceId;
            const codeReader = new window.zxing.BrowserMultiFormatReader();
            codeReader.getVideoInputDevices()
            .then((videoInputDevices) => {
                if (videoInputDevices.length < 1) {
                    console.log('No video devices found');
                    return;
                }
                selectedDeviceId = videoInputDevices[0].deviceId;
                $('#scan-modal').on('hidden.coreui.modal', function (e) {
                    codeReader.reset();
                })

                $(document).on('click', '.group-start-button', function() {
                    let targetId = $(this).data('id');
                    codeReader.decodeFromVideoDevice(selectedDeviceId, 'video', (result, err) => {
                        if (result) {
                            $(`#${targetId}`).val(result.text);
                            $('#scan-modal').modal('hide');
                            codeReader.reset();
                        }
                        if (err && !(err instanceof window.zxing.NotFoundException)) {
                            console.log(err);
                            $('#scan-modal').modal('hide');
                            codeReader.reset();
                        }
                    })
                });
            }).catch((err) => { console.log(err)});

            const qrCodeReader = new window.zxing.BrowserQRCodeReader();
            $(document).on('change', '.file-select', function() {
                const targetId = $(this).data('id');
                $(`#${targetId}`).val('');

                if (this.files && this.files[0]) {
                    $(`#${targetId}_img`).text(this.files[0].name);
                    var reader = new FileReader();
                    const img = document.getElementById('imgshow');

                    reader.onload = function (e) {
                        img.src = e.target.result;
                    }
                    reader.readAsDataURL(this.files[0]);


                    qrCodeReader.decodeFromImage(img).then((result) => {
                        $(`#${targetId}`).val(result.text);
                        console.log(result.text);
                    }).catch((err) => {
                        $(`#${targetId}_error`).val(' (*)Please upload other image');
                        console.error(err)
                    });
                } else {
                    $(`#${targetId}_img`).text('No file selected');
                }
            });

        } catch(err){
            console.log(err)
        }
    });
});
</script>
@endsection --}}

