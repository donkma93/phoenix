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
            'text' => 'Create Tote'
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

    <form action="{{ route('staff.tote.create') }}" method="POST" enctype="multipart/form-data">
    @csrf
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h2 class="mb-0">{{ __('Create Tote') }}</h2>
            </div>
            <div class="card-body">
                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Name') }}</b></label>
                    <div class="search-input">
                        <input id="name" type="text" class="form-control w-100 @error('name') is-invalid @enderror" name="name">
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Status') }}</b></label>
                    <div class="search-input">
                        <select name="status" class="form-control w-100 @error('status') is-invalid @enderror" name="status">
                            @foreach (App\Models\Tote::$statusName as $key => $status)
                                <option value="{{ $key }}">{{ $status }}</option>
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
                    <label class="col-form-label search-label"><b>{{ __('Warehouse') }}</b></label>
                    <div class="search-input position-relative">
                        <input type="input" class="form-control w-100" id="warehouse-input" list="dropdown-area" name="warehouse" value="@if (isset($oldInput['warehouse'])){{$oldInput['warehouse']}}@endif" autocomplete="off" />
                        <button type="button" id="area-button" class="btn scan-btn apy-4" data-toggle="modal" data-target="#scan-modal"><i class="fa fa-qrcode font-20"></i></button>
                    </div>
                </div>

                <div class="search-form-group">
                    <div class="search-label d-none d-sm-block"></div>
                    <div class="search-input text-center text-sm-left">
                        <input class="btn btn-primary" type="submit" value="{{ __('Create') }}">
                    </div>
                </div>
            </div>
        </div>
    </form>
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
