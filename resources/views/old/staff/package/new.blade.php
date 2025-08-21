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
            'text' => 'Create Package',
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

@section('content')
<div class="fade-in">
    <form action="{{ route('staff.package.create') }}" method="POST" enctype="multipart/form-data" id="create-form">
    @csrf
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h2 class="mb-0">{{ __('Package Group') }}</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-success apx-8" onclick="showNew()">Create new</button>
                        <button type="button" class="btn btn-info apx-8" data-toggle="modal" data-target="#group-modal">Existed Group</button>

                        <input type="hidden" id="group_type" name="group_type" value="">
                    </div>
                </div>

                <div class="row apy-20" id="add-new-group">
                    <div class="col-md-6">
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('User') }}</b></label>
                            <div class="search-input position-relative">
                                <input id="email" type="text" list="dropdown-email" class="form-control w-100 @error('email') is-invalid @enderror" name="email" autocomplete="off">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Name') }}</b></label>
                            <div class="search-input">
                                <input id="name" type="text" class="form-control w-100 @error('name') is-invalid @enderror" name="name">
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Barcode') }}</b></label>
                            <div class="search-input position-relative">
                                <input id="barcode" type="text" class="form-control w-100 @error('barcode') is-invalid @enderror" name="barcode" value="">
                                <button type="button" id="group-start-button" class="btn scan-btn apy-4" data-toggle="modal" data-target="#scan-modal"><i class="fa fa-qrcode font-20"></i></button>
                                @error('barcode')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('File') }}</b></label>
                            <div class="search-input">
                            <input id="file" hidden type="file" class="img-picker @error('file') is-invalid @enderror" name="file">
                                <div class="btn btn-info" onclick="upload()">Update file</div>
                                @error('file')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Unit Width') }}</b></label>
                            <div class="search-input">
                                <input id="width" type="text" class="form-control w-100 @error('unit_width') is-invalid @enderror" name="unit_width">
                                @error('unit_width')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Unit Height') }}</b></label>
                            <div class="search-input">
                                <input id="height" type="text" class="form-control w-100 @error('unit_height') is-invalid @enderror" name="unit_height">
                                @error('unit_height')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Unit Length') }}</b></label>
                            <div class="search-input">
                                <input id="length" type="text" class="form-control w-100 @error('unit_length') is-invalid @enderror" name="unit_length">
                                @error('unit_length')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Unit Weight') }}</b></label>
                            <div class="search-input">
                                <input id="size" type="text" class="form-control w-100 @error('unit_weight') is-invalid @enderror" name="unit_weight">
                                @error('unit_weight')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row apy-20" id="exited-group">
                    <input type="hidden" name="group_id" id="exited-group-id"/>
                    <input type="hidden" name="user_id" id="exited-group-user"/>
                    <div class="col-md-6">
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('User') }}</b></label>
                            <div class="search-input" id="exited-group-email"></div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Name') }}</b></label>
                            <div class="search-input" id="exited-group-name"></div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Barcode') }}</b></label>
                            <div class="search-input" id="exited-group-barcode"></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Unit Width') }}</b></label>
                            <div class="search-input" id="exited-group-width"></div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Unit Height') }}</b></label>
                            <div class="search-input" id="exited-group-height"></div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Unit Length') }}</b></label>
                            <div class="search-input" id="exited-group-length"></div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Unit Weight') }}</b></label>
                            <div class="search-input" id="exited-group-weight"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <h2 class="mb-0">{{ __('Add Package') }}</h2>
                <hr>
                <div class="row">
                    <div class="col-md-6 col-xl-4">
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Warehouse Area') }}</b></label>
                            <div class="search-input position-relative">
                                <input type="text" class="form-control w-100" id="warehouse-input" list="dropdown-area" name="warehouse" autocomplete="off" />
                                <button type="button" id="shift-warehouse" list="dropdown-area" class="btn scan-btn apy-4" data-toggle="modal" data-target="#scan-modal"><i class="fa fa-qrcode font-20"></i></button>
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Status') }}</b></label>
                            <div class="search-input">
                                <select id="shift-status" name="status" class="form-control w-100" name="status">
                                    @foreach (App\Models\Package::$statusName as $key => $status)
                                        <option value="{{ $key }}">{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Package number') }}</b></label>
                            <div class="search-input">
                                <input type="number" class="form-control w-100" id="shift-package-number" value="0" />
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-4">
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Unit') }}</b></label>
                            <div class="search-input position-relative">
                                <input id="shift-unit" type="number" class="form-control w-100" name="unit" value="0"/>
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Package weight') }}</b></label>
                            <div class="search-input position-relative">
                                <input id="shift-weight" type="text" class="form-control w-100" name="weight"/>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-4">
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Package length') }}</b></label>
                            <div class="search-input position-relative">
                                <input id="shift-length" type="text" class="form-control w-100" name="length"/>
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Package width') }}</b></label>
                            <div class="search-input position-relative">
                                <input id="shift-width" type="text" class="form-control w-100" name="width"/>
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Package height') }}</b></label>
                            <div class="search-input position-relative">
                                <input id="shift-height" type="text" class="form-control w-100" name="height"/>
                            </div>
                        </div>


                        <div class="search-form-group">
                            <div class="search-label d-none d-sm-block"></div>
                            <div class="search-input text-center text-sm-left">
                                <div class="btn btn-primary" onclick="addRow()"> {{ __('Add Package') }} </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row apx-16" id="shift-table">
                    <div class="table-responsive">
                        <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-new-package-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Warehouse') }}</th>
                                    <th>{{ __('Unit') }}</th>
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

                    <div class="search-form-group w-100">
                        <div class="btn btn-primary btn-block" onclick="checkSubmit()">
                            {{ __('Create') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- Modal -->
<div id="group-modal" class="modal fade bd-example-scan-lg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select group</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('User') }}</b></label>
                    <div class="search-input position-relative">
                        <input type="text" class="form-control w-100" id="user-search" list="dropdown-user" autocomplete="off" />
                    </div>
                </div>
                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Name') }}</b></label>
                    <div class="search-input position-relative">
                        <input type="text" class="form-control w-100" id="name-search" />
                    </div>
                </div>
                <div class="search-form-group">
                    <div class="search-label d-none d-sm-block"></div>
                    <div class="search-input text-center text-sm-left">
                        <button class="btn btn-primary" onclick="searchGroup()">Search</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="group-table">
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
    $('#add-new-group').hide()
    $('#exited-group').hide()
    $('.card-footer').hide()
    $('#shift-table').hide()
    let groupList;
    let index = 0;
    let users = @php echo json_encode($users) @endphp;
    let warehouseAreas = @php echo json_encode($areas) @endphp;
    let warehouses = @php echo json_encode($warehouses) @endphp;

    filterInput(document.getElementById("email"), users, 'dropdown-email');
    filterInput(document.getElementById("user-search"), users, 'dropdown-user');
    filterInput(document.getElementById("warehouse-input"), warehouses, 'dropdown-area');

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

            document.getElementById('group-start-button').addEventListener('click', () => {
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

            document.getElementById('shift-warehouse').addEventListener('click', () => {
                codeReader.decodeFromVideoDevice(selectedDeviceId, 'video', (result, err) => {
                    if (result) {
                        const areaName = warehouseAreas.find(area =>{
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

    function upload() {
        $('#file').click()
    }

    async function addRow() {
        loading()
        const unit = $('#shift-unit').val()
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


        const areaName = $('#warehouse-input').val()
        let areaInfo;

        if(areaName) {
            areaInfo = warehouseAreas.find(area =>{
                return area.name == areaName
            });

            if(!areaInfo) {
                $('#warehouse-input').addClass('is-invalid')
                createFlash([{type: 'error', content: 'Warehouse Area not existed'}])
                loading(false)

                return
            }

            if(areaInfo.is_full == 1) {
                $('#warehouse-input').addClass('is-invalid')
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
                    <input type="hidden" name="package[${index}][unit_number]" value="${parseInt(unit)}" />
                    <input type="hidden" name="package[${index}][received_unit_number]" value="${parseInt(unit)}" />
                    <input type="hidden" name="package[${index}][warehouse_area_id]" value="${areaInfo ? areaInfo.id : ''}" />
                    <input type="hidden" name="package[${index}][status]" value="${status}" />
                    <input type="hidden" name="package[${index}][weight_staff]" value="${weight ? parseFloat(weight) : ''}" />
                    <input type="hidden" name="package[${index}][length_staff]" value="${length ? parseFloat(length) : ''}" />
                    <input type="hidden" name="package[${index}][width_staff]" value="${width ? parseFloat(width) : ''}" />
                    <input type="hidden" name="package[${index}][height_staff]" value="${height ? parseFloat(height) : ''}" />
                    <td>${areaInfo ? areaInfo.name : 'Not in warehouse area'}</td>
                    <td>${unit}</td>
                    <td>${weight}</td>
                    <td>${length}</td>
                    <td>${width}</td>
                    <td>${height}</td>
                    <td>${statusName}</td>
                    <td>
                        <div class="btn btn-danger" onclick="removeRow(${index})">Remove</button>
                    </td>
                </tr>
            `

            element.append(row)
        }

        $('#shift-barcode').removeClass('is-invalid')
        $('#shift-unit').removeClass('is-invalid')
        $('#warehouse-input').removeClass('is-invalid')
        $('#weight-input').removeClass('is-invalid')
        $('#shift-package-number').removeClass('is-invalid')
        $('#shift-weight').removeClass('is-invalid')
        $('#shift-length').removeClass('is-invalid')
        $('#shift-width').removeClass('is-invalid')
        $('#shift-height').removeClass('is-invalid')

        $('#shift-unit').val(0)
        $('#shift-package-number').val(0)
        $('#warehouse-input').val('')
        $('#shift-weight').val(null)
        $('#shift-length').val(null)
        $('#shift-width').val(null)
        $('#shift-height').val(null)
        $("#shift-status").val(0);

        loading(false)
    }

    function removeRow(index) {
        loading()

        const row = $(`#shift-table-row-${index}`)
        const rowCount = $('#shift-table-body tr').length;

        if(rowCount <= 1) {
            $('#shift-table').hide()
        }

        row.remove()
        loading(false)
    }

    function showNew() {
        $('#add-new-group').show()
        $('#exited-group').hide()
        $('.card-footer').show()

        $(`#group_type`).val('new')
    }

    function showExited() {
        $('#add-new-group').hide()
        $('#exited-group').show()
        $('.card-footer').show()
    }

    function searchGroup() {
        loading()

        const emailInput = $('#user-search').val()
        let email;

        if(emailInput) {
            email = users.find(user =>{
                return user == emailInput
            });

            if(!email) {
                $('#user-search').addClass('is-invalid')
                createFlash([{type: 'error', content: 'User not existed'}])
                loading(false)

                return
            }
        } else {
            $('#user-search').addClass('is-invalid')
            createFlash([{type: 'error', content: 'Please enter user email!'}])
            loading(false)

            return
        }

        const name = $('#name-search').val()

        $.ajax({
            type: 'POST',
            url: "{{ route('staff.package.getGroup') }}",
            data: {
                email,
                name,
                _token: '{{csrf_token()}}'
            },
            success:function(data) {
                $('#user-search').removeClass('is-invalid')
                let raw = '<div class="text-center">No data</div>'
                if(data.length > 0) {
                    groupList = data
                    raw = createTable(data)
                }

                $('#group-table').empty()
                $('#group-table').append(raw)

                loading(false);
            },
            error: function(e) {
                loading(false);
                alert('Something wrong! Please contact admin for more information!')
            }
        });
    }

    function createTable(data) {
        let body = []
        for(let i = 0; i < data.length; i++) {
            const row = `<tr id="group-table-row-${i}">
                    <td><button class="btn btn-success" onclick="selectRow(${i})" data-dismiss="modal">Select</button></td>
                    <td>${data[i].name}</td>
                    <td>${data[i].barcode ? data[i].barcode : ''}</td>
                </tr>`
            body.push(row)
        }
        let rawBody = body.toString()

        const rawTable =`
            <div class="table-responsive">
                <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-new-package-search-barcode-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Barcode') }}</th>
                        </tr>
                    </thead>
                    <tbody id="shift-table-body">
                        ${rawBody.replaceAll(",", " ")}
                    </tbody>
                </table>
            </div>
        `

        return rawTable
    }

    function selectRow(index) {
        showExited()
        $(`#exited-group-id`).val(groupList[index].id);
        $(`#exited-group-user`).val(groupList[index].user.id);
        $(`#exited-group-email`).text(groupList[index].user.email);
        $(`#exited-group-name`).text(groupList[index].name);
        $(`#exited-group-barcode`).text(groupList[index].barcode);
        $(`#exited-group-width`).text(groupList[index].unit_width);
        $(`#exited-group-length`).text(groupList[index].unit_length);
        $(`#exited-group-height`).text(groupList[index].unit_height);
        $(`#exited-group-weight`).text(groupList[index].unit_weight);

        $(`#group_type`).val('exited')
    }

    function checkSubmit() {
        const type = $(`#group_type`).val()

        if(type == 'new') {
            const email = $(`#email`).val()
            const name = $(`#name`).val()

            let haveError = false
            if(!email || email == '') {
                $(`#email`).addClass('is-invalid')
                createFlash([{type: 'error', content: 'Please enter user email!'}])
                haveError = true
            } else {
                $(`#email`).removeClass('is-invalid')
            }

            if(!name || name == '') {
                $(`#name`).addClass('is-invalid')
                createFlash([{type: 'error', content: 'Please enter package name!'}])
                haveError = true
            } else {
                $(`#name`).removeClass('is-invalid')
            }

            if(haveError) {
                return
            }
        }

        $('#create-form').submit()
    }
  </script>
@endsection
