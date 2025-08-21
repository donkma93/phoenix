@extends('layouts.app',[
'class' => '',
'folderActive' => '',
'elementActive' => 'dashboard'
])

@section('content')
<div class="content">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card ">
                <div class="card-header ">
                    <h4 class="card-title">Request list</h4>
                </div>
                <div class="card-body ">
                    <form method="get" action="" class="form-horizontal">
                        <div class="row">
                            <div class="col-xl-6 col-12">
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Email</label>
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <input type="text" id="email-input" list="dropdown-email" name="email" class="form-control"
                                                   value="@if (isset($oldInput['email'])){{$oldInput['email']}}@endif" autocomplete="off" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-12">
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Type</label>
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <select class="form-control" name="type" id="type">
                                                <option selected></option>
                                                @foreach ($requestTypes as $type)
                                                <option value="{{ $type }}"
                                                    @if (isset($oldInput['type']) && $oldInput['type'] == $type)
                                                    selected="selected"
                                                    @endif
                                                >
                                                    {{ ucfirst($type) }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-12">
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Status</label>
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <select class="form-control" name="status" id="status">
                                                <option selected></option>
                                                @foreach (App\Models\UserRequest::$statusName as $key => $status)
                                                <option value="{{ $key }}"
                                                        @if (isset($oldInput['status']) && $oldInput['status'] == $key)
                                                selected="selected"
                                                @endif
                                                >{{ $status }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-12">
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Unit Code</label>
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <input type="text" id="barcode" name="barcode" class="form-control"
                                                   value="@if (isset($oldInput['barcode'])){{$oldInput['barcode']}}@endif" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-12">
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">From Date</label>
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <input type="text" class="form-control datepicker" id="startDate"
                                                   value="@if(isset($oldInput['startDate'])){{ date('Y-m-d', strtotime($oldInput['startDate'])) }}@else{{ date('Y-m-d', strtotime('1980-01-01')) }}@endif"
                                                   name="startDate" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-12">
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">To Date</label>
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <input type="text" class="form-control datepicker" id="endDate"
                                                   value="@if(isset($oldInput['endDate'])){{ date('Y-m-d', strtotime($oldInput['endDate'])) }}@else{{ date('Y-m-d') }}@endif"
                                                   name="endDate">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <button type="submit" class="btn btn-info btn-round">Search</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card ">
                <div class="card-footer">
                    @if (count($requests) == 0)
                    <div class="text-center">{{ __('No data.') }}</div>
                    @else
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="text-primary">
                            <tr>
                                <th>#</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Group Names') }}</th>
                                <th>{{ __('Option') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Created') }}</th>
                                <th>{{ __('Start') }}</th>
                                <th>{{ __('End') }}</th>
                                <th @if (isset($oldInput['status']) && $oldInput['status'] == "2") class="th-done" @endif>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($requests as $request)
                            <tr class="row-{{ $request->id }}">
                                <td>{{ ($requests->currentPage() - 1) * $requests->perPage() + $loop->iteration }}</td>
                                <td>{{ $request->user->email }}</td>
                                <td>{{ ucfirst($request->mRequestType->name) ?? '' }}</td>
                                <td>{{ $groupNames[$request->id] ?? '' }}</td>
                                <td>{{ App\Models\UserRequest::$optionName[$request->option] ?? '' }}</td>
                                <td>
                                    @foreach (App\Models\UserRequest::$statusName as $key => $status)
                                    @if ($request->status == $key)
                                    {{ $status }}
                                    @endif
                                    @endforeach
                                </td>
                                <td>{{ $request->created_at }}</td>
                                <td>{{ $request->start_at }}</td>
                                <td>{{ $request->finish_at }}</td>
                                <td @if ((!isset($oldInput['status']) || $oldInput['status'] != App\Models\UserRequest::STATUS_DONE || $oldInput['status'] != App\Models\UserRequest::STATUS_CANCEL) && ($request->status == App\Models\UserRequest::STATUS_DONE || $request->status == App\Models\UserRequest::STATUS_CANCEL)) class="text-left" @endif>
                                <a class="btn btn-warning btn-icon btn-sm" href="{{ route('staff.request.detail', ['id' => $request->id]) }}" rel="tooltip" title="Detail">
                                    <i class="fa fa-info"></i>
                                </a>
                                @if ($request->status == App\Models\UserRequest::STATUS_NEW)
                                <button class="btn btn-info btn-icon btn-sm" onclick="updateRequest({{ $request->id }}, 1)" rel="tooltip" title="Start">
                                    <i class="fa fa-play"></i>
                                </button>
                                @elseif ($request->status == App\Models\UserRequest::STATUS_INPROGRESS)
                                <button class="btn btn-success btn-icon btn-sm" onclick="updateRequest({{ $request->id }}, 2)" rel="tooltip" title="Done">
                                    <i class="fa fa-check"></i>
                                </button>
                                @endif
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center justify-content-md-end amt-16">
                        {{ $requests->appends(request()->all())->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        // format date, default is MM/DD/YYYY
        demo.date_format = 'YYYY-MM-DD';
        // initialise Datetimepicker and Sliders
        demo.initDateTimePicker();
        if ($('.slider').length != 0) {
            demo.initSliders();
        }
    });


    let users = @php echo json_encode($users) @endphp;

    createSuggestBlock(document.getElementById("email-input"), users, 'dropdown-email');

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
                }).catch((err) => { console.log(err)})
        } catch(err){
            console.log(err)
        }
    })

    function updateRequest(id, status) {
        // loading(true)
        const className = '.row-'+ id;
        $.ajax({
            type: 'POST',
            url: "{{ route('staff.request.update') }}",
            data: {
                id,
                status,
                _token: '{{csrf_token()}}'
            },
            success:function(data) {
                let url = "{{ route('staff.request.detail', ['id' => 'id']) }}"
                url = url.replace('id', id);
                window.location.href = url
            },
            error: function() {
                loading(false)
            }
        });
    }
</script>
@endpush
