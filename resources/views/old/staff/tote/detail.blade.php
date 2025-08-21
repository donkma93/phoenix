@extends('layouts.staff')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('staff.dashboard')
        ],
        [
            'text' => 'Tote',
            'url' => route('staff.tote.list')
        ],
        [
            'text' => $tote['id']
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
            <h2 class="mb-0">{{ __('Tote detail') }}</h2>
        </div>

        <div class="card-body">
            <form action="{{ route('staff.tote.update') }}" method="POST" enctype="multipart/form-data">
            <input type="hidden" value="{{ $tote['id'] }}" name="id" />
            @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Name') }}</b></label>
                            <div class="search-input col-form-label">
                                {{ $tote->name }}
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Preview Code') }}</b></label>
                            <div class="col-form-label" id="barcode-preview">{!! DNS2D::getBarcodeSVG($tote->barcode, 'QRCODE') !!}</div>
                        </div>
                        
                        <div class="search-form-group amb-16">
                            <div class="search-label d-none d-sm-block"></div>
                            <div class="search-input text-center text-sm-left">
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewPDF('barcode-preview')">
                                    {{ __('Print Code') }}
                                </button>
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Warehouse') }}</b></label>
                            <div class="search-input position-relative">
                                <input type="input" class="form-control w-100" id="warehouse-input" list="dropdown-area" name="warehouse" value="{{ $tote->warehouseArea->name }}" autocomplete="off" />
                                <button type="button" id="area-button" class="btn scan-btn apy-4" data-toggle="modal" data-target="#scan-modal"><i class="fa fa-qrcode font-20"></i></button>
                            </div>
                        </div>

                        
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Status') }}</b></label>
                            <div class="search-input">
                                @if(!isset($tote['deleted_at']))
                                    <select name="status" class="form-control w-100 @error('status') is-invalid @enderror" name="status">
                                        @foreach (App\Models\Tote::$statusName as $key => $status)
                                            <option value="{{ $key }}"
                                            @if($tote['status'] == $key)
                                                selected
                                            @endif>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    {{ __('Deleted') }}
                                @endif
                            </div>
                        </div>

                        <div class="search-form-group">
                            <div class="search-label d-none d-sm-block"></div>
                            <div class="search-input text-center text-sm-left">
                                <input type="submit" value="{{ __('Update') }}" class="btn btn-primary" />
                            </div>
                        </div>
                    </div>
                </div>
            </form>
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
    let warehouses = @php echo json_encode($warehouses) @endphp;
    let areas = @php echo json_encode($areas) @endphp;
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

            document.getElementById('area-button').addEventListener('click', () => {
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
</script>
@endsection
