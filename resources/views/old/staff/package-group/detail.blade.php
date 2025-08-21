@extends('layouts.staff')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('staff.dashboard')
        ],
        [
            'text' => 'Package Group',
            'url' => route('staff.package-group.list')
        ],
        [
            'text' => $packageGroup->id,
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
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('Package Group detail') }}</h2>
            @if(isset($productId))
                <a class="btn btn-success" href="{{ route('staff.product.detail', ['id' => $productId]) }}">
                    {{ __('Product') }}
                </a>
            @else 
                <form action="{{ route('staff.package-group.createProduct') }}" method="POST" enctype="multipart/form-data">    
                @csrf
                    <input type="hidden" value="{{ $packageGroup['id'] }}" name="id" />
                    <input class="btn btn-info" type="submit" value="{{ __('Create Product') }}">
                </form>
            @endif
        </div>
        <div class="card-body">
            <form action="{{ route('staff.package-group.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
                <input type="hidden" value="{{ $packageGroup['id'] }}" name="id" />
                <div class="row">
                    <div class="col-md-6 col-lg-12 col-xl-6">
                        <div class="form-group search-form-group">
                            <label for="name" class="col-form-label search-label"><b>{{ __('Name') }}</b> </label>
                            <div class="search-input col-form-label">
                                @if(!isset($packageGroup['deleted_at']))
                                    <input type="text" class="form-control w-100 @error('name') is-invalid @enderror" name="name" value="{{ $packageGroup['name'] }}"/>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                @else 
                                    {{ $packageGroup['name'] ?? '' }}
                                @endif
                            </div>
                        </div>
                        <div class="form-group search-form-group">
                            <label for="name" class="col-form-label search-label"><b>{{ __('User') }}</b> </label>
                            <div class="search-input col-form-label">
                                {{ $packageGroup['user']['email'] }}
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label for="created" class="col-form-label search-label"><b>{{ __('Status') }}</b></label>
                            <div class="search-input col-form-label">
                                @if(!isset($packageGroup['deleted_at']))
                                    In use
                                @else
                                    Deleted
                                @endif
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label for="total_package" class="col-form-label search-label"><b>{{ __('Total package') }}</b> </label>
                            <div class="search-input  col-form-label">
                                {{ $totalPackages }}
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label for="barcode" class="col-form-label search-label"><b>{{ __('Group Code') }}</b> </label>
                            <div class="search-input col-form-label">
                                @if(!isset($packageGroup['deleted_at']))    
                                    <input type="text" class="form-control w-100 @error('barcode') is-invalid @enderror" name="barcode" value="{{ $packageGroup['barcode'] }}"/>
                                    @error('barcode')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                @else 
                                    {{ $packageGroup['barcode'] ?? '' }}
                                @endif
                            </div>
                        </div>


                        @if(isset($packageGroup['barcode']))
                            <div class="form-group search-form-group">
                                <label for="barcode" class="col-form-label search-label"><b>{{ __('Preview') }}</b> </label>
                                <div class="search-input col-form-label show-barcode" id="group-barcode-preview">
                                    {!! DNS2D::getBarcodeSVG($packageGroup['barcode'], 'QRCODE') !!}
                                </div>
                            </div>

                            <div class="search-form-group">
                                <div class="search-label d-none d-sm-block"></div>
                                <div class="search-input text-center text-sm-left">
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewBarcode('group-barcode-preview')">
                                        {{ __('Print Code') }}
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6 col-lg-12 col-xl-6">
                        <div class="form-group search-form-group">
                            <label for="width" class="col-form-label search-label"><b>{{ __('Unit width') }}</b> </label>
                            <div class="search-input col-form-label">
                            @if(!isset($packageGroup['deleted_at']))
                                <input type="text" class="form-control w-100 @error('unit_width') is-invalid @enderror" name="unit_width" value="{{ $packageGroup['unit_width'] }}"/>
                                @error('unit_width')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            @else 
                                {{ $packageGroup['unit_width'] }}
                            @endif
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label for="length" class="col-form-label search-label"><b>{{ __('Unit length') }}</b> </label>
                            <div class="search-input col-form-label">
                            @if(!isset($packageGroup['deleted_at']))
                                <input type="text" class="form-control w-100 @error('unit_length') is-invalid @enderror" name="unit_length" value="{{ $packageGroup['unit_length'] }}"/>
                                @error('unit_length')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            @else 
                                {{ $packageGroup['unit_length'] }}
                            @endif
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label for="height" class="col-form-label search-label"><b>{{ __('Unit height') }}</b> </label>
                            <div class="search-input col-form-label">
                                @if(!isset($packageGroup['deleted_at']))
                                    <input type="text" class="form-control w-100 @error('unit_height') is-invalid @enderror" name="unit_height" value="{{ $packageGroup['unit_height'] }}"/>
                                    @error('unit_height')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                @else 
                                    {{ $packageGroup['unit_height'] }}
                                @endif
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label for="height" class="col-form-label search-label"><b>{{ __('Unit weight') }}</b> </label>
                            <div class="search-input col-form-label">
                                @if(!isset($packageGroup['deleted_at']))
                                    <input type="text" class="form-control w-100 @error('unit_weight') is-invalid @enderror" name="unit_weight" value="{{ $packageGroup['unit_weight'] }}"/>
                                    @error('unit_weight')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                @else 
                                    {{ $packageGroup['unit_weight'] }}
                                @endif
                            </div>
                        </div>
                        
                        @if(!isset($packageGroup['deleted_at']))
                            <div class="form-group search-form-group">
                                <div class="search-label d-none d-sm-block"></div>
                                <div class="search-input text-center text-sm-left">
                                    <input class="btn btn-info" type="submit" value="{{ __('Update') }}">
                                </div>
                            </div>
                        @endif

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Image') }}</b></label>
                            @if(!isset($packageGroup->product->image_url))
                                {{ __('No image') }}
                            @endif
                        </div>
                        @if(isset($packageGroup->product->image_url))
                            <div class="form-group search-form-group">
                                <img  width="300" height="300" src="{{ asset($packageGroup->product->image_url) }}" alt="Product image" class="img-fluid">
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
        @if(!isset($packageGroup['deleted_at']))
        <div class="card-footer" >
            <h2 class="mb-0">{{ __('Add Package') }}</h2>
            <hr>
            <form action="{{ route('staff.package-group.add') }}" method="POST" enctype="multipart/form-data">
            @csrf
                <input type="hidden" name="user_id" value="{{ $packageGroup['user_id'] }}" />
                <input type="hidden" name="package_group_id" value="{{ $packageGroup['id'] }}" />
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Warehouse Area') }}</b></label>
                            <div class="search-input position-relative">
                                <input type="text" class="form-control w-100 @error('warehouse') is-invalid @enderror" list="dropdown-area" name="warehouse" id="warehouse-input" name="warehouse" autocomplete="off" />
                                <button type="button" id="package-area" class="btn scan-btn apy-4" data-toggle="modal" data-target="#scan-modal"><i class="fa fa-qrcode font-20"></i></button>
                                @error('warehouse')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Status') }}</b></label>
                            <div class="search-input">
                                <select id="package-status" name="status" class="form-control w-100 @error('status') is-invalid @enderror" name="status">
                                    @foreach (App\Models\Package::$statusName as $key => $status)
                                        @if($key != App\Models\Package::STATUS_OUTBOUND)
                                            <option value="{{ $key }}">{{ $status }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('status')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Package number') }}</b></label>
                            <div class="search-input">
                                <input type="number" class="form-control w-100 @error('number') is-invalid @enderror" id="shift-package-number" value="0" name="number" />
                                @error('number')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Unit') }}</b></label>
                            <div class="search-input position-relative">
                                <input id="package_unit" type="number" class="form-control w-100 @error('unit') is-invalid @enderror" name="unit" value="0"/>
                                @error('unit')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Package weight') }}</b></label>
                            <div class="search-input position-relative">
                                <input id="package-weight" type="text" class="form-control w-100 @error('weight') is-invalid @enderror" name="weight"/>
                                @error('weight')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Package length') }}</b></label>
                            <div class="search-input position-relative">
                                <input id="package-length" type="text" class="form-control w-100" name="length"/>
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Package width') }}</b></label>
                            <div class="search-input position-relative">
                                <input id="package-width" type="text" class="form-control w-100" name="width"/>
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Package height') }}</b></label>
                            <div class="search-input position-relative">
                                <input id="package-height" type="text" class="form-control w-100" name="height"/>
                            </div>
                        </div>


                        <div class="search-form-group">
                            <div class="search-label d-none d-sm-block"></div>
                            <div class="search-input text-center text-sm-left">
                                <input type="submit" class="btn btn-primary" value="{{ __('Add Package') }}" />
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        @endif
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="mb-0">{{ __('Package in group') }}</h2>
        </div>
        <div class="card-body">
            @if (count($packages) == 0)
                <div class="text-center">{{ __('No Package') }}</div>
            @else
                <div class="table-responsive">
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-pg-detail-table">
                    <thead>
                            <tr>
                                <th>No</th>
                                <th>{{ __('Package Code') }}</th>
                                <th>{{ __('Warehouse Area') }}</th>
                                <th>{{ __('Unit') }}</th>
                                <th>{{ __('Unit Received') }}</th>
                                <th>{{ __('Weight(U)') }}</th>
                                <th>{{ __('Length(U)') }}</th>
                                <th>{{ __('Width(U)') }}</th>
                                <th>{{ __('Height(U)') }}</th>
                                <th>{{ __('Cuft(U)') }}</th>
                                <th>{{ __('Weight(S)') }}</th>
                                <th>{{ __('Length(S)') }}</th>
                                <th>{{ __('Width(S)') }}</th>
                                <th>{{ __('Height(S)') }}</th>
                                <th>{{ __('Cuft(S)') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Is delete')}}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($packages as $detail)
                            <tr>
                                <td>{{ ($packages->currentPage() - 1) * $packages->perPage() + $loop->iteration }}</td>
                                <td>
                                    {{ $detail->barcode }}
                                </td>
                                <td>
                                    {{ $detail->warehouseArea->name ?? ''}}
                                    <div id="barcode-{{ $loop->iteration }}" class="d-none">
                                        {!! DNS2D::getBarcodeSVG($detail->barcode, 'QRCODE') !!}
                                    </div>

                                    <div class="d-none" id="info-barcode-{{ $loop->iteration }}" style="height:120px">
                                        <div style="word-wrap: break-word; font-size:10px">
                                            <b>Product:</b> {{ $detail->packageGroup->name ?? '' }}
                                        </div>
                                        <div style="word-wrap: break-word; font-size:10px">
                                            <b>Number of unit:</b> {{ $detail->unit_number }}
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $detail->unit_number }}</td>
                                <td>{{ $detail->received_unit_number }}</td>
                                <td>{{ $detail->weight }}</td>
                                <td>{{ $detail->length }}</td>
                                <td>{{ $detail->width }}</td>
                                <td>{{ $detail->height }}</td>
                                <td>
                                @php
                                    $height = $detail->height ?? 0;
                                    $width = $detail->width ?? 0;
                                    $length = $detail->length ?? 0;
                                    $cuft = ($height * $width * $length) / (12*12*12);
                                @endphp
                                    {{  round($cuft, 4) }}
                                </td>
                                <td>{{ $detail['weight_staff'] }}</td>
                                <td>{{ $detail['length_staff'] }}</td>
                                <td>{{ $detail['width_staff'] }}</td>
                                <td>{{ $detail['height_staff'] }}</td>
                                <td>
                                @php
                                    $heightS = $detail->length_staff ?? 0;
                                    $widthS = $detail->width_staff ?? 0;
                                    $lengthS = $detail->height_staff ?? 0;
                                    $cuftS = ($heightS * $widthS * $lengthS) / (12*12*12);
                                @endphp
                                    {{  round($cuftS, 4) }}
                                </td>
                                <td>{{ App\Models\Package::$statusName[$detail->status] }}</td>
                                <td>
                                    @if(!isset($detail->deleted_at))
                                        In use
                                    @else
                                        Deleted
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewPackageBarcode('barcode-{{ $loop->iteration }}')">
                                        Print Code
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
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="preview-barcode">
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
    let warehouseAreas = @php echo json_encode($warehouseAreas) @endphp;
    let areas = @php echo json_encode($areasDetail) @endphp;
    filterInput(document.getElementById("warehouse-input"), warehouseAreas, 'dropdown-area');

    function previewBarcode(id) {
        $(".modal-body").find("embed").remove();

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

            document.getElementById('package-area').addEventListener('click', () => {
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
