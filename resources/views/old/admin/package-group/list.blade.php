@extends('layouts.admin')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('admin.dashboard')
        ],
        [
            'text' => 'Package Group'
        ]
    ]
])
@endsection

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('Package Group list') }}</h2>
            @if($needCompare)
                <form action="{{ route('admin.package-group.compare') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="submit" class="btn btn-success" value="Compare product"/> 
                </form>   
            @endif
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.package-group.list') }}" class="form-horizontal" role="form">
                <div class="form-group search-form-group">
                    <label for="type" class="col-form-label search-label"><b>{{ __('Email') }}</b></label>
                    <div class="search-input position-relative">
                        <input type="input" list="dropdown-email" class="form-control w-100" id="email-input" name="email" value="@if (isset($oldInput['email'])){{$oldInput['email']}}@endif" autocomplete="off" />
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="name" class="col-form-label search-label"><b>{{ __('Name') }}</b></label>
                    <div class="search-input">
                        <input type="input" list="dropdown-name" class="form-control w-100" id="name-input" name="name" value="@if (isset($oldInput['name'])){{$oldInput['name']}}@endif" />
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

                <div class="form-group search-form-group">
                    <label for="is_delete" class="col-form-label search-label"><b>{{ __('Show Deleted') }}</b></label>
                    <div class="search-input search-radio">
                        <div class="form-check form-check-inline amr-20">
                            <input class="form-check-input" id="all-verify" type="radio" value="" name="onlyDeleted"
                            @if(!isset($oldInput['onlyDeleted']))
                                checked
                            @endif
                            >
                            <label class="form-check-label" for="all-member">All</label>
                        </div>
                        <div class="form-check form-check-inline amr-20">
                            <input class="form-check-input" id="verify-only" type="radio" value="1" name="onlyDeleted"
                            @if(isset($oldInput['onlyDeleted']) && $oldInput['onlyDeleted'] == 1)
                                checked
                            @endif
                            >
                            <label class="form-check-label" for="only-deleted">Only deleted</label>
                        </div>
                        <div class="form-check form-check-inline amr-20">
                            <input class="form-check-input" id="not-verify" type="radio" value="0" name="onlyDeleted"
                            @if(isset($oldInput['onlyDeleted']) && $oldInput['onlyDeleted'] == 0)
                                checked
                            @endif>
                            <label class="form-check-label" for="not-delete">Not deleted</label>
                        </div>
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
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="admin-pg-list-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Image') }}</th>
                                <th>{{ __('Barcode') }}</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('File') }}</th>
                                <th>{{ __('Total package') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Last Updated') }}</th>
                                <th>{{ __('Created') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($packages as $package)
                                <tr>
                                    <td>{{ ($packages->currentPage() - 1) * $packages->perPage() + $loop->iteration }}</td>
                                    <td><a href="{{ route('admin.package-group.detail', ['id' => $package['id'] ])}}">{{ $package['name'] }}</a></td>
                                    <td>@if(isset($package->product->image_url))<img  width="177" height="110" src="{{ asset($package->product->image_url) }}" alt="Product image" class="img-fluid">@endif</td>
                                    <td>{{ $package['barcode'] }}</td>
                                    <td>{{ $package['user']['email'] }}</td>
                                    <td>
                                        @if(isset($package['file']))
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewPDF(`{{ asset($package['file']) }}`)">
                                                Preview file
                                            </button>
                                        @endif
                                    </td>
                                    <td>{{ $package->package_details_count + $package->packages_count }} </td>
                                    <td>@if(!isset($package['deleted_at']))
                                            In use
                                        @else
                                            Deleted
                                        @endif
                                    </td>
                                    <td>{{ $package['updated_at'] }}</td>
                                    <td>{{ $package['created_at'] }}</td>
                                    <td>
                                        <a href="{{ route('admin.package-group.detail', ['id' => $package['id'] ])}} " class="btn btn-info">{{ __('Detail') }}</a>
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
    let users = @php echo json_encode($users) @endphp;
    filterInput(document.getElementById("email-input"), users, 'dropdown-email');
    
    let groups = @php echo json_encode($groups) @endphp;
    filterInput(document.getElementById("name-input"), groups, 'dropdown-name');

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
        }).catch((err) => { console.log(err)})
      } catch(err){
        console.log(err)
      }
    })

    function previewPDF(file) {
        $("#preview-barcode").find("embed").remove();
        
        const { jsPDF } = window.jspdf;
        const splitFile = file.split('.');
        const fileType = splitFile[splitFile.length - 1];
        const validImageTypes = ['gif', 'jpeg', 'png', 'tiff', 'jpg', 'heif'];

        let imgSrc;
        if (validImageTypes.includes(fileType)) {
            let doc = new jsPDF("p", "mm", "a4");

            let width = doc.internal.pageSize.getWidth();
            let height = doc.internal.pageSize.getHeight();
            doc.addImage(file, 'JPEG',  10, 10, width, height);
            imgSrc = doc.output('bloburl');
        } else {
            imgSrc = file
        }

        
        let embed = "<embed src="+ imgSrc +" frameborder='0' width='100%' height='500px' type='application/pdf' class='preview-pdf'>"
        $("#preview-barcode").append(embed)
    }
  </script>
@endsection
