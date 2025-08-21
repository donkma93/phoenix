@extends('layouts.user')

@section('breadcrumb')
    @include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('dashboard')
        ],
        [
            'text' => 'Request Outbound',
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
    $u = [];
    foreach ($unitPackageGroups as $group) {
        $u[$group['id']] = $unitPackageGroups[$group['id']]['packages'];
    }
@endphp

@section('content')
    <div class="fade-in">
        <div class="card">
            <div class="card-header">
               <h2 class="mb-0">Create Outbound</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('requests.outbound.store') }}" class="form-horizontal" role="form" enctype="multipart/form-data">
                    @csrf

                    @php
                        $groups = old('group', []);
                        $dataUnitGroup = [];
                    @endphp

                    <div id="content">
                        @foreach ($groups as $id => $group)
                            <div id="{{ "group_{$id}" }}" class="amb-32 apy-8 addition-form">
                                <div id="{{ "group_{$id}_form" }}">
                                    <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                                        <h3 class="amb-4">{{ __('Package Group') }}</h3>
                                        <button class="btn btn-danger btn-sm apx-16" onclick='deleteGroup(`{{ "group_{$id}" }}`)'>
                                            <i class="fa fa-trash font-14"></i>
                                        </button>
                                    </div>

                                    <div class="row amb-20 amx-n4">
                                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                                            <select id="{{ "group_{$id}_id" }}" name="{{ "group[{$id}][id]" }}" data-id="{{ "{$id}" }}" class="form-control pg-select">
                                                <option selected>{{ __('Select Package Group (*)') }}</option>
                                                @foreach ($unitPackageGroups as $packageGroup)
                                                    <option value="{{ $packageGroup['id'] }}" data-id="{{ $packageGroup['id'] }}"
                                                        data-width="{{ $packageGroup['unit_width'] }}" data-weight="{{ $packageGroup['unit_weight'] }}"
                                                        data-height="{{ $packageGroup['unit_height'] }}" data-length="{{ $packageGroup['unit_length'] }}"
                                                        @if (isset($group['id']) && $group['id'] == $packageGroup['id'])
                                                            selected="selected"
                                                            @php
                                                                $dataUnitGroup[$id] = $packageGroup;
                                                            @endphp
                                                        @endif
                                                    >{{ $packageGroup['name'] }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has("group.{$id}.id"))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first("group.{$id}.id") }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    <div id="{{ "group_{$id}_id_info" }}">
                                        @if (isset($dataUnitGroup[$id]))
                                            <div id="{{ "unit_package_info_{$id}" }}">
                                                <div class="row amb-20 amx-n4">
                                                    <div style="margin-left:15px;" class="col-12 col-md-5 col-xl-4 apx-4">
                                                        <select id="{{ "unit_group_select_{$id}" }}" name="{{ "group[{$id}][unit_number]" }}" class="form-control">
                                                            <option value="-1">{{ __('Select Package (*)') }}</option>
                                                            @foreach ($u[$group['id']] as $kp => $package)
                                                                <option value="{{ $package['unit_number'] }}"
                                                                    @if (isset($package['unit_number']) && $group['unit_number'] == $package['unit_number'])
                                                                        selected="selected"
                                                                    @endif
                                                                >
                                                                    {{ 'Package has ' . $package['unit_number'] . ' Unit (Max number package = ' . $package['total']. ')' }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @if ($errors->has("group.{$id}.unit_number"))
                                                            <p class="text-danger mb-0">
                                                                {{ $errors->first("group.{$id}.unit_number") }}
                                                            </p>
                                                        @endif
                                                    </div>

                                                    <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                        <input type="number" class="form-control" name="{{ "group[{$id}][package_number]" }}"
                                                            placeholder="Number Package (*)" step="any" min="1" value="{{ $group['package_number'] ?? '' }}"
                                                        />
                                                        @if ($errors->has("group.{$id}.package_number"))
                                                            <p class="text-danger mb-0">
                                                                {{ $errors->first("group.{$id}.package_number") }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="row amb-20 amx-n4">
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                            <select id="{{ "group_{$id}_ship_mode" }}" name="{{ "group[{$id}][ship_mode]" }}" data-id="{{ "{$id}" }}" class="form-control ship-select">
                                                @foreach (\App\Models\UserRequest::$shipModes as $value => $name)
                                                    <option value="{{ $value }}"
                                                        @if (isset($group['ship_mode']) && $group['ship_mode'] == $value)
                                                            selected="selected"
                                                        @endif
                                                    >
                                                        {{ $name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has("group.{$id}.ship_mode"))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first("group.{$id}.ship_mode") }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    @php
                                        $isSP = isset($group['ship_mode']) && $group['ship_mode'] == App\Models\UserRequest::SMALL_PARCEL;
                                    @endphp

                                    <div id="{{ "group_{$id}_insurance_content" }}" style="{{ $isSP ? "" : "display: none;" }}">
                                        <div class="row amb-20 amx-n4">
                                            <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8 d-flex">
                                                <label for="{{ "group_{$id}_is_insurance" }}" class="col-form-label search-label"><b>{{ __('Buy Insurance') }}</b></label>
                                                <div class="search-input">
                                                    <input style="margin: 10px 0px 0px 10px;" id="{{ "group_{$id}_is_insurance" }}" type="checkbox"
                                                        name="{{ "group[{$id}][is_insurance]" }}"/>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row amb-20 amx-n4">
                                            <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                <b>{{ __('Insurance Fee: ') }}</b>
                                                <span id="{{ "group_{$id}_insurance_fee_preview" }}"> </span>
                                                <input id="group_${id}_insurance_fee" type="number" min="0" placeholder="Insurance Fee 7%"
                                                    onChange="displayFee($(this).val(), `{{ "group_{$id}_insurance_fee_preview" }}`);"
                                                    class="form-control w-100" name="{{ "group[{$id}][insurance_fee]" }}" value="{{ $group['insurance_fee'] ?? '' }}"
                                                >
                                                @if ($errors->has("group.{$id}.insurance_fee"))
                                                    <p class="text-danger mb-0">
                                                        {{ $errors->first("group.{$id}.insurance_fee") }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div id="{{ "group_{$id}_pallet_content" }}" style="{{ !$isSP ? "" : "display: none;" }}" class="row amb-20 amx-n4">
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                            <b>{{ __('Pallet') }}</b>
                                            <input id="{{ "group_{$id}_pallet" }}" type="number" min="0" placeholder="Number Pallet"
                                                class="form-control w-100" name="{{ "group[{$id}][pallet]" }}" value="{{ $group['pallet'] ?? '' }}"
                                            >
                                            @if ($errors->has("group.{$id}.pallet"))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first("group.{$id}.pallet") }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row amb-20 amx-n4">
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                            <input id="{{ "group_{$id}_file_barcode" }}" type="file" hidden
                                                class="file-select"
                                                data-id="{{ "group_{$id}_barcode" }}"
                                                name="{{ "group[{$id}][file]" }}" accept="image/*,application/pdf"
                                            >
                                            @if ($errors->has("group.{$id}.file"))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first("group.{$id}.file") }}
                                                </p>
                                            @endif

                                            <div id="{{ "group_{$id}_barcode_img" }}">No file selected</div>
                                            <div class="btn btn-info w-100" onclick='uploadBarcodeImage(`{{ "group_{$id}_file_barcode" }}`)'>
                                                Create QR code by file
                                            </div>
                                            <div id="{{ "group_{$id}_file_barcode_error" }}" class="text-danger mb-0"></div>
                                        </div>
                                    </div>

                                    <div class="row amb-20 amx-n4">
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                            <input id="{{ "group_{$id}_barcode" }}" type="text" placeholder="Create QR code by Scan"
                                                class="form-control w-100" name="{{ "group[{$id}][barcode]" }}"
                                            >
                                            @if ($errors->has("group.{$id}.barcode"))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first("group.{$id}.barcode") }}
                                                </p>
                                            @endif
                                            <button type="button"  class="btn scan-btn apy-4 group-start-button" data-id="{{ "group_{$id}_barcode" }}" data-toggle="modal" data-target="#scan-modal">
                                                <i class="fa fa-qrcode font-20"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="search-form-group">
                        <div class="search-label d-none d-sm-block"></div>

                        @if ($errors->has('group'))
                            <p class="text-danger mb-0">
                                {{ $errors->first('group') }}
                            </p>
                        @endif
                    </div>

                    <div class="search-form-group">
                        <div class="search-label d-none d-sm-block"></div>
                        <button type="button" class="btn btn-secondary apx-16 amr-8" onclick="addGroup()">
                            {{ __('Add Group') }}
                        </button>
                        <div class="search-input text-center text-sm-left">
                            <button class="btn btn-primary" type="submit">{{ __('Create Request') }}</button>
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
    function uploadBarcodeImage(targetId) {
        $(`#${targetId}`).click();
    }

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

    let unitData = {};
    function initOld() {
        unitData = {!! json_encode($u) !!};
    }

    $(document).ready(function () {
        initOld();

        $(document).on('change', '.ship-select', function() {
            const groupId = $(this).data('id');
            const data = $(this).children("option:selected").val();

            const insuranceId = `#group_${groupId}_insurance_content`;
            const palletId = `#group_${groupId}_pallet_content`;

            if (data == 0) {
                $(insuranceId).show();
                $(palletId).hide();
            } else {
                $(insuranceId).hide();
                $(palletId).show();
            }
        });

        $(document).on('change', '.pg-select', function() {
            const id = $(this).attr('id');
            const groupId = $(this).data('id');
            const data = $(this).children("option:selected").data();


            if (Object.keys(data).length) {
                const unitGroupId = data['id'];
                const target = `${id}_info`;
                $(`#${target}`).empty();

                if (unitData[unitGroupId] == undefined) {
                    return;
                }

                let optionContent = '';
                for(const package of unitData[unitGroupId]) {
                    optionContent += `
                        <option value="${package['unit_number']}">Package has ${package['unit_number']} Unit (Max number package = ${package['total']})</option>
                    `;
                }

                let unitInfoId = 0;

                $(`#${target}`).append(`
                    <div id="unit_package_info_${id}">
                        <div class="row amb-20 amx-n4">
                            <div style="margin-left:15px;" class="col-12 col-md-5 col-xl-4 apx-4">
                                <select id="unit_group_select_${id}" name="group[${groupId}][unit_number]" class="form-control">
                                    <option value="-1">{{ __('Select Package (*)') }}</option>
                                    ${optionContent}
                                </select>
                            </div>

                            <div class="col-12 col-md-5 col-xl-4 apx-4">
                                <input type="number" class="form-control" name="group[${groupId}][package_number]"
                                    placeholder="Number Package (*)" step="any" min="1"
                                />
                            </div>
                        </div>
                    </div>
                `);
            }
        });
    });

    let id = 0;

    function addGroup() {
        while ($(`#group_${id}`).length) {
            id += 1;
        }

        $(`#content`).append(`
            <div id="group_${id}" class="amb-32 apy-8 addition-form">
                <div id="group_{$id}_form">
                    <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                        <h3 class="amb-4">{{ __('Package Group') }}</h3>
                        <button class="btn btn-danger btn-sm apx-16" onclick='deleteGroup("group_${id}")'>
                            <i class="fa fa-trash font-14"></i>
                        </button>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <select id="group_${id}_id" name="group[${id}][id]" data-id="${id}" class="form-control pg-select">
                                <option selected>{{ __('Select Package Group (*)') }}</option>
                                @foreach ($unitPackageGroups as $packageGroup)
                                    <option value="{{ $packageGroup['id'] }}" data-id="{{ $packageGroup['id'] }}"
                                        data-width="{{ $packageGroup['unit_width'] }}" data-weight="{{ $packageGroup['unit_weight'] }}"
                                        data-height="{{ $packageGroup['unit_height'] }}" data-length="{{ $packageGroup['unit_length'] }}"
                                    >{{ $packageGroup['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="group_${id}_id_info">
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <select id="group_${id}_ship_mode" name="group[${id}][ship_mode]" data-id="${id}" class="form-control ship-select">
                                @foreach (\App\Models\UserRequest::$shipModes as $value => $name)
                                    <option value="{{ $value }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="group_${id}_insurance_content">
                        <div class="row amb-20 amx-n4">
                            <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8 d-flex">
                                <label for="group_${id}_is_insurance" class="col-form-label search-label"><b>{{ __('Buy Insurance') }}</b></label>
                                <div class="search-input">
                                    <input style="margin: 10px 0px 0px 10px;" id="group_${id}_is_insurance" type="checkbox" name="group[${id}][is_insurance]"/>
                                </div>
                            </div>
                        </div>

                        <div class="row amb-20 amx-n4">
                            <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                <b>{{ __('Insurance Fee: ') }}</b>
                                <span id="group_${id}_insurance_fee_preview"> </span>
                                <input id="group_${id}_insurance_fee" type="number" min="0" placeholder="Insurance Fee 7%"
                                    onChange="displayFee($(this).val(), 'group_${id}_insurance_fee_preview');"
                                    class="form-control w-100" name="group[${id}][insurance_fee]"
                                >
                            </div>
                        </div>
                    </div>

                    <div id="group_${id}_pallet_content" style="display: none;" class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <b>{{ __('Pallet') }}</b>
                            <input id="group_${id}_pallet" type="number" min="0" placeholder="Number Pallet"
                                class="form-control w-100" name="group[${id}][pallet]"
                            >
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <input id="group_${id}_file_barcode" type="file" hidden
                                class="file-select"
                                data-id="group_${id}_barcode"
                                name="group[${id}][file]" accept="image/*,application/pdf"
                            >

                            <div id="group_${id}_barcode_img">No file selected</div>
                            <div class="btn btn-info w-100" onclick='uploadBarcodeImage("group_${id}_file_barcode")'>
                                Create QR code by file
                            </div>
                            <div id="group_${id}_file_barcode_error" class="text-danger mb-0"></div>
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <input id="group_${id}_barcode" type="text" placeholder="Create QR code by Scan"
                                class="form-control w-100" name="group[${id}][barcode]"
                            >
                            <button type="button"  class="btn scan-btn apy-4 group-start-button" data-id="group_${id}_barcode" data-toggle="modal" data-target="#scan-modal">
                                <i class="fa fa-qrcode font-20"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `);
    }

    function deleteGroup(id) {
       $(`#${id}`).remove();
    }

    function displayFee(rawVal, id) {
        const invalidMsg = 'Invalid Fee';

        try {
            const val = rawVal / 100 * 7;
            const content = val == NaN || val < 0 ? invalidMsg : val.toFixed(2);

            $(`#${id}`).text(content);
        } catch {
            $(`#${id}`).text(invalidMsg);
        }
    }
</script>
@endsection
