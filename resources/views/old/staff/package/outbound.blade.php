@extends('layouts.staff')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('staff.dashboard')
        ],
        [
            'text' => 'Package',
            'url' => route('staff.package.list')
        ],
        [
            'text' => 'Outbound'
        ]
    ]
])
@endsection

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-header">
            <h2 class="mb-0">{{ __('Package outbound') }}</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('staff.package.outbound') }}" method="get" enctype="multipart/form-data">
                <div class="form-group search-form-group">
                    <label for="type" class="col-form-label search-label"><b>{{ __('Email') }}</b></label>
                    <div class="search-input position-relative">
                        <input type="input" class="form-control w-100" id="email-input" list="dropdown-email" name="email" value="@if (isset($packagesRes['oldInput']['email'])){{$packagesRes['oldInput']['email']}}@endif" autocomplete="off" />
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Warehouse Area') }}</b></label>
                    <div class="search-input position-relative">
                        <input type="input" class="form-control w-100" id="warehouse-input" list="dropdown-area" name="warehouse" value="@if (isset($packagesRes['oldInput']['warehouse'])){{$packagesRes['oldInput']['warehouse']}}@endif" autocomplete="off" />
                        <button type="button" id="areaButton" class="btn scan-btn apy-4" data-toggle="modal" data-target="#scan-modal"><i class="fa fa-qrcode font-20"></i></button>
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Group') }}</b></label>
                    <div class="search-input position-relative">
                        <input type="input" class="form-control w-100" id="group-input" name="group" value="@if (isset($packagesRes['oldInput']['group'])){{$packagesRes['oldInput']['group']}}@endif"/>
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="barcode" class="col-form-label search-label"><b>{{ __('Package Code') }}</b></label>
                    <div class="search-input position-relative">
                        <input type="input" id="barcode" class="form-control w-100" name="barcode" value="@if(isset($packagesRes['oldInput']['barcode'])){{ $packagesRes['oldInput']['barcode'] }}@endif" />
                        <button type="button" id="startButton" class="btn scan-btn apy-4" data-toggle="modal" data-target="#scan-modal"><i class="fa fa-qrcode font-20"></i></button>
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <div class="search-label d-none d-sm-block"></div>
                    <div class="search-input apl-24">
                        <input class="form-check-input" id="show-selected" type="checkbox" value="true" name="showSelectedOnly"
                            @if(isset($packagesRes['oldInput']['showSelectedOnly']))
                                checked
                            @endif
                        />
                        <label class="form-check-label" for="show-selected">{{ __('Show Selected') }}</label>
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
            @if (count($packagesRes['packages']) == 0)
                <div class="text-center">{{ __('No data.') }}</div>
            @else
                <div class="table-responsive">
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-outbound-table">
                        <thead>
                            <tr>
                                <th>{{ __('Package Code') }}</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Group Name') }}</th>
                                <th>{{ __('Area') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Address') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($packagesRes['packages'] as $package)
                                <tr>
                                    <td>{{ $package->barcode }}
                                        <input type="hidden" value={{$package->id }} id="package-{{ $package->id }}" /></td>
                                    <td>{{ $package->user->email ?? '' }}</td>
                                    <td>{{ $package->packageGroup->name ?? '' }}</td>
                                    <td>{{ $package->warehouseArea->name ?? '' }}</td>
                                    <td>{{ App\Models\Package::$statusName[$package->status] }}</td>
                                    @php
                                        $textAreaValue = null;
                                        if(!empty(session('packageIds'))){
                                            foreach(session('packageIds') as $index => $packageId){
                                                if($packageId == $package->id) {
                                                    $textAreaValue = session('address')[$index];
                                                }
                                            }
                                        }
                                    @endphp
                                    <td>
                                        <textarea class="form-control" id="textarea-input-{{ $package->id }}" name="textarea-input" rows="3" placeholder="Address.."  @if(isset($textAreaValue)) disabled @endif >@if($textAreaValue && $textAreaValue != "") {{ $textAreaValue }} @endif</textarea>
                                    </td>
                                    <td>
                                        <input id="checkbox-package-{{ $package->id}}" type="checkbox" value="" onchange="saveId({{ $package->id }})"
                                            @if((!empty(session('packageIds')) && in_array($package->id, session('packageIds'))))
                                                    checked
                                            @endif
                                        />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center justify-content-md-end amt-16">
                    {{ $packagesRes['packages']->appends(request()->all())->links('components.pagination') }}
                </div>
            @endif
            @if (count($packagesRes['packages']) != 0)
            <div class="row justify-content-center amy-24">
                <div class="col-md-7">
                    <button class="btn btn-block btn-success" data-toggle="modal" data-target=".modal" onclick="callModal({{$packagesRes['oldInput']['user_id']}})">Outbound packages</button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                WARNING
            </div>
            <div class="modal-body">
                Are you sure for update selected package ?
            </div>
            <div class="modal-footer btn-update-package border-0">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
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

@section('scripts')
    <script>
        let users = @php echo json_encode($packagesRes['users']) @endphp;
        let warehouses = @php echo json_encode($packagesRes['warehouses']) @endphp;
        let areas = @php echo json_encode($packagesRes['areas']) @endphp;

        filterInput(document.getElementById("email-input"), users, 'dropdown-email');
        filterInput(document.getElementById("warehouse-input"), warehouses, 'dropdown-area');

        function saveId(id) {
            let textAreaElement = $(`#textarea-input-${id}`)
            let checkbox = $(`#checkbox-package-${id}`)
            let address = $(`#textarea-input-${id}`).val()
            if(!address || address == "") {
                textAreaElement.focus();
                textAreaElement.addClass("is-invalid");
                checkbox.prop("checked", false);

                return
            }
            textAreaElement.removeClass("is-invalid");
            let isAdd = false
            let info = [-1, ""];
            if(checkbox.is(":checked")) {
                isAdd = true
                textAreaElement.prop( "disabled", true );

                let package_id = $(`#package-${id}`).val()
                info = address

            } else {
                textAreaElement.prop( "disabled", false );
            }
            $.ajax({
                type: "POST",
                url: "{{ route('staff.package.save') }}",
                data: {
                    id,
                    isAdd,
                    address,
                    _token: '{{csrf_token()}}'
                },
                success:function(data)
                { }
            });
        }

        function callModal(userId) {
            const element = $(".btn-update-package");
            element.find('.btn-ok').remove()
            const btn = $('<button>').addClass('btn btn-primary btn-ok').text('Update').on('click', () => {
                $('#confirm-delete').modal('hide')
                updatePackage(userId)
            })
            element.append(btn);
        }

        function updatePackage(userId) {
            loading()
            $.ajax({
                type: "POST",
                url: "{{ route('staff.package.updatePackageStatus') }}",
                data: {
                    _token: '{{csrf_token()}}'
                },
                success:function(data) {
                    let url = "{{ route('staff.package.outbound', ['user_id' => 'userId']) }}"
                    url = url.replace('userId', userId ? userId : '');
                    window.location.href = url
                },
                error: function() {
                    loading(false);
                    setTimeout(() =>  alert('Something wrong! Please contact admin for more information!'), 1000)
                }
            });
        }

        window.addEventListener('load', function () {
            try {
                let selectedDeviceId;
                const codeReader = new window.zxing.BrowserMultiFormatReader()
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

                    document.getElementById('startButton').addEventListener('click', () => {
                        codeReader.decodeFromVideoDevice(selectedDeviceId, 'video', (result, err) => {
                            if (result) {
                                $('#barcode').val(result.text);
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

                    document.getElementById('areaButton').addEventListener('click', () => {
                        codeReader.decodeFromVideoDevice(selectedDeviceId, 'video', (result, err) => {
                            if (result) {
                                const areaName = areas.find(area =>{
                                    return area.barcode == result.text
                                });

                                if(areaName) {
                                    if(areaName['is_full'] == 1) {
                                        createFlash([{type: 'error', content: 'This area is full !'}])
                                        $(`#warehouse-input`).val('')
                                    } else {
                                        $(`#warehouse-input`).val(areaName.name)
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
                }).catch((err) => { console.log(err)})
            } catch(err){
                console.log(err)
            }
        })
    </script>
@endsection
