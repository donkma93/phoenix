@extends('layouts.admin')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('admin.dashboard')
        ],
        [
            'text' => 'Package Group History'
        ]
    ]
])
@endsection

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('Package Group History list') }}</h2>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.package-group-history.list') }}" class="form-horizontal" role="form">
                <div class="form-group search-form-group">
                    <label for="type" class="col-form-label search-label"><b>{{ __('Previous Email') }}</b></label>
                    <div class="search-input position-relative">
                        <input type="input" list="dropdown-previous-mail" class="form-control w-100" id="previous-mail-input" name="previous_email" value="@if (isset($oldInput['previous_email'])){{$oldInput['previous_email']}}@endif" autocomplete="off" />
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="type" class="col-form-label search-label"><b>{{ __('Email') }}</b></label>
                    <div class="search-input position-relative">
                        <input type="input" list="dropdown-email" class="form-control w-100" id="email-input" name="email" value="@if (isset($oldInput['email'])){{$oldInput['email']}}@endif" autocomplete="off" />
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="type" class="col-form-label search-label"><b>{{ __('Staff') }}</b></label>
                    <div class="search-input position-relative">
                        <input type="input" list="dropdown-staff" class="form-control w-100" id="staff-input" name="staff" value="@if (isset($oldInput['staff'])){{$oldInput['staff']}}@endif" autocomplete="off" />
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="name" class="col-form-label search-label"><b>{{ __('Previous Name') }}</b></label>
                    <div class="search-input">
                        <input type="input" list="dropdown-previous-name" class="form-control w-100" id="previous-name-input" name="previous_name" value="@if (isset($oldInput['previous_name'])){{$oldInput['previous_name']}}@endif" />
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="name" class="col-form-label search-label"><b>{{ __('Name') }}</b></label>
                    <div class="search-input">
                        <input type="input" list="dropdown-name" class="form-control w-100" id="name-input" name="name" value="@if (isset($oldInput['name'])){{$oldInput['name']}}@endif" />
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="barcode" class="col-form-label search-label"><b>{{ __('Previous Barcode') }}</b></label>
                    <div class="search-input position-relative">
                        <input type="input" id="previous_barcode" class="form-control w-100" name="previous_barcode" value="@if (isset($oldInput['previous_barcode'])){{$oldInput['previous_barcode']}}@endif" />
                        <button type="button" id="previousButton" class="btn scan-btn apy-4" data-toggle="modal" data-target="#scan-modal"><i class="fa fa-qrcode font-20"></i></button>
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="barcode" class="col-form-label search-label"><b>{{ __('Barcode') }}</b></label>
                    <div class="search-input position-relative">
                        <input type="input" id="barcode" class="form-control w-100" name="barcode" value="@if (isset($oldInput['barcode'])){{$oldInput['barcode']}}@endif" />
                        <button type="button" id="startButton" class="btn scan-btn apy-4" data-toggle="modal" data-target="#scan-modal"><i class="fa fa-qrcode font-20"></i></button>
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Type') }}</b></label>
                    <div class="search-input">
                        <select id="type" name="type" class="form-control w-100">
                            <option selected></option>
                            @foreach (App\Models\PackageGroupHistory::$typeName as $key => $type)
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

                <div class="search-form-group">
                    <div class="search-label d-none d-sm-block"></div>
                    <div class="search-input text-center text-sm-left">
                        <input class="btn btn-primary" type="submit" value="{{ __('Search') }}">
                    </div>
                </div>
            </form>
        </div>
        <div class="card-footer">
            @if (count($histories) == 0)
                <div class="text-center">{{ __('No data.') }}</div>
            @else
                <div class="table-responsive">
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="admin-pgh-list-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>{{ __('Previous Name') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Previous Barcode') }}</th>
                                <th>{{ __('Barcode') }}</th>
                                <th>{{ __('Previous User') }}</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Performer') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($histories as $history)
                                <tr>
                                    <td>{{ ($histories->currentPage() - 1) * $histories->perPage() + $loop->iteration }}</td>
                                    <td>{{ $history['previous_name'] }}</td>
                                    <td>{{ $history['name'] }}</td>
                                    <td>{{ $history['previous_barcode'] }}</td>
                                    <td>{{ $history['barcode'] }}</td>
                                    <td>{{ $history['previousUser']['email'] }}</td>
                                    <td>{{ $history['user']['email'] }}</td>
                                    <td>{{ $history['staff']['email'] }}</td>
                                    <td>{{ App\Models\PackageGroupHistory::$typeName[$history['type']] }}</td>
                                    <td>{{ $history['created_at'] }}</td>
                                    <td>
                                        <a href="{{ route('admin.package-group-history.detail', ['id' => $history['id'] ])}} " class="btn btn-info">{{ __('Detail') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center justify-content-md-end amt-16">
                    {{ $histories->appends(request()->all())->links('components.pagination') }}
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
    let users = @php echo json_encode($users) @endphp;
    filterInput(document.getElementById("email-input"), users, 'dropdown-email');
    filterInput(document.getElementById("previous-mail-input"), users, 'dropdown-previous-mail');

    let staffs = @php echo json_encode($staffs) @endphp;
    filterInput(document.getElementById("staff-input"), staffs, 'dropdown-staff');
    
    let groups = @php echo json_encode($groups) @endphp;
    filterInput(document.getElementById("name-input"), groups, 'dropdown-name');
    filterInput(document.getElementById("previous-name-input"), groups, 'dropdown-previous-name');

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

            document.getElementById('previousButton').addEventListener('click', () => {
                codeReader.decodeFromVideoDevice(selectedDeviceId, 'video', (result, err) => {
                    if (result) {
                        $('#previous_barcode').val(result.text);

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
