@extends('layouts.admin')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('admin.dashboard')
        ],
        [
            'text' => 'Package'
        ]
    ]
])
@endsection

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-header">
            <h2 class="mb-0">{{ __('Package list') }}</h2>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.package.list') }}" class="form-horizontal" role="form">
                <div class="form-group search-form-group">
                    <label for="type" class="col-form-label search-label"><b>{{ __('Email') }}</b></label>
                    <div class="search-input position-relative">
                        <input type="input" class="form-control w-100" id="email-input"  list="dropdown-email" name="email" value="@if (isset($oldInput['email'])){{$oldInput['email']}}@endif" autocomplete="off" />
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="warehouse" class="col-form-label search-label"><b>{{ __('Warehouse Area') }}</b></label>
                    <div class="search-input position-relative">
                        <input type="input"  list="dropdown-area" class="form-control w-100" id="warehouse-input" name="warehouse" value="@if (isset($oldInput['warehouse'])){{$oldInput['warehouse']}}@endif" autocomplete="off" />
                        <button type="button" id="area-button" class="btn scan-btn apy-4" data-toggle="modal" data-target="#scan-modal"><i class="fa fa-qrcode font-20"></i></button>
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="status" class="col-form-label search-label"><b>{{ __('Status') }}</b></label>
                    <div class="search-input">
                        <select id="status" name="status" class="form-control w-100">
                            <option selected></option>
                            @foreach (App\Models\Package::$statusName as $key => $status)
                                <option value="{{ $key }}"
                                @if (isset($oldInput['status']) && $oldInput['status'] == $key)
                                            selected
                                        @endif
                                >{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="barcode" class="col-form-label search-label"><b>{{ __('Package Code') }}</b></label>
                    <div class="search-input position-relative">
                        <input type="input" id="barcode" class="form-control w-100" name="barcode" value="@if (isset($oldInput['barcode'])){{$oldInput['barcode']}}@endif" />
                        <button type="button" id="start-button" class="btn scan-btn apy-4" data-toggle="modal" data-target="#scan-modal"><i class="fa fa-qrcode font-20"></i></button>
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="startDate" class="col-form-label search-label"><b>{{ __('From Date') }}</b></label>
                    <div class="search-input">
                        <input id="startDate" type="text" class="form-control date-picker w-100 @error('startDate') is-invalid @enderror" name="startDate" value="@if(isset($oldInput['startDate'])){{ date('Y-m-d', strtotime($oldInput['startDate'])) }}@else{{ date('Y-m-d', strtotime('01-01-1980')) }}@endif">
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="endDate" class="col-form-label search-label"><b>{{ __('To Date') }}</b></label>
                    <div class="search-input">
                        <input id="endDate" type="text" class="form-control date-picker w-100 @error('endDate') is-invalid @enderror" name="endDate" value="@if(isset($oldInput['endDate'])){{ date('Y-m-d', strtotime($oldInput['endDate'])) }}@else{{ date('Y-m-d') }}@endif">
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="is_delete" class="col-form-label search-label"><b>{{ __('Show Deleted') }}</b></label>
                    <div class="search-input search-radio">
                        <div class="form-check form-check-inline amr-20">
                            <input class="form-check-input" id="all-verify" type="radio" value="" name="onlyDeleted"
                            @if(!isset($oldInput['onlyDeleted']))
                                checked
                            @endif
                            >
                            <label class="form-check-label" for="all-member">All</label>
                        </div>
                        <div class="form-check form-check-inline amr-20">
                            <input class="form-check-input" id="verify-only" type="radio" value="1" name="onlyDeleted"
                            @if(isset($oldInput['onlyDeleted']) && $oldInput['onlyDeleted'] == 1)
                                checked
                            @endif
                            >
                            <label class="form-check-label" for="only-deleted">Only deleted</label>
                        </div>
                        <div class="form-check form-check-inline amr-20">
                            <input class="form-check-input" id="not-verify" type="radio" value="0" name="onlyDeleted"
                            @if(isset($oldInput['onlyDeleted']) && $oldInput['onlyDeleted'] == 0)
                                checked
                            @endif>
                            <label class="form-check-label" for="not-delete">Not deleted</label>
                        </div>
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
            @if (count($packages) == 0)
                <div class="text-center">{{ __('No data.') }}</div>
            @else
                <div class="table-responsive">
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="admin-package-list-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>{{ __('Package Code') }}</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Group Name') }}</th>
                                <th>{{ __('Warehouse Area') }}</th>
                                <th>{{ __('Unit') }}</th>
                                <th>{{ __('Unit Received') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Created') }}</th>
                                <th>{{ __('Last Updated') }}</th>
                                <th>{{ __('Is deleted') }}</th>
                                <th>{{ __('Deleted') }}</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($packages as $package)
                            <tr>
                                <td>{{ ($packages->currentPage() - 1) * $packages->perPage() + $loop->iteration }}</td>
                                <td>
                                    {{ $package['barcode'] }}
                                </td>
                                <td>
                                    <a href="{{ route('admin.user.profile', ['id' => $package['user']['id']  ])}}">{{ $package['user']['email'] }}</a>
                                    <div id="barcode-{{ $loop->iteration }}" class="d-none">
                                        {!! DNS2D::getBarcodeSVG($package->barcode, 'QRCODE') !!}
                                    </div>

                                    <div class="d-none" id="info-barcode-{{ $loop->iteration }}" style="height:120px">
                                        <div style="word-wrap: break-word; font-size:10px">
                                            <b>Product:</b> {{ $package->packageGroup->name ?? '' }}
                                        </div>
                                        <div style="word-wrap: break-word; font-size:10px">
                                            <b>Number of unit:</b> {{ $package->unit_number }}
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $package['packageGroup']['name'] ?? '' }}</td>
                                <td>
                                    @if(isset($package['warehouseArea']['name']))
                                        {{ $package['warehouseArea']['name'] }}
                                    @endif
                                </td>
                                <td>{{  $package['unit_number'] }}</td>
                                <td>{{  $package['received_unit_number'] }}</td>
                                <td>{{ App\Models\Package::$statusName[$package['status']] }}</td>
                                <td>{{ $package->created_at }}
                                <td>{{ $package->updated_at }}
                                <td>
                                    @if(!isset($package['deleted_at']))
                                        In use
                                    @else
                                        Deleted
                                    @endif
                                </td>
                                <td>{{ $package->deleted_at }}
                                <td>
                                    <a href="{{ route('admin.package.detail', ['id' => $package['id'] ])}} " class="btn btn-info">{{ __('Detail') }}</a>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewPDF('barcode-{{ $loop->iteration }}')">
                                        Preview Code
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center justify-content-md-end amt-16">
                    {{ $packages->appends(request()->all())->links('components.pagination') }}
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

<!-- Modal -->
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body">
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
    let email = @php echo json_encode($users) @endphp;
    filterInput(document.getElementById("email-input"), email, 'dropdown-email');


    let warehouses = @php echo json_encode($warehouseAreas) @endphp;
    filterInput(document.getElementById("warehouse-input"), warehouses, 'dropdown-area');

    let areas = @php echo json_encode($areas) @endphp;

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

            document.getElementById('start-button').addEventListener('click', () => {
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

            document.getElementById('area-button').addEventListener('click', () => {
                codeReader.decodeFromVideoDevice(selectedDeviceId, 'video', (result, err) => {
                    if (result) {
                        const areaName = areas.find(area =>{
                            return area.barcode == result.text
                        });

                        if(areaName) {
                            $('#warehouse-input').val(areaName.name)
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

    function previewPDF(id) {
        $(".modal-body").find("embed").remove();

        const { jsPDF } = window.jspdf;
        let doc = new jsPDF("l", "mm", "a4");
        let svgHtml = $(`#${id}`).html();

        const htmlId = `info-${id}`
        const infoPackage = document.getElementById(htmlId)
        
        if (svgHtml) {
            svgHtml = svgHtml.replace(/\r?\n|\r/g, '').trim();
        }
        
        let canvas = document.createElement('canvas');
        let context = canvas.getContext('2d');
        v = canvg.Canvg.fromString(context, svgHtml);
        v.start()

        let imgData = canvas.toDataURL('image/png');
        html2canvas(infoPackage, {
            scale: 10,
            onclone: function (clonedDoc) {
                clonedDoc.getElementById(`info-${id}`).style.display = 'block';
                clonedDoc.getElementById(`info-${id}`).classList.remove("d-none");
            }
        }).then(canvas2 => {
            let imgData2 = canvas2.toDataURL('image/png');
            
            doc.addImage(imgData, 'PNG', 10, 10, 100, 100);
            doc.addImage(imgData2, 'PNG', 120, 10, 100, 100);
            imgSrc = doc.output('bloburl');
            
            let embed = "<embed src="+ imgSrc +" frameborder='0' width='100%' height='500px' type='application/pdf' class='preview-pdf'>"
            $(".modal-body").append(embed)
        });
    }
</script>
@endsection
