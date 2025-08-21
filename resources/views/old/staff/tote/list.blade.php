@extends('layouts.staff')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('staff.dashboard')
        ],
        [
            'text' => 'Tote'
        ]
    ]
])
@endsection

@section('content')
<div class="fade-in">
    <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('Tote list') }}</h2>
            <a class="btn btn-success" href="{{ route('staff.tote.new') }}">
                {{ __('New Tote') }}
            </a>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('staff.tote.list') }}" class="form-horizontal" role="form">
                <div class="form-group search-form-group">
                    <label for="name" class="col-form-label search-label"><b>{{ __('Name') }}</b></label>
                    <div class="search-input">
                        <input type="text" class="form-control" name="name" value="@if (isset($oldInput['name'])){{$oldInput['name']}}@endif" />
                    </div>
                </div>
                <div class="form-group search-form-group">
                    <label for="barcode" class="col-form-label search-label"><b>{{ __('Code') }}</b></label>
                    <div class="search-input position-relative">
                        <input type="input" id="barcode" class="form-control w-100" name="barcode" value="@if (isset($oldInput['barcode'])){{$oldInput['barcode']}}@endif" />
                        <button type="button" id="start-button" class="btn scan-btn apy-4" data-toggle="modal" data-target="#scan-modal"><i class="fa fa-qrcode font-20"></i></button>
                    </div>
                </div>
                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Status') }}</b></label>
                    <div class="search-input">
                        <select id="status" name="status" class="form-control w-100">
                            <option selected></option>
                            @foreach (App\Models\Tote::$statusName as $key => $status)
                                <option value="{{ $key }}"
                                    @if (isset($oldInput['status']) && $oldInput['status'] == $key)
                                        selected="selected"
                                    @endif
                                >{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Warehouse') }}</b></label>
                    <div class="search-input position-relative">
                        <input type="input" class="form-control w-100" id="warehouse-input" list="dropdown-area" name="warehouse" value="@if (isset($oldInput['warehouse'])){{$oldInput['warehouse']}}@endif" autocomplete="off" />
                        <button type="button" id="area-button" class="btn scan-btn apy-4" data-toggle="modal" data-target="#scan-modal"><i class="fa fa-qrcode font-20"></i></button>
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
            @if (count($totes) == 0)
                <div class="text-center">{{ __('No data.') }}</div>
            @else
                <div class="table-responsive">
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-tote-list-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Barcode') }}</th>
                                <th>{{ __('Area') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($totes as $tote)
                            <tr>
                                <td>{{ ($totes->currentPage() - 1) * $totes->perPage() + $loop->iteration }}</td>
                                <td>{{ $tote->name }}</td>
                                <td>{{ $tote->barcode }}</td>
                                <td>{{ $tote->warehouseArea->name }}</td>
                                <td>{{ App\Models\Tote::$statusName[$tote->status] }}</td>
                                <td>
                                    <a class="btn btn-info" href="{{ route('staff.tote.detail', ['id' => $tote->id]) }}">Detail</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center justify-content-md-end amt-16">
                    {{ $totes->appends(request()->all())->links('components.pagination') }}
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
</script>
@endsection
