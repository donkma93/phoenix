@extends('layouts.staff')

@section('breadcrumb')
    @include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('staff.dashboard')
        ],
        [
            'text' => 'Request list'
        ]
    ]
])
@endsection

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-header">
            <h2 class="mb-0">{{ __('Request list') }}</h2>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('staff.request.list') }}" class="form-horizontal" role="form">
                <div class="form-group search-form-group">
                    <label for="type" class="col-form-label search-label"><b>{{ __('Email') }}</b></label>
                    <div class="search-input position-relative">
                        <input type="input" class="form-control w-100" id="email-input" list="dropdown-email" name="email" value="@if (isset($oldInput['email'])){{$oldInput['email']}}@endif" autocomplete="off" />
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="type" class="col-form-label search-label"><b>{{ __('Type') }}</b></label>
                    <div class="search-input">
                        <select id="type" name="type" class="form-control w-100">
                            <option selected></option>
                            @foreach ($requestTypes as $type)
                                <option value="{{ $type }}"
                                    @if (isset($oldInput['type']) && $oldInput['type'] == $type)
                                        selected="selected"
                                    @endif
                                >{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="status" class="col-form-label search-label"><b>{{ __('Status') }}</b></label>
                    <div class="search-input">
                        <select id="status" name="status" class="form-control w-100">
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

                <div class="form-group search-form-group">
                    <label for="barcode" class="col-form-label search-label"><b>{{ __('Unit Code') }}</b></label>
                    <div class="search-input position-relative">
                        <input type="input" id="barcode" class="form-control w-100" name="barcode" value="@if (isset($oldInput['barcode'])){{$oldInput['barcode']}}@endif" />
                        <button type="button" id="start-button" class="btn scan-btn apy-4" data-toggle="modal" data-target="#scan-modal"><i class="fa fa-qrcode font-20"></i></button>
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
            @if (count($requests) == 0)
                <div class="text-center">{{ __('No data.') }}</div>
            @else
                <div class="table-responsive">
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-request-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Group Names') }}</th>
                                <th>{{ __('Option') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Created') }}</th>
                                <th>{{ __('Start') }}</th>
                                <th>{{ __('End') }}</th>
                                <th @if (isset($oldInput['status']) && $oldInput['status'] == "2") class="th-done" @endif></th>
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
                                    <a class="btn action-btn btn-info" href="{{
                                    route('staff.request.detail', ['id' => $request->id]) }}">
                                        Detail
                                    </a>
                                    @if ($request->status == App\Models\UserRequest::STATUS_NEW)
                                        <button class="btn action-btn btn-success" onclick="updateRequest({{ $request->id }}, 1)">Start</button>
                                    @elseif ($request->status == App\Models\UserRequest::STATUS_INPROGRESS)
                                        <button class="btn action-btn btn-warning text-white" onclick="updateRequest({{ $request->id }}, 2)">Done</button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center justify-content-md-end amt-16">
                    {{ $requests->appends(request()->all())->links('components.pagination') }}
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
  <script type="text/javascript">
        let users = @php echo json_encode($users) @endphp;

        filterInput(document.getElementById("email-input"), users, 'dropdown-email');

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
            loading(true)
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
@endsection
