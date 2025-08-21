@extends('layouts.staff')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('staff.dashboard')
        ],
        [
            'text' => 'Warehouse Area'
        ]
    ]
])
@endsection

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('Warehouse Area List') }}</h2>
        </div>

        <form method="GET" action="{{ route('staff.warehouseArea.list') }}" class="form-horizontal" role="form">
            <div class="card-body">
                <div class="form-group search-form-group">
                    <label for="name" class="col-form-label search-label"><b>{{ __('Name') }}</b></label>
                    <div class="search-input position-relative">
                        <input type="input" id="area-name" class="form-control w-100" list="dropdown-area" name="name" value="@if (isset($oldInput['name'])){{$oldInput['name']}}@endif" />
                        <button type="button" id="startButton" class="btn scan-btn apy-4" data-toggle="modal" data-target="#scan-modal"><i class="fa fa-qrcode font-20"></i></button>
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="warehouse" class="col-form-label search-label"><b>{{ __('Warehouse') }}</b></label>
                    <div class="search-input position-relative">
                        <input type="input" class="form-control w-100" id="warehouse-input" list="dropdown-warehouse" name="warehouse" value="@if (isset($oldInput['warehouse'])){{$oldInput['warehouse']}}@endif" autocomplete="off" />
                    </div>
                </div>

                <div class="search-form-group">
                    <div class="search-label d-none d-sm-block"></div>
                    <div class="search-input text-center text-sm-left">
                        <input class="btn btn-primary" type="submit" value="{{ __('Search') }}">
                    </div>
                </div>
            </div>
        </form>

        <div class="card-footer">
            @if (count($warehouseAreas) == 0)
                <div class="text-center">{{ __('No data.') }}</div>
            @else
                <div class="table-responsive">
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-wa-list-table">
                         <thead>
                            <tr>
                                <th>No</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Area Code') }}</th>
                                <th>{{ __('Warehouse') }}</th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($warehouseAreas as $area)
                                <tr>
                                    <td>{{ ($warehouseAreas->currentPage() - 1) * $warehouseAreas->perPage() + $loop->iteration }}</td>
                                    <td>{{ $area['name'] }}</td>
                                    <td>
                                        @if(isset($area['barcode']))
                                            {{ $area['barcode'] }}
                                            <div class="d-none" id="barcode-{{ $loop->iteration }}">
                                                {!! DNS1D::getBarcodeSVG($area['barcode'], 'C128', 3, 100, 'black', false) !!}
                                            </div>
                                            <div class="d-none" id="info-barcode-{{ $loop->iteration }}" style="height:120px">
                                                <div style="word-wrap: break-word; font-size:10px">
                                                    {{ $area['name'] }}
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $area['warehouse']['name'] }}</td>
                                    <td id="status-{{$loop->iteration}}">
                                        @if($area['is_full'])
                                            Full
                                        @else
                                            <button class="btn btn-danger btn-block" onclick="setFull({{$area['id']}}, {{$loop->iteration}})">Area Full</button>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($area['barcode']))
                                            <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewPDF('barcode-{{ $loop->iteration }}')">
                                                {{ __('Preview Code') }}
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        <a class="btn btn-info btn-block" href="{{ route('staff.warehouseArea.detail', ['id' => $area['id']]) }}">{{ __('Detail') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center justify-content-md-end amt-16">
                    {{ $warehouseAreas->appends(request()->all())->links('components.pagination') }}
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
    let warehouses = @php echo json_encode($warehouses) @endphp;
    let areaList = @php echo json_encode($areaList) @endphp;

    filterInput(document.getElementById("area-name"), areaList, 'dropdown-area');
    filterInput(document.getElementById("warehouse-input"), warehouses, 'dropdown-warehouse');

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

            document.getElementById('startButton').addEventListener('click', () => {
                codeReader.decodeFromVideoDevice(selectedDeviceId, 'video', (result, err) => {
                    if (result) {
                        const areaName = areas.find(area =>{
                            return area.barcode == result.text
                        });

                        if(areaName) {
                            $('#area-name').val(areaName.name)
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
            
            doc.addImage(imgData, 'PNG', 10, 10);
            doc.addImage(imgData2, 'PNG', 30, 40, 100, 100);
            imgSrc = doc.output('bloburl');
            
            let embed = "<embed src="+ imgSrc +" frameborder='0' width='100%' height='500px' type='application/pdf' class='preview-pdf'>"
            $(".modal-body").append(embed)
        });
    }

    function setFull(id, index) {
        loading()
        $.ajax({
            type: 'POST',
            url: "{{ route('staff.warehouseArea.update') }}",
            data: {
                id,
                fromList: true,
                _token: '{{csrf_token()}}'
            },
            success: function(data) {
                $(`#status-${index}`).text("Full")
                $(`#status-${index} button`).remove()

                createFlash([{content: 'Update success!'}])
            },
            error: function(e) {
                alert('Something wrong! Please contact admin for more information!')
            },
            complete: function() {
                loading(false)
            }
        });
    }
</script>
@endsection
