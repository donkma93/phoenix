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
            'text' => 'Create'
        ]
    ]
])
@endsection


@section('styles')
<style>
.modal-body-scroll {
    max-height: calc(100vh - 210px);
    overflow-y: auto;
}

.img-unit {
    max-height: 250px;
    max-width: 250px;
    overflow: hidden;
    border: 1px solid black;
    margin: 10px;
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

@php
    $oldRequest = old('m_request_type_id');
    $hasOldRequest = isset($requestTypes[$oldRequest]);


    $showChoicePackage = true;
    if (!$hasOldRequest || ($hasOldRequest && !in_array($requestTypes[$oldRequest], ['relabel', 'repack', 'outbound', 'warehouse labor']))) {
        $showChoicePackage = false;
    }

    $showBarcode = true;
    if (!$hasOldRequest || ($hasOldRequest && !in_array($requestTypes[$oldRequest], ['relabel', 'outbound', 'warehouse labor']))) {
        $showBarcode = false;
    }

    $showRadioType = true;
    if (!$hasOldRequest || ($hasOldRequest && !in_array($requestTypes[$oldRequest], ['add package', 'removal', 'return']))) {
        $showRadioType = false;
    }

    $showAddNewPackage = true;
    if (!$hasOldRequest || ($hasOldRequest && $requestTypes[$oldRequest] != "add package")) {
        $showAddNewPackage = false;
    }

    $dataPackageGroup = $dataRemovalGroup = $dataReturnGroup = $dataUnitGroup = [];

    $u = [];
    if (old('unit_group') != null) {
        foreach (old('unit_group') as $groupId => $group) {
            if (isset($unitPackageGroups[$group['id']])) {
                $u[$groupId] = $unitPackageGroups[$group['id']]['packages'];
            }
        }
    }
@endphp



@section('content')
    <div class="fade-in">
        <div class="card">
            <div class="card-header">
               <h2 class="mb-0">Create New Request</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('requests.store') }}" class="form-horizontal" role="form" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group search-form-group">
                        <label for="m_request_type_id" class="search-label col-form-label"><b>{{ __('Type') }}</b></label>
                        <div class="search-input">
                            <select id="m_request_type_id" name="m_request_type_id" class="form-control w-75">
                                <option selected disabled></option>
                                @foreach ($requestTypes as $id => $type)
                                    @if ($type != "outbound" && $type != "add package")
                                        <option value="{{ $id }}"
                                            @if (old('m_request_type_id') == $id)
                                                selected="selected"
                                            @endif
                                        >{{ $type }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @if ($errors->has('m_request_type_id'))
                            <p class="text-danger mb-0">
                                {{ $errors->first('m_request_type_id') }}
                            </p>
                        @endif
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label for="note" class="col-form-label search-label"><b>{{ __('Note') }}</b></label>
                        <div class="search-input">
                            <textarea name="note" id="note" class="form-control">{{ old('note') }}</textarea>
                        </div>
                    </div>

                    <div id="size_radio_option"
                        @if(!$showRadioType)
                            style="display: none;"
                        @endif
                    >
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Size Type') }}</b></label>
                            <div class="search-input search-radio">
                                @foreach (App\Models\UserRequest::$sizeName as $value => $name)
                                    <div class="form-check d-inline-flex mr-3">
                                        <input class="form-check-input"
                                        @if (old('size_type') != null)
                                            {{ old('size_type') == $value ? 'checked' : '' }}
                                        @else
                                            {{ $value == App\Models\UserRequest::SIZE_INCH ? 'checked' : '' }}
                                        @endif
                                        type="radio" name="size_type" id="{{ 'size_type' . $value }}" value="{{ $value }}">
                                        <label class="form-check-label" for="{{ 'size_type' . $value }}">{{ $name }}</label>
                                    </div>
                                @endforeach

                                @if ($errors->has('size_type'))
                                    <p class="text-danger mb-0">
                                        {{ $errors->first('size_type') }}
                                    </p>
                                @endif
                            </div>
                        </div>


                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Weight Type') }}</b></label>
                            <div class="search-input search-radio">
                                @foreach (App\Models\UserRequest::$weightName as $value => $name)
                                    <div class="form-check d-inline-flex mr-3">
                                        <input class="form-check-input"
                                        @if (old('weight_type') != null)
                                            {{ old('weight_type') == $value ? 'checked' : '' }}
                                        @else
                                            {{ $value == App\Models\UserRequest::WEIGHT_POUND ? 'checked' : '' }}
                                        @endif
                                        type="radio" name="weight_type" id="{{ 'weight_type' . $value }}" value="{{ $value }}">
                                        <label class="form-check-label" for="{{ 'weight_type' . $value }}">{{ $name }}</label>
                                    </div>
                                @endforeach
                                @if ($errors->has('weight_type'))
                                    <p class="text-danger mb-0">
                                        {{ $errors->first('weight_type') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div id="new_package_option"
                        @if(!$showAddNewPackage)
                            style="display: none;"
                        @endif
                    >
                        <div id="add_package_option_content">
                            @if (old('package_group') != null)
                                @foreach (old('package_group') as $groupId => $group)
                                    <div class="amb-32 apy-8 addition-form" id="{{ 'package_group_'.$groupId }}">
                                        <div id="{{ 'package_group_form_'.$groupId }}">
                                            <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                                                <h3 class="amb-4">{{ __('Package Group') }}</h3>
                                                <button class="btn btn-danger btn-sm apx-16" onclick="deleteGroup(`{{ 'package_group_' . $groupId }}`)">
                                                    <i class="fa fa-trash font-14"></i>
                                                </button>
                                            </div>

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                    <select id="{{ 'package_group_select_' . $groupId }}" name="{{ 'package_group['. $groupId . '][id]' }}" class="form-control pg-select">
                                                        <option selected>{{ __('Select Package Group (*)') }}</option>
                                                        @foreach ($packageGroups as $packageGroup)
                                                            <option value="{{ $packageGroup['id'] }}"
                                                                data-width="{{ $packageGroup['unit_width'] }}" data-weight="{{ $packageGroup['unit_weight'] }}"
                                                                data-height="{{ $packageGroup['unit_height'] }}" data-length="{{ $packageGroup['unit_length'] }}"
                                                                @if (isset($group['id']) && $group['id'] == $packageGroup['id'])
                                                                    selected="selected"
                                                                    @php
                                                                        $dataPackageGroup[$groupId] = $packageGroup;
                                                                    @endphp
                                                                @endif
                                                            >{{ $packageGroup['name'] }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            @if ($errors->has('package_group.' . $groupId . '.id'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('package_group.' . $groupId . '.id') }}
                                                </p>
                                            @endif
                                            @if ($errors->has('package_group.' . $groupId . '.name'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('package_group.' . $groupId . '.name') }}
                                                </p>
                                            @endif

                                            <div id="{{ 'package_group_select_' . $groupId . '_content' }}">
                                                @if (isset($dataPackageGroup[$groupId]))
                                                    <div class="row amb-20 amx-n4">
                                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                            <b>{{ __('Package Group Unit Width') }}</b>
                                                            <div class="form-control">
                                                                {{ $dataPackageGroup[$groupId]['unit_width'] ?? '' }}
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                            <b>{{ __('Package Group Unit Weight') }}</b>
                                                            <div class="form-control">
                                                                {{ $dataPackageGroup[$groupId]['unit_weight'] ?? '' }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row amb-20 amx-n4">
                                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                            <b>{{ __('Package Group Unit Height') }}</b>
                                                            <div class="form-control">
                                                                {{ $dataPackageGroup[$groupId]['unit_height'] ?? '' }}
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                            <b>{{ __('Package Group Unit Length') }}</b>
                                                            <div class="form-control">
                                                                {{ $dataPackageGroup[$groupId]['unit_length'] ?? '' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="amb-20">
                                                <b>{{ __('Package information') }}</b>
                                                <div id="{{ 'package_info_'. $groupId }}">
                                                    @if(isset($group['info']))
                                                        @foreach ($group['info'] as $k => $v)
                                                            <div id="{{ 'package_info_' . $groupId . '_' . $k }}" class="row amx-n4 amb-8">
                                                                <div class="col-10 col-xl-8 apx-4">
                                                                    <b>{{ __('Package') }}</b>
                                                                </div>

                                                                <div class="col-10 col-xl-8 apx-4">
                                                                    <div class="row amx-n4">
                                                                        <div class="col-12 col-md-6 apx-4 amb-8">
                                                                            <input type="number" class="form-control"
                                                                                name="{{ 'package_group[' . $groupId . '][info][' . $k . '][package_width]' }}"
                                                                                value="{{ $v['package_width'] ?? '' }}" placeholder="Package Width" step="any" min="0" />
                                                                        </div>
                                                                        <div class="col-12 col-md-6 apx-4 amb-8">
                                                                            <input type="number" class="form-control"
                                                                                name="{{ 'package_group[' . $groupId . '][info][' . $k . '][package_weight]' }}"
                                                                                value="{{ $v['package_weight'] ?? '' }}" placeholder="Package Weight" step="any" min="0" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @if ($errors->has('package_group.' . $groupId . '.info.' . $k . '.package_width'))
                                                                    <div class="col-10 col-xl-8 apx-4">
                                                                        <p class="text-danger mb-0">
                                                                            {{ $errors->first('package_group.' . $groupId . '.info.' . $k . '.package_width') }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                                @if ($errors->has('package_group.' . $groupId . '.info.' . $k . '.package_weight'))
                                                                    <div class="col-10 col-xl-8 apx-4">
                                                                        <p class="text-danger mb-0">
                                                                            {{ $errors->first('package_group.' . $groupId . '.info.' . $k . '.package_weight') }}
                                                                        </p>
                                                                    </div>
                                                                @endif

                                                                <div class="col-10 col-xl-8 apx-4">
                                                                    <div class="row amx-n4">
                                                                        <div class="col-12 col-md-6 apx-4 amb-8">
                                                                            <input type="number" class="form-control"
                                                                                name="{{ 'package_group[' . $groupId . '][info][' . $k . '][package_height]' }}"
                                                                                value="{{ $v['package_height'] ?? '' }}" placeholder="Package Height" step="any" min="0" />
                                                                        </div>
                                                                        <div class="col-12 col-md-6 apx-4 amb-8">
                                                                            <input type="number" class="form-control"
                                                                                name="{{ 'package_group[' . $groupId . '][info][' . $k . '][package_length]' }}"
                                                                                value="{{ $v['package_length'] ?? '' }}" placeholder="Package Length" step="any" min="0" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @if ($errors->has('package_group.' . $groupId . '.info.' . $k . '.package_height'))
                                                                    <div class="col-10 col-xl-8 apx-4">
                                                                        <p class="text-danger mb-0">
                                                                            {{ $errors->first('package_group.' . $groupId . '.info.' . $k . '.package_height') }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                                @if ($errors->has('package_group.' . $groupId . '.info.' . $k . '.package_length'))
                                                                    <div class="col-10 col-xl-8 apx-4">
                                                                        <p class="text-danger mb-0">
                                                                            {{ $errors->first('package_group.' . $groupId . '.info.' . $k . '.package_length') }}
                                                                        </p>
                                                                    </div>
                                                                @endif

                                                                <div class="col-10 col-xl-8 apx-4">
                                                                    <div class="row amx-n4">
                                                                        <div class="col-12 col-md-6 apx-4 amb-8">
                                                                            <input type="number" class="form-control"
                                                                                name="{{ 'package_group[' . $groupId . '][info][' . $k . '][unit_number]' }}"
                                                                                value="{{ $v['unit_number'] ?? '' }}" placeholder="Number Unit per Package (*)" step="any" min="0" />
                                                                        </div>
                                                                        <div class="col-12 col-md-6 apx-4 amb-8">
                                                                            <input type="number" class="form-control"
                                                                                name="{{ 'package_group[' . $groupId . '][info][' . $k . '][package_number]' }}"
                                                                                value="{{ $v['package_number'] ?? '' }}" placeholder="Number package (*)" step="any" min="0" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @if ($errors->has('package_group.' . $groupId . '.info.' . $k . '.unit_number'))
                                                                    <div class="col-10 col-xl-8 apx-4">
                                                                        <p class="text-danger mb-0">
                                                                            {{ $errors->first('package_group.' . $groupId . '.info.' . $k . '.unit_number') }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                                @if ($errors->has('package_group.' . $groupId . '.info.' . $k . '.package_number'))
                                                                    <div class="col-10 col-xl-8 apx-4">
                                                                        <p class="text-danger mb-0">
                                                                            {{ $errors->first('package_group.' . $groupId . '.info.' . $k . '.package_number') }}
                                                                        </p>
                                                                    </div>
                                                                @endif

                                                                <div class="col d-flex align-items-center apx-12 amb-8">
                                                                    <i class="fa fa-close atext-gray-500 font-20 pointer line-height-1" onclick="deletePackageInfo(this)"></i>
                                                                </div>

                                                                <div  class="col-10 col-xl-8 apx-4"><hr></div>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>

                                                @if ($errors->has('package_group.' . $groupId . '.info'))
                                                    <p class="text-danger mb-0">
                                                        {{ $errors->first('package_group.' . $groupId . '.info') }}
                                                    </p>
                                                @endif
                                                <button type="button" class="btn btn-secondary apx-24 add-btn" onclick="addPackageInfo({{ $groupId }})">
                                                    {{ __('Add') }}
                                                </button>
                                            </div>

                                            <div class="amb-20">
                                                <b>{{ __('Tracking urls') }}</b>
                                                <div id="{{ 'package_group_track_' . $groupId}}">
                                                    @if(isset($group['tracking_url']) && $group['tracking_url'] != null)
                                                        @foreach ($group['tracking_url'] as $k => $v)
                                                            <div class="row amb-8 amx-n4">
                                                                <div class="col-10 col-xl-8 apx-4">
                                                                    <input type="text" class="form-control" name="{{ 'package_group[' . $groupId . '][tracking_url][]' }}"
                                                                        placeholder="Tracking url" value="{{ $v ?? '' }}"
                                                                    />
                                                                </div>
                                                                <div class="d-flex align-items-center apx-12">
                                                                    <i class="fa fa-close atext-gray-500 font-20 pointer" onclick="deletePackage(this)"></i>
                                                                </div>
                                                            </div>
                                                            @if ($errors->has('package_group.' . $groupId . '.tracking_url.' . $k))
                                                                <p class="text-danger mb-0">
                                                                    {{ $errors->first('package_group.' . $groupId . '.tracking_url.' . $k) }}
                                                                </p>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </div>

                                                <button type="button" class="btn btn-secondary apx-24 add-btn" onclick="addTrack('package_group', {{ $groupId }})">
                                                    {{ __('Add') }}
                                                </button>
                                            </div>

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <input type="file" accept="image/*" hidden id="{{ 'file_unit_upload_pg_' . $groupId }}" name="{{ 'package_group[' . $groupId . '][file_unit][]' }}" multiple
                                                        class="btn-primary form-control" onchange="displayFileName(`{{ 'file_unit_upload_pg_' . $groupId }}`, `{{ 'file_unit_selected_pg_' . $groupId }}`)">
                                                    <div class="btn btn-info w-100" onclick="uploadImage(`{{ 'file_unit_upload_pg_' . $groupId }}`)"> Upload unit image</div>
                                                    <span id="{{ 'file_unit_selected_pg_' . $groupId }}">No file selected</span>
                                                    <div id="{{ 'file_unit_error_pg_' . $groupId }}" class="text-danger mb-0"></div>
                                                    <div id="{{ "file_unit_selected_pg_{$groupId}_content" }}"></div>
                                                </div>
                                            </div>
                                            @if ($errors->has('package_group.' . $groupId . '.file_unit'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('package_group.' . $groupId . '.file_unit') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            @if (old('new_package_group') != null)
                                @foreach (old('new_package_group') as $groupId => $group)
                                    <div class="amb-32 apy-8 addition-form" id="{{ 'new_package_group_'.$groupId }}">
                                        <div id="{{ 'new_package_group_form_'.$groupId }}">
                                            <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                                                <h3 class="amb-4">{{ __('New Package Group') }}</h3>
                                                <button class="btn btn-danger btn-sm apx-16" onclick="deleteGroup(`{{ 'new_package_group_' . $groupId }}`)">
                                                    <i class="fa fa-trash font-14"></i>
                                                </button>
                                            </div>

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                    <input type="text" class="form-control" name="{{ 'new_package_group[' . $groupId . '][name]' }}"
                                                        placeholder="Package Group Name (*)" value="{{ $group['name'] ?? '' }}"
                                                    />
                                                </div>
                                            </div>
                                            @if ($errors->has('new_package_group.' . $groupId . '.name'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('new_package_group.' . $groupId . '.name') }}
                                                </p>
                                            @endif

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <b>{{ __('Package Group Unit Width') }}</b>
                                                    <input type="number" class="form-control" name="{{ 'new_package_group[' . $groupId . '][unit_width]' }}" placeholder="Package Group Unit Width" value="{{ $group['unit_width'] ?? '' }}" step="any" min="0"
                                                    />
                                                    @if ($errors->has('new_package_group.' . $groupId . '.unit_width'))
                                                        <p class="text-danger mb-0">
                                                            {{ $errors->first('new_package_group.' . $groupId . '.unit_width') }}
                                                        </p>
                                                    @endif
                                                </div>
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <b>{{ __('Package Group Unit Weight') }}</b>
                                                    <input type="number" class="form-control" name="{{ 'new_package_group[' . $groupId . '][unit_weight]' }}" placeholder="Package Group Unit Weight" value="{{ $group['unit_weight'] ?? '' }}" step="any" min="0"
                                                    />
                                                    @if ($errors->has('new_package_group.' . $groupId . '.unit_weight'))
                                                        <p class="text-danger mb-0">
                                                            {{ $errors->first('new_package_group.' . $groupId . '.unit_weight') }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <b>{{ __('Package Group Unit Height') }}</b>
                                                    <input type="number" class="form-control" name="{{ 'new_package_group[' . $groupId . '][unit_height]' }}" placeholder="Package Group Unit Height" value="{{ $group['unit_height'] ?? '' }}" step="any" min="0"
                                                    />
                                                    @if ($errors->has('new_package_group.' . $groupId . '.unit_height'))
                                                        <p class="text-danger mb-0">
                                                            {{ $errors->first('new_package_group.' . $groupId . '.unit_height') }}
                                                        </p>
                                                    @endif
                                                </div>
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <b>{{ __('Package Group Unit Length') }}</b>
                                                    <input type="number" class="form-control" name="{{ 'new_package_group[' . $groupId . '][unit_length]' }}" placeholder="Package Group Unit Length" value="{{ $group['unit_length'] ?? '' }}" step="any" min="0"
                                                    />
                                                    @if ($errors->has('new_package_group.' . $groupId . '.unit_length'))
                                                        <p class="text-danger mb-0">
                                                            {{ $errors->first('new_package_group.' . $groupId . '.unit_length') }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="amb-20">
                                                <b>{{ __('Package information') }}</b>
                                                <div id="{{ 'new_package_info_'. $groupId }}">
                                                    @if(isset($group['info']))
                                                        @foreach ($group['info'] as $k => $v)
                                                            <div id="{{ 'new_package_info_' . $groupId . '_' . $k }}" class="row amx-n4 amb-8">
                                                                <div class="col-10 col-xl-8 apx-4">
                                                                    <b>{{ __('Package') }}</b>
                                                                </div>

                                                                <div class="col-10 col-xl-8 apx-4">
                                                                    <div class="row amx-n4">
                                                                        <div class="col-12 col-md-6 apx-4 amb-8">
                                                                            <input type="number" class="form-control"
                                                                                name="{{ 'new_package_group[' . $groupId . '][info][' . $k . '][package_width]' }}"
                                                                                value="{{ $v['package_width'] ?? '' }}" placeholder="Package Width" step="any" min="0" />
                                                                        </div>
                                                                        <div class="col-12 col-md-6 apx-4 amb-8">
                                                                            <input type="number" class="form-control"
                                                                                name="{{ 'new_package_group[' . $groupId . '][info][' . $k . '][package_weight]' }}"
                                                                                value="{{ $v['package_weight'] ?? '' }}" placeholder="Package Weight" step="any" min="0" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-10 col-xl-8 apx-4">
                                                                    @if ($errors->has('new_package_group.' . $groupId . '.info.' . $k . '.package_width'))
                                                                        <p class="text-danger mb-0">
                                                                            {{ $errors->first('new_package_group.' . $groupId . '.info.' . $k . '.package_width') }}
                                                                        </p>
                                                                    @endif
                                                                    @if ($errors->has('new_package_group.' . $groupId . '.info.' . $k . '.package_weight'))
                                                                        <p class="text-danger mb-0">
                                                                            {{ $errors->first('new_package_group.' . $groupId . '.info.' . $k . '.package_weight') }}
                                                                        </p>
                                                                    @endif
                                                                </div>

                                                                <div class="col-10 col-xl-8 apx-4">
                                                                    <div class="row amx-n4">
                                                                        <div class="col-12 col-md-6 apx-4 amb-8">
                                                                            <input type="number" class="form-control"
                                                                                name="{{ 'new_package_group[' . $groupId . '][info][' . $k . '][package_height]' }}"
                                                                                value="{{ $v['package_height'] ?? '' }}" placeholder="Package Height" step="any" min="0" />
                                                                        </div>
                                                                        <div class="col-12 col-md-6 apx-4 amb-8">
                                                                            <input type="number" class="form-control"
                                                                                name="{{ 'new_package_group[' . $groupId . '][info][' . $k . '][package_length]' }}"
                                                                                value="{{ $v['package_length'] ?? '' }}" placeholder="Package Length" step="any" min="0" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-10 col-xl-8 apx-4">
                                                                    @if ($errors->has('new_package_group.' . $groupId . '.info.' . $k . '.package_height'))
                                                                        <p class="text-danger mb-0">
                                                                            {{ $errors->first('new_package_group.' . $groupId . '.info.' . $k . '.package_height') }}
                                                                        </p>
                                                                    @endif
                                                                    @if ($errors->has('new_package_group.' . $groupId . '.info.' . $k . '.package_length'))
                                                                        <p class="text-danger mb-0">
                                                                            {{ $errors->first('new_package_group.' . $groupId . '.info.' . $k . '.package_length') }}
                                                                        </p>
                                                                    @endif
                                                                </div>

                                                                <div class="col-10 col-xl-8 apx-4">
                                                                    <div class="row amx-n4">
                                                                        <div class="col-12 col-md-6 apx-4 amb-8">
                                                                            <input type="number" class="form-control"
                                                                                name="{{ 'new_package_group[' . $groupId . '][info][' . $k . '][unit_number]' }}"
                                                                                value="{{ $v['unit_number'] ?? '' }}" placeholder="Number Unit per Package (*)" step="any" min="0" />
                                                                        </div>
                                                                        <div class="col-12 col-md-6 apx-4 amb-8">
                                                                            <input type="number" class="form-control"
                                                                                name="{{ 'new_package_group[' . $groupId . '][info][' . $k . '][package_number]' }}"
                                                                                value="{{ $v['package_number'] ?? '' }}" placeholder="Number package (*)" step="any" min="0" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-10 col-xl-8 apx-4">
                                                                    @if ($errors->has('new_package_group.' . $groupId . '.info.' . $k . '.unit_number'))
                                                                        <p class="text-danger mb-0">
                                                                            {{ $errors->first('new_package_group.' . $groupId . '.info.' . $k . '.unit_number') }}
                                                                        </p>
                                                                    @endif
                                                                    @if ($errors->has('new_package_group.' . $groupId . '.info.' . $k . '.package_number'))
                                                                        <p class="text-danger mb-0">
                                                                            {{ $errors->first('new_package_group.' . $groupId . '.info.' . $k . '.package_number') }}
                                                                        </p>
                                                                    @endif
                                                                </div>

                                                                <div class="col d-flex align-items-center apx-12 amb-8">
                                                                    <i class="fa fa-close atext-gray-500 font-20 pointer line-height-1" onclick="deletePackageInfo(this)"></i>
                                                                </div>

                                                                <div  class="col-10 col-xl-8 apx-4"><hr></div>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>

                                                @if ($errors->has('new_package_group.' . $groupId . '.info'))
                                                    <p class="text-danger mb-0">
                                                        {{ $errors->first('new_package_group.' . $groupId . '.info') }}
                                                    </p>
                                                @endif
                                                <button type="button" class="btn btn-secondary apx-24 add-btn" onclick="addNewPackageInfo({{ $groupId }})">
                                                    {{ __('Add') }}
                                                </button>
                                            </div>

                                            <div class="amb-20">
                                                <b>{{ __('Tracking urls') }}</b>
                                                <div  id="{{ 'new_package_group_track_' . $groupId}}">
                                                    @if (isset($group['tracking_url']) && $group['tracking_url'] != null)
                                                        @foreach ($group['tracking_url'] as $k => $v)
                                                            <div class="row amb-8 amx-n4">
                                                                <div class="col-10 col-xl-8 apx-4">
                                                                    <input type="text" class="form-control" name="{{ 'new_package_group[' . $groupId . '][tracking_url][]' }}"
                                                                        placeholder="Tracking url" value="{{ $v ?? '' }}"
                                                                    />
                                                                </div>
                                                                <div class="d-flex align-items-center apx-12">
                                                                    <i class="fa fa-close atext-gray-500 font-20 pointer" onclick="deletePackage(this)"></i>
                                                                </div>
                                                            </div>
                                                            @if ($errors->has('new_package_group.' . $groupId . '.tracking_url.' . $k))
                                                                <p class="text-danger mb-0">
                                                                    {{ $errors->first('new_package_group.' . $groupId . '.tracking_url.' . $k) }}
                                                                </p>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </div>

                                                <button type="button" class="btn btn-secondary apx-24 add-btn" onclick="addTrack('new_package_group', {{ $groupId }})">
                                                    {{ __('Add') }}
                                                </button>
                                            </div>

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <input id="{{ 'new_package_group_file_barcode_' . $groupId }}" type="file" hidden
                                                        class="file-select"
                                                        data-id="{{ 'new_package_group_barcode_' . $groupId }}"
                                                        name="{{ 'new_package_group['. $groupId . '][file_barcode]' }}" accept="image/*,application/pdf"
                                                    >
                                                    <div id="{{ 'new_package_group_barcode_' . $groupId . '_img' }}">No file selected</div>
                                                    <div class="btn btn-info w-100" onclick="uploadBarcodeImage(`{{ 'new_package_group_file_barcode_' . $groupId }}`)">
                                                        Upload New Package Group QR code file
                                                    </div>
                                                    <div id="{{ 'new_package_group_barcode_' . $groupId . '_error' }}" class="text-danger mb-0"></div>
                                                </div>
                                            </div>

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <input id="{{ 'new_package_group_barcode_' . $groupId }}" type="text" class="form-control w-100"
                                                        name="{{ 'new_package_group['. $groupId . '][barcode]' }}" placeholder="New Package Group QR code" value="{{ $group['barcode'] ?? '' }}">
                                                    <button type="button"  class="btn scan-btn apy-4 group-start-button" data-id="{{ 'new_package_group_barcode_' . $groupId }}" data-toggle="modal" data-target="#scan-modal">
                                                        <i class="fa fa-qrcode font-20"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @if ($errors->has('new_package_group.' . $groupId . '.barcode'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('new_package_group.' . $groupId . '.barcode') }}
                                                </p>
                                            @endif

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <input type="file" accept="image/*" hidden id="{{ 'file_unit_upload_' . $groupId }}" name="{{ 'new_package_group[' . $groupId . '][file_unit][]' }}" multiple
                                                        class="btn-primary form-control" onchange="displayFileName(`{{ 'file_unit_upload_' . $groupId }}`, `{{ 'file_unit_selected_' . $groupId }}`)">
                                                    <div class="btn btn-info w-100" onclick="uploadImage(`{{ 'file_unit_upload_' . $groupId }}`)"> Upload unit image</div>
                                                    <span id="{{ 'file_unit_selected_' . $groupId }}">No file selected</span>
                                                    <div id="{{ 'file_unit_error_' . $groupId }}" class="text-danger mb-0"></div>
                                                    <div id="{{ "file_unit_selected_{$groupId}_content" }}"></div>
                                                </div>
                                            </div>
                                            @if ($errors->has('new_package_group.' . $groupId . '.file_unit'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('new_package_group.' . $groupId . '.file_unit') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <div class="form-group search-form-group">
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
                        </div>
                    </div>

                    <div id="removal_option"
                        @if(!isset($requestTypes[old('m_request_type_id')]) || $requestTypes[old('m_request_type_id')] != "removal")
                            style="display: none;"
                        @endif
                    >
                        <div id="removal_option_content">
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
                                                    <div id="{{ "removal_file_unit_selected_{$groupId}_content" }}"></div>
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
                                                    <div id="{{ "removal_new_file_unit_selected_{$groupId}_content" }}"></div>
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
                    </div>

                    <div id="return_option"
                        @if(!isset($requestTypes[old('m_request_type_id')]) || $requestTypes[old('m_request_type_id')] != "return")
                            style="display: none;"
                        @endif
                    >
                        <div id="return_option_content">
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
                                                    <div id="{{ "return_file_unit_selected_{$groupId}_content" }}"></div>
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
                                                    <div id="{{ "return_new_file_unit_selected_{$groupId}_content" }}"></div>
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
                    </div>

                    <div id="other_option"
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
                    </div>

                    <div id="unit_package_option"
                        @if(!$showChoicePackage)
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
                    </div>

                    <div class="search-form-group">
                        <div class="search-label d-none d-sm-block"></div>
                        <div class="search-input text-center text-sm-left">
                            <button class="btn btn-primary" type="submit">{{ __('Create') }}</button>
                        </div>
                    </div>
                </form>
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

    <!-- QR code -->
    <img id="imgshow" height="200" hidden>
@endsection

@section('scripts')
<script>
    let id = 0;
    let npgId = 0;
    let pInfo = npInfo = 0;
    let removalId = newRemovalId = 0;
    let returnId = newReturnId = 0;
    let unitId = unitInfoId = 0;
    const maxFileNumber = 3;

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
                            <div id="file_unit_selected_pg_${id}_content"></div>
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

    function deletePackageInfo(e) {
        $(e).parent().parent().remove()
    }

    function deleteGroup(id) {
       $(`#${id}`).remove();
    }

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
                            <div id="file_unit_selected_${npgId}_content"></div>
                        </div>
                    </div>
                </div>
            </div>
        `);
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

    function loadFile(event, npgId, prefix = '') {
        // let Quagga = window.quagga;

        // Quagga.decodeSingle({
        //     src: URL.createObjectURL(event.target.files[0]),
        //      decoder: {
        //         readers: [
        //             { format: 'code_128_reader', config: {} },
        //             { format: 'ean_reader', config: {} },
        //             { format: 'ean_8_reader', config: {} },
        //             { format: 'codabar_reader', config: {} },
        //             { format: 'i2of5_reader', config: {} },
        //             { format: '2of5_reader', config: {} },
        //             { format: 'code_93_reader', config: {} },
        //             { format: 'code_39_reader', config: {} }
        //         ]
        //     },
        //     inputStream: {
        //         size: 1280
        //     },
        //     locate: true,
        // }, function(result) {
        //     console.log(result);
        //     if(result && result.codeResult) {
        //         $(`#${prefix}barcode_display_${npgId}`).text(result.codeResult.code);
        //         $(`#${prefix}barcode_${npgId}`).val(result.codeResult.code);
        //         $(`#${prefix}span_error_${npgId}`).text("");
        //     } else {
        //         $(`#${prefix}span_error_${npgId}`).text(" (*)Please upload other image");
        //         $(`#${prefix}barcode_display_${npgId}`).text('');
        //         $(`#${prefix}barcode_${npgId}`).val('');
        //     }
        // });
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
                            <div id="removal_file_unit_selected_${removalId}_content"></div>
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
                            <div id="removal_new_file_unit_selected_${newRemovalId}_content"></div>
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
                            <div id="return_file_unit_selected_${returnId}_content"></div>
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
                            <div id="return_new_file_unit_selected_${newReturnId}_content"></div>
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
        const content = `${target}_content`;
        $(`#${content}`).empty();

        if (fileNumber > maxFileNumber) {
            alert(`You can select max ${maxFileNumber} images`);
            // $(`#${input}`).val('');
            $(`#${target}`).text(`You can select max ${maxFileNumber} images`);
        } else {
            // $(`#${target}`).text($(`#${input}`)[0].files[0].name);
            $(`#${target}`).text(`You have selected ${fileNumber} images`);

            for (i = 0; i < fileNumber; i++) {
                let img = $(`
                    <img id="#${content}_${i}"
                        class="img-unit"
                    >
                `);

                img.attr('src', URL.createObjectURL($(`#${input}`)[0].files[i]));
                img.appendTo(`#${content}`);
                img.onload = function() {
                    URL.revokeObjectURL(img.src) // free memory
                }
            }
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
        unitData = {!! json_encode($u) !!};
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

    $(document).ready(function () {
        initOld();

        $('#m_request_type_id').on('change', function() {
            requestTypeSelect = $(this).children("option:selected").text();
            $requestType = $(this).children("option:selected").text();
            toggleOption($requestType);
            toggleBarcodeFile($requestType);
            toggleAddPackage($requestType);
            toggleRemoval($requestType);
            toggleReturn($requestType);
            toggleSizeRadio($requestType);
            toggleContent();
        });

        // $(document).on('change', '.file-select', function(event) {
        //     const id = $(this).data('id');;

        //     console.log(event.target.files[0]);
        //     alert(event.target.files[0]);

        //     let Quagga = window.quagga;

        //     Quagga.decodeSingle({
        //         src: URL.createObjectURL(event.target.files[0]),
        //         decoder: {
        //             readers: [
        //                 { format: 'code_128_reader', config: {} },
        //                 { format: 'ean_reader', config: {} },
        //                 { format: 'ean_8_reader', config: {} },
        //                 { format: 'codabar_reader', config: {} },
        //                 { format: 'i2of5_reader', config: {} },
        //                 { format: '2of5_reader', config: {} },
        //                 { format: 'code_93_reader', config: {} },
        //                 { format: 'code_39_reader', config: {} }
        //             ]
        //         },
        //         inputStream: {
        //             size: 1280
        //         },
        //         locate: true,
        //     }, function(result) {
        //         if(result && result.codeResult) {
        //             $(`#${id}`).val(result.codeResult.code);
        //             $(`#${id}_error`).text("");
        //         } else {
        //             $(`#${id}`).val('');
        //             $(`#${id}_error`).text(" (*)Please upload other image");
        //         }
        //     });
        // });

        $(document).on('change', '.pg-select', function() {
            const id = $(this).attr('id');
            const data = $(this).children("option:selected").data();

            if (Object.keys(data).length) {
                const unitGroupId = $(this).data('id');
                unitData[unitGroupId] = data['packages'];

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
        })
    });
</script>
@endsection
