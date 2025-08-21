@extends('layouts.admin')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('admin.dashboard')
        ],
        [
            'text' => 'Package',
            'url' => route('admin.package.list')
        ],
        [
            'text' => 'History'
        ]
    ]
])
@endsection

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-header">
            <h2 class="mb-0">{{ __('Package history') }}</h2>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('admin.package.history') }}" class="form-horizontal" role="form">
                <div class="form-group search-form-group">
                    <label for="barcode" class="col-form-label search-label"><b>{{ __('Package Code') }}</b></label>
                    <div class="search-input position-relative">
                        <input type="input" id="barcode" class="form-control w-100" name="barcode" value="@if (isset($oldInput['barcode'])){{$oldInput['barcode']}}@endif" />
                        <button type="button" id="start-button" class="btn scan-btn apy-4" data-toggle="modal" data-target="#scan-modal"><i class="fa fa-qrcode font-20"></i></button>
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
                    <label for="previous_status" class="col-form-label search-label"><b>{{ __('Previous Status') }}</b></label>
                    <div class="search-input">
                        <select id="previous_status" name="previous_status" class="form-control w-100">
                            <option selected></option>
                            @foreach (App\Models\Package::$statusName as $key => $status)
                                <option value="{{ $key }}"
                                    @if (isset($oldInput['previous_status']) && $oldInput['previous_status'] == $key)
                                        selected
                                    @endif
                                >{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="previous_status" class="col-form-label search-label"><b>{{ __('Type') }}</b></label>
                    <div class="search-input">
                        <select id="previous_status" name="type" class="form-control w-100">
                            <option selected></option>
                            @foreach (App\Models\PackageHistory::$typeName as $key => $type)
                                <option value="{{ $key }}"
                                    @if (isset($oldInput['type']) && $oldInput['type'] == $key)
                                        selected
                                    @endif
                                >{{ $type }}</option>
                            @endforeach
                        </select>
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
                    <label for="startDate" class="col-form-label search-label"><b>{{ __('From Date') }}</b></label>
                    <div class="search-input">
                        <input id="startDate" type="text" class="form-control w-100 date-picker @error('startDate') is-invalid @enderror" name="startDate" value="@if(isset($oldInput['startDate'])){{ date('Y-m-d', strtotime($oldInput['startDate'])) }}@else{{ date('Y-m-d', strtotime('01-01-1980')) }}@endif">
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="endDate" class="col-form-label search-label"><b>{{ __('To Date') }}</b></label>
                    <div class="search-input">
                        <input id="endDate" type="text" class="form-control w-100 date-picker @error('endDate') is-invalid @enderror" name="endDate" value="@if(isset($oldInput['endDate'])){{ date('Y-m-d', strtotime($oldInput['endDate'])) }}@else{{ date('Y-m-d') }}@endif">
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
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="admin-pg-history-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>{{ __('Previous Code') }}</th>
                                <th>{{ __('Current Code') }}</th>
                                <th>{{ __('Previous Status') }}</th>
                                <th>{{ __('Updated Status') }}</th>
                                <th>{{ __('Warehouse Area') }}</th>
                                <th>{{ __('Staff') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($packages as $package)
                            <tr>
                                <td>{{ ($packages->currentPage() - 1) * $packages->perPage() + $loop->iteration }}</td>
                                <td>{{ $package['previous_barcode'] }}</td>
                                <td>{{ $package['barcode'] }}</td>
                                <td>{{ App\Models\Package::$statusName[$package['previous_status']] ?? '' }}</td>
                                <td>{{ App\Models\Package::$statusName[$package['status']] }}</td>
                                <td>{{ $package['warehouseArea']['name'] ?? '' }}</td>
                                <td>{{ $package['staff']['email'] ?? '' }}</td>
                                <td>{{ $package['created_at'] }}</td>
                                <td>{{ App\Models\PackageHistory::$typeName[$package['type']] }}</td>
                                <td>
                                    <a href="{{ route('admin.package.history-detail', ['id' => $package['id'] ])}} " class="btn btn-info">{{ __('Detail') }}</a>
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
@endsection

@section('scripts')
<script>
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
</script>
@endsection
