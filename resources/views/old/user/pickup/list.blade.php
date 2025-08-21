@extends('layouts.user')

@section('breadcrumb')
    @include('layouts.partials.breadcrumb', [
        'items' => [
            [
                'text' => 'Dashboard',
                'url' => route('dashboard'),
            ],
            [
                'text' => 'Pickup Request',
            ],
        ],
    ])
@endsection

@section('content')
    <?php
    header('Content-Type: image/png');
    ?>
    <div class="fade-in">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="mb-0">{{ __('Pickup Request') }}</h2>
                <a class="btn btn-success" href="{{ route('pickup.create') }}">
                    {{ __('New Pickup') }}
                </a>
            </div>
            <div class="card-footer">
                @if (count($data['pickups']) == 0)
                    <div class="text-center">{{ __('No data.') }}</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-align-middle table-bordered table-striped table-sm"
                            id="staff-package-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>{{ __('Request Code') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('KG') }}</th>
                                    <th>{{ __('Count') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data['pickups'] as $pickup)
                                    <tr>
                                        <td>{{ ($data['pickups']->currentPage() - 1) * $data['pickups']->perPage() + $loop->iteration }}
                                        </td>
                                        <td>
                                            {{ $pickup->pickup_code }}

                                        </td>
                                        <td
                                        style="text-align:center"
                                        >{{ App\Models\PickupRequest::$statusName[$pickup->status] }}</td>
                                        <td
                                        style="text-align:center;font-size:16px"
                                        > <b>{{ $pickup->totalKG }}</b>
                                        </td>
                                        <td
                                            style="text-align:center"
                                        >{{ count($pickup->orderJourneys) }}</td>
                                        <td>{{ $pickup->created_date }}</td>
                                        <td>
                                            <a class="btn btn-info btn-block"
                                                href="{{ route('pickup.show', ['pickup_id' => $pickup->id]) }}">
                                                {{ __('Detail') }}
                                            </a>
                                            @if($pickup->status == App\Models\PickupRequest::NEW)
                                                <a class="btn btn-block btn-primary" href="{{ route('pickup.list') }}" 
                                                    onclick="event.preventDefault();
                                                        document.getElementById(
                                                        'delete-form-{{$pickup->id}}').submit();">
                                                    {{ __('Cancel') }} 
                                                </a> 
                                            @endif
                                            <form id="delete-form-{{$pickup->id}}" 
                                                + action="{{route('pickup.destroy', ['pickup_id' => $pickup->id])}}"
                                                method="post">
                                                @csrf @method('DELETE')
                                            </form>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                       
                    </div>
                    <div class="d-flex justify-content-center justify-content-md-end amt-16">
                        {{ $data['pickups']->appends(request()->all())->links('components.pagination') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body" id="preview-barcode">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="scan-modal" class="modal fade bd-example-scan-lg" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <video id="video" style="border: 1px solid gray; width: 100%; height: 100%"></video>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- @section('scripts')
    <script>
        let users = @php echo json_encode($users) @endphp;
        let warehouses = @php echo json_encode($warehouses) @endphp;
        let areas = @php echo json_encode($areas) @endphp;

        filterInput(document.getElementById("email-input"), users, 'dropdown-email');
        filterInput(document.getElementById("warehouse-input"), warehouses, 'dropdown-area');

        window.addEventListener('load', function() {
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
                        $('#scan-modal').on('hidden.coreui.modal', function(e) {
                            codeReader.reset();
                        })

                        document.getElementById('start-button').addEventListener('click', () => {
                            codeReader.decodeFromVideoDevice(selectedDeviceId, 'video', (result,
                                err) => {
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
                            codeReader.decodeFromVideoDevice(selectedDeviceId, 'video', (result,
                                err) => {
                                if (result) {
                                    const areaName = areas.find(area => {
                                        return area.barcode == result.text
                                    });

                                    if (areaName) {
                                        $('#warehouse-input').val(areaName.name)
                                    } else {
                                        createFlash([{
                                            type: 'error',
                                            content: 'Area code not correct !'
                                        }])
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
                    }).catch((err) => {
                        console.log(err)
                    })
            } catch (err) {
                console.log(err)
            }
        })

        function previewPDF(id) {
            $(".modal-body").find("embed").remove();

            const {
                jsPDF
            } = window.jspdf;
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
                onclone: function(clonedDoc) {
                    clonedDoc.getElementById(`info-${id}`).style.display = 'block';
                    clonedDoc.getElementById(`info-${id}`).classList.remove("d-none");
                }
            }).then(canvas2 => {
                let imgData2 = canvas2.toDataURL('image/png');

                doc.addImage(imgData, 'PNG', 10, 10, 100, 100);
                doc.addImage(imgData2, 'PNG', 120, 10, 100, 100);
                imgSrc = doc.output('bloburl');

                let embed = "<embed src=" + imgSrc +
                    " frameborder='0' width='100%' height='500px' type='application/pdf' class='preview-pdf'>"
                $(".modal-body").append(embed)
            });
        }
    </script>
@endsection --}}
