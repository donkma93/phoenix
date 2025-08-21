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
            'text' => $package['id']
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
    <div class="card">
        <div class="card-header">
            <h2 class="mb-0">{{ __('Package detail') }}</h2>
        </div>
        <form action="{{ route('staff.package.update') }}" method="POST" enctype="multipart/form-data">
            <input type="hidden" value="{{ $package['id'] }}" name="id" />
            @csrf    
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Package Code') }}</b></label>
                            @if(isset($package['deleted_at']))
                                <div class="search-input col-form-label">
                                    {{ $package->barcode }}
                                </div>
                            @else
                                <div class="search-input col-form-label">
                                    <input type="text" class="form-control w-100 @error('barcode') is-invalid @enderror" name="barcode" value="{{ $package['barcode'] }}"/>
                                    @error('barcode')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            @endif
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Preview Code') }}</b></label>
                            <div class="col-form-label" id="barcode-preview">{!! DNS2D::getBarcodeSVG($package->barcode, 'QRCODE') !!}</div>
                        </div>
                        
                        <div class="search-form-group amb-16">
                            <div class="search-label d-none d-sm-block"></div>
                            <div class="search-input text-center text-sm-left">
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewPDF('barcode-preview')">
                                    {{ __('Print Code') }}
                                </button>
                            </div>
                        </div>

                        <div class="d-none" id="info-barcode" style="height:120px; width: 170px">
                            <div style="word-wrap: break-word; font-size:8px">
                                <b>Product:</b> {{ $package['packageGroup']['name'] ?? '' }}
                            </div>
                            <div style="word-wrap: break-word; font-size:8px">
                                <b>Number of unit:</b> {{ $package['unit_number'] }}
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('User') }}</b></label>
                            <div class="col-form-label">
                                {{ $package['user']['email'] ?? '' }}
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Unit') }}</b></label>
                            <div class="col-form-label">
                                {{ $package['unit_number'] ?? 0 }}
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Received') }}</b></label>
                            <div class="col-form-label">
                                {{ $package['received_unit_number'] ?? 0 }}
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Created') }}</b></label>
                            <div class="col-form-label">
                                {{ $package['created_at'] }}
                            </div>
                        </div>

                        @if(isset($package['unit_barcode']))
                            <div class="form-group search-form-group">
                                <label class="col-form-label search-label"><b>{{ __('Unit Code') }}</b></label>
                                <div class="col-form-label">
                                    {{ $package['unit_barcode'] }}
                                </div>
                            </div>

                            <div class="form-group search-form-group">
                                <label class="col-form-label search-label"><b>{{ __('Preview U.Code') }}</b></label>
                                <div class="col-form-label" id="unit-barcode-preview">{!! DNS2D::getBarcodeSVG($package['unit_barcode'], 'QRCODE') !!}</div>
                            </div>
                            
                            <div class="search-form-group amb-16">
                                <div class="search-label d-none d-sm-block"></div>
                                <div class="search-input text-center text-sm-left">
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewPDFGroup('unit-barcode-preview')">
                                        {{ __('Print Code') }}
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Group Code') }}</b></label>
                            <div class="search-input col-form-label">
                                {{ $package['packageGroup']['barcode'] }}
                            </div>
                        </div>

                        @if(isset($package['packageGroup']['barcode']))
                            <div class="form-group search-form-group">
                                <label class="col-form-label search-label"><b>{{ __('Preview Code') }}</b></label>
                                <div class="col-form-label" id="group-barcode-preview">{!! DNS2D::getBarcodeSVG($package['packageGroup']['barcode'], 'QRCODE') !!}</div>
                            </div>

                            <div class="search-form-group amb-16">
                                <div class="search-label d-none d-sm-block"></div>
                                <div class="search-input text-center text-sm-left">
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewPDFGroup('group-barcode-preview')">
                                        {{ __('Print Code') }}
                                    </button>
                                </div>
                            </div>
                        @endif

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Group name') }}</b></label>
                            <div class="col-form-label">
                                {{ $package['packageGroup']['name'] }}
                            </div>
                        </div>
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Unit width') }}</b></label>
                            <div class="col-form-label">
                                {{ $package['packageGroup']['unit_width'] }}
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Unit height') }}</b></label>
                            <div class="col-form-label">
                                {{ $package['packageGroup']['unit_height'] }}
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Unit length') }}</b></label>
                            <div class="col-form-label">
                                {{ $package['packageGroup']['unit_length'] }}
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Unit weight') }}</b></label>
                            <div class="col-form-label">
                                {{ $package['packageGroup']['unit_weight'] }}
                            </div>
                        </div>
                    </div>
                </div>  
                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-package-detail-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Code') }}</th>
                                    <th>{{ __('Weight') }}</th>
                                    <th>{{ __('Length') }}</th>
                                    <th>{{ __('Width') }}</th>
                                    <th>{{ __('Height') }}</th>
                                    <th>{{ __('Cuft') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groups as $detail)
                                <tr>
                                    <td>{{ ($groups->currentPage() - 1) * $groups->perPage() + $loop->iteration }}</td>
                                    <td>{{ $detail->name }}</td>
                                    <td>
                                        {{ $detail->barcode }}
                                        @if(isset($detail->barcode))
                                        <div id="barcode-{{ $loop->iteration }}" class="d-none">
                                            
                                            {!! DNS2D::getBarcodeSVG($detail->barcode, 'QRCODE') !!}
                                        </div>

                                        <div class="d-none" id="info-barcode-{{ $loop->iteration }}" style="height:120px">
                                            <div style="word-wrap: break-word; font-size:10px">
                                                <b>Product:</b> {{ $detail->name ?? '' }}
                                            </div>
                                        </div>
                                        @endif
                                    </td>
                                    <td>{{ $detail->unit_weight }}</td>
                                    <td>{{ $detail->unit_length }}</td>
                                    <td>{{ $detail->unit_width }}</td>
                                    <td>{{ $detail->unit_height }}</td>
                                    <td>
                                    @php
                                        $heightS = $detail->unit_height ?? 0;
                                        $widthS = $detail->unit_width ?? 0;
                                        $lengthS = $detail->unit_length ?? 0;
                                        $cuftS = ($heightS * $widthS * $lengthS) / (12*12*12);
                                    @endphp
                                        {{  round($cuftS, 4) }}
                                    </td>
                                    <td>
                                        @if(isset($detail->barcode))
                                            <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewPackageBarcode('barcode-{{ $loop->iteration }}')">
                                                Print Code
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center justify-content-md-end amt-16">
                        {{ $groups->appends(request()->all())->links('components.pagination') }}
                    </div>
                </div> 
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-6">
                        <h2>User measure</h2>
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Package width') }}</b></label>
                            <div class="col-form-label">
                                {{ $package['width'] ?? 0 }}
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Package height') }}</b></label>
                            <div class="col-form-label">
                                {{ $package['height'] ?? 0 }}
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Package length') }}</b></label>
                            <div class="col-form-label">
                                {{ $package['length'] ?? 0 }}
                            </div>
                        </div>

                        
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Package weight') }}</b></label>
                            <div class="col-form-label">
                                {{ $package['weight'] ?? 0 }}
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Cuft') }}</b></label>
                            <div class="col-form-label">
                                @php
                                    $cuft = ($package['width'] * $package['height'] * $package['length'] ) / (12 * 12 * 12 )
                                @endphp
                                {{ round($cuft, 4) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h2>Staff measure</h2>
                            @if(isset($package['deleted_at']))\
                            <div class="form-group search-form-group">
                                <label class="col-form-label search-label"><b>{{ __('Package width') }}</b></label>
                                <div class="col-form-label">
                                    {{ $package['width_staff'] }}
                                </div>
                            </div>

                            <div class="form-group search-form-group">
                                <label class="col-form-label search-label"><b>{{ __('Package height') }}</b></label>
                                <div class="col-form-label">
                                    {{ $package['height_staff'] }}
                                </div>
                            </div>

                            <div class="form-group search-form-group">
                                <label class="col-form-label search-label"><b>{{ __('Package length') }}</b></label>
                                <div class="col-form-label">
                                    {{ $package['length_staff'] }}
                                </div>
                            </div>

                            
                            <div class="form-group search-form-group">
                                <label class="col-form-label search-label"><b>{{ __('Package weight') }}</b></label>
                                <div class="col-form-label">
                                    {{ $package['weight_staff'] }}
                                </div>
                            </div>

                            <div class="form-group search-form-group">
                                <label class="col-form-label search-label"><b>{{ __('Cuft') }}</b></label>
                                <div class="col-form-label">
                                    @php
                                        $cuft = ($package['width_staff'] * $package['height_staff'] * $package['length_staff'] ) / (12 * 12 * 12 )
                                    @endphp
                                    {{ round($cuft, 4) }}
                                </div>
                            </div>

                            <div class="form-group search-form-group">
                                <label class="col-form-label search-label"><b>{{ __('Status') }}</b></label>
                                <div class="col-form-label">
                                    {{ App\Models\Package::$statusName[$package['status']] }}@if(isset($package['deleted_at']))<span class="atext-red-500">(Deleted)</span>@endif
                                </div>
                            </div>

                            <div class="form-group search-form-group">
                                <label class="col-form-label search-label"><b>{{ __('Warehouse Area') }}</b></label>
                                <div class="col-form-label">
                                    {{ $package['warehouseArea']['name'] ?? '' }}
                                </div>
                            </div>
                        @else
                            <div class="form-group search-form-group">
                                <label class="col-form-label search-label"><b>{{ __('Package width') }}</b></label>
                                <div class="col-form-label">
                                    <input type="text" class="form-control w-100 @error('width_staff') is-invalid @enderror" name="width_staff" value="{{ $package['width_staff'] }}"/>
                                    @error('width_staff')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group search-form-group">
                                <label class="col-form-label search-label"><b>{{ __('Package height') }}</b></label>
                                <div class="col-form-label">
                                    <input type="text" class="form-control w-100 @error('height_staff') is-invalid @enderror" name="height_staff" value="{{ $package['height_staff'] }}"/>
                                    @error('height_staff')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group search-form-group">
                                <label class="col-form-label search-label"><b>{{ __('Package length') }}</b></label>
                                <div class="col-form-label">
                                    <input type="text" class="form-control w-100 @error('length_staff') is-invalid @enderror" name="length_staff" value="{{ $package['length_staff'] }}"/>
                                    @error('length_staff')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="form-group search-form-group">
                                <label class="col-form-label search-label"><b>{{ __('Package weight') }}</b></label>
                                <div class="col-form-label">
                                    <input type="text" class="form-control w-100 @error('weight_staff') is-invalid @enderror" name="weight_staff" value="{{ $package['weight_staff'] }}"/>
                                    @error('weight_staff')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group search-form-group">
                                <label class="col-form-label search-label"><b>{{ __('Cuft') }}</b></label>
                                <div class="col-form-label">
                                    @php
                                        $cuft_staff = ($package['width_staff'] * $package['height_staff'] * $package['length_staff'] ) / (12 * 12 * 12 )
                                    @endphp
                                    {{ round($cuft_staff, 4) }}
                                </div>
                            </div>

                            <div class="form-group search-form-group">
                                <label class="col-form-label search-label"><b>{{ __('Status') }}</b></label>
                                <div class="col-form-label">
                                    <select id="shift-status" name="status" class="form-control w-100" name="status">
                                        @foreach (App\Models\Package::$statusName as $key => $status)
                                            <option value="{{ $key }}"
                                            @if($package['status'] == $key)
                                                selected
                                            @endif>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group search-form-group">
                                <label class="col-form-label search-label"><b>{{ __('Warehouse Area') }}</b></label>
                                <div class="col-form-label position-relative w-50">
                                    <input type="text" list="dropdown-area" class="form-control w-100 @error('warehouse') is-invalid @enderror" id="warehouse-input" name="warehouse" autocomplete="off" value="{{ $package['warehouseArea']['name'] ?? '' }}"/>
                                    <button type="button" id="warehouse" class="btn scan-btn apy-4" data-toggle="modal" data-target="#scan-modal"><i class="fa fa-qrcode font-20"></i></button>
                                    @error('warehouse')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="search-form-group">
                                <div class="search-label d-none d-sm-block"></div>
                                <div class="search-input text-center text-sm-left">
                                    <input type="submit" value="{{ __('Update') }}" class="btn btn-primary" />
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </form>
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
    let warehouseAreas = @php echo json_encode($areas) @endphp;
    let warehouses = @php echo json_encode($warehouses) @endphp;

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

            document.getElementById('warehouse').addEventListener('click', () => {
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

    function previewPDF(id) {
        $(".modal-body").find("embed").remove();
        
        const { jsPDF } = window.jspdf;
        let doc = new jsPDF("l", "mm", "a4");
        const infoPackage = document.getElementById("info-barcode");
        let svgHtml = $(`#${id}`).html();
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
                clonedDoc.getElementById(`info-barcode`).style.display = 'block';
                clonedDoc.getElementById(`info-barcode`).classList.remove("d-none");
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

    function previewPDFGroup(id) {
        $(".modal-body").find("embed").remove();

        const { jsPDF } = window.jspdf;
        let doc = new jsPDF("l", "mm", "a4");
        
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

        let embed = "<embed src="+ imgSrc +" frameborder='0' width='100%' height='500px' type='application/pdf' class='preview-pdf'>"
        $(".modal-body").append(embed)
    }

    function previewPackageBarcode(id) {
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
