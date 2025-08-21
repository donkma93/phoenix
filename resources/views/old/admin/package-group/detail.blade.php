@extends('layouts.admin')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('admin.dashboard')
        ],
        [
            'text' => 'Package Group',
            'url' => route('admin.package-group.list')
        ],
        [
            'text' => $packageGroup['id']
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
            <h2 class="mb-0">{{ __('Package Group Detail') }}</h2>
            <form action="{{ route('admin.package-group.delete') }}" method="POST" enctype="multipart/form-data" id="delete-form">
            @csrf
                @if(isset($productId))
                    <a class="btn btn-success" href="{{ route('admin.product.detail', ['id' => $productId]) }}">
                        {{ __('Product') }}
                    </a>
                @endif 
                <input type="hidden" name="id" value="{{ $packageGroup['id'] }}" />
                <button type="button" class="btn @if(!isset($packageGroup['deleted_at'])) btn-danger @else btn-primary @endif" data-toggle="modal" data-target="#confirm-delete">@if(!isset($packageGroup['deleted_at'])) Delete group @else Restore group @endif</button>
            </form>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group search-form-group">
                        <label for="name" class="col-form-label search-label"><b>{{ __('Name') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $packageGroup['name'] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Group code') }}</b></label>
                        <div class="search-input col-form-label">
                           {{ $packageGroup['barcode'] }}
                        </div>
                    </div>

                    @if(isset($packageGroup['barcode']))
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Preview code') }}</b></label>
                            <div class="col-form-label" id="group-barcode-preview">{!! DNS2D::getBarcodeSVG($packageGroup['barcode'], 'QRCODE') !!}</div>
                        </div>
                        
                        <div class="search-form-group amb-16">
                            <div class="search-label d-none d-sm-block"></div>
                            <div class="search-input text-center text-sm-left">
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewBarcode('group-barcode-preview')">
                                    {{ __('Print code') }}
                                </button>
                            </div>
                        </div>
                    @endif

                    <div class="form-group search-form-group">
                        <label for="barcode" class="col-form-label search-label"><b>{{ __('User') }}</b></label>
                        <div class="search-input col-form-label">
                            <a href="{{ route('admin.user.profile', ['id' => $packageGroup['user']['id']  ])}}">{{ $packageGroup['user']['email'] }}</a>
                        </div>
                    </div>

                    @if(!isset($packageGroup['deleted_at']))
                        <form action="{{ route('admin.package-group.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                            <div class="form-group search-form-group">
                                <label for="barcode" class="col-form-label search-label"><b>{{ __('Change to User') }}</b></label>
                                <div class="search-input position-relative">
                                    <input type="hidden" name="id" value="{{ $packageGroup['id'] }}" />
                                    <input type="input" class="form-control w-100 @error('email') is-invalid @enderror" id="email-input" list="dropdown-email" name="email" value="@if (isset($oldInput['email'])){{$oldInput['email']}}@endif" autocomplete="off" />
                                    <span class="atext-red-500">(*)If this group is in new or inprogress request, you can't change this group</span>
                                </div>
                            </div>

                            <div class="search-form-group amb-16">
                                <div class="search-label d-none d-sm-block"></div>
                                <div class="search-input text-center text-sm-left">
                                    <input type="submit" value="Change" class="btn btn-warning"/>
                                </div>
                            </div>
                        </form>
                    @endif 

                    <div class="form-group search-form-group">
                        <label for="created" class="col-form-label search-label"><b>{{ __('Status') }}</b></label>
                        <div class="search-input col-form-label">
                            @if(!isset($packageGroup['deleted_at']))
                                In use
                            @else
                                Deleted
                            @endif
                        </div>
                    </div>
                    

                    <div class="form-group search-form-group">
                        <label for="created" class="col-form-label search-label"><b>{{ __('Created') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $packageGroup['created_at'] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label for="created" class="col-form-label search-label"><b>{{ __('Packages') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $packageGroup['packages_count'] }}
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('File') }}</b></label>
                        <div class="search-input">
                            @if(isset($packageGroup['file']))
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewPDF(`{{ asset($packageGroup['file']) }}`)" >
                                    Preview file
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Unit width') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $packageGroup['unit_width'] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Unit length') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $packageGroup['unit_length'] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Unit height') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $packageGroup['unit_height'] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Unit weight') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $packageGroup['unit_weight'] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Image') }}</b></label>
                        @if(!isset($packageGroup->product->image_url))
                            {{ __('No image') }}
                        @endif
                    </div>
                    @if(isset($packageGroup->product->image_url))
                        <div class="form-group search-form-group">
                            <img  width="300" height="300" src="{{ asset($packageGroup->product->image_url) }}" alt="Product image" class="img-fluid">
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-footer">
            @if (count($packages) == 0)
                <div class="text-center">No data.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="admin-pg-detail-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>{{ __('Package Code') }}</th>
                                <th>{{ __('Warehouse Area') }}</th>
                                <th>{{ __('Unit') }}</th>
                                <th>{{ __('Unit Received') }}</th>
                                <th>{{ __('Weight(U)') }}</th>
                                <th>{{ __('Length(U)') }}</th>
                                <th>{{ __('Width(U)') }}</th>
                                <th>{{ __('Height(U)') }}</th>
                                <th>{{ __('Cuft(U)') }}</th>
                                <th>{{ __('Weight(S)') }}</th>
                                <th>{{ __('Length(S)') }}</th>
                                <th>{{ __('Width(S)') }}</th>
                                <th>{{ __('Height(S)') }}</th>
                                <th>{{ __('Cuft(S)') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Is delete')}}</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($packages as $package)
                            <tr>
                                <td>{{ ($packages->currentPage() - 1) * $packages->perPage() + $loop->iteration }}</td>
                                <td>
                                    {{ $package['barcode'] }}
                                </td>
                                <td>
                                    {{ $package['warehouseArea']['name'] ?? ''}}
                                    <div id="barcode-{{ $loop->iteration }}" class="d-none">
                                        {!! DNS2D::getBarcodeSVG($package->barcode, 'QRCODE') !!}
                                    </div>

                                    <div class="d-none" id="info-barcode-{{ $loop->iteration }}" style="height:100px">
                                        <div style="word-wrap: break-word; font-size:8px">
                                            <b>Product:</b> {{ $package->packageGroup->name ?? '' }}
                                        </div>
                                        <div style="word-wrap: break-word; font-size:8px">
                                            <b>Number of unit:</b> {{ $package->unit_number }}
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $package['unit_number'] }}</td>
                                <td>{{ $package['received_unit_number'] }}</td>
                                <td>{{ $package['weight'] }}</td>
                                <td>{{ $package['length'] }}</td>
                                <td>{{ $package['width'] }}</td>
                                <td>{{ $package['height'] }}</td>
                                <td>
                                @php
                                    $height = $package->length ?? 0;
                                    $width = $package->width ?? 0;
                                    $length = $package->height ?? 0;
                                    $cuft = ($height * $width * $length) / (12*12*12);
                                @endphp
                                    {{  round($cuft, 4) }}
                                </td>
                                <td>{{ $package['weight_staff'] }}</td>
                                <td>{{ $package['length_staff'] }}</td>
                                <td>{{ $package['width_staff'] }}</td>
                                <td>{{ $package['height_staff'] }}</td>
                                <td>
                                @php
                                    $heightS = $package->length_staff ?? 0;
                                    $widthS = $package->width_staff ?? 0;
                                    $lengthS = $package->height_staff ?? 0;
                                    $cuftS = ($heightS * $widthS * $lengthS) / (12*12*12);
                                @endphp
                                    {{  round($cuftS, 4) }}
                                </td>
                                <td>{{ App\Models\Package::$statusName[$package['status']] }}</td>
                                <td>
                                    @if(!isset($package['deleted_at']))
                                        In use
                                    @else
                                        Deleted
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.package.detail', ['id' => $package['id'] ])}} " class="btn btn-info">{{ __('Detail') }}</a>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewPackageBarcode('barcode-{{ $loop->iteration }}')">
                                        Preview code
                                    </button>
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

<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                WARNING
            </div>
            <div class="modal-body">
                @if(!isset($packageGroup['deleted_at']))
                    Are you sure for delete this group?
                    </br>
                    <b>Product</b> of this group will be deleted!
                    </br>
                    <b>All package</b> in this group will be deleted!
                    </br>
                    <span class="atext-red-500"><b>(*)If this group is in new or inprogress request, you can't delete this group</b></span>
                @else 
                    Are you sure for restore this group?
                    </br>
                    <b>Product</b> of this group will be restore!
                    </br>
                    <b>All package</b> in this group will be restore!
                @endif
            </div>
            <div class="modal-footer btn-delete-area">
                <button type="button" class="btn btn-default " data-dismiss="modal">Cancel</button>
                @if(!isset($packageGroup['deleted_at']))
                    <button class="btn btn-danger" onclick="deleteGroup()">Delete</button>
                @else
                    <button class="btn btn-primary" onclick="deleteGroup()">Restore</button>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function deleteGroup() {
        $('#delete-form').submit()
    }    

    let emails = @php echo json_encode($emails) @endphp;
    filterInput(document.getElementById("email-input"), emails, 'dropdown-email');

    function previewPDF(file) {
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

        $(".modal-body").find("embed").remove();
        let embed = "<embed src="+ imgSrc +" frameborder='0' width='100%' height='500px' type='application/pdf' class='preview-pdf'>"
        $(".modal-body").append(embed)
    }

    function previewBarcode(id) {
        $(".modal-body").find("embed").remove();
        
        const { jsPDF } = window.jspdf;
        let doc = new jsPDF("p", "mm", "a4");
        let svgHtml = $(`#${id}`).html();
        if (svgHtml) {
            svgHtml = svgHtml.replace(/\r?\n|\r/g, '').trim();
        }
  
        let canvas = document.createElement('canvas');
        let context = canvas.getContext('2d');
        v = canvg.Canvg.fromString(context, svgHtml);
        v.start()

        let imgData = canvas.toDataURL('image/png');
       
        doc.addImage(imgData, 'PNG', 10, 10, 100, 100);
        imgSrc = doc.output('bloburl');

        let embed = "<embed src="+ imgSrc +" frameborder='0' width='100%' height='500px' type='application/pdf' class='preview-pdf'>"
        $(".modal-body").append(embed)
    }

    function previewPackageBarcode(id) {
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
            
            doc.addImage(imgData, 'PNG', 10, 10, 100, 100);
            doc.addImage(imgData2, 'PNG', 120, 10, 100, 100);
            imgSrc = doc.output('bloburl');
    
            let embed = "<embed src="+ imgSrc +" frameborder='0' width='100%' height='500px' type='application/pdf' class='preview-pdf'>"
            $(".modal-body").append(embed)
        });
    }
</script>
@endsection
