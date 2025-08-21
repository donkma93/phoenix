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
            'text' => $package['id']
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
            <h2 class="mb-0">{{ __('Package detail') }}</h2>
            @if(!isset($package['packageGroup']['deleted_at']))
                <form action="{{ route('admin.package.delete') }}" method="POST" enctype="multipart/form-data" id="delete-form">
                @csrf
                    <input type="hidden" name="id" value="{{ $package->id }}" />
                    <input type="hidden" name="user_id" value="{{ $package['user']['id'] }}" />
                    <button type="button" class="btn @if(!isset($package['deleted_at'])) btn-danger @else btn-primary @endif" data-toggle="modal" data-target="#confirm-delete">@if(!isset($package['deleted_at'])) Delete package @else Restore package @endif</button>
                </form>
            @else 
                You can't restore this package because package's group is deleted!
            @endif
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Package code') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $package->barcode }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Preview code') }}</b></label>
                        <div class="col-form-label" id="barcode-preview">{!! DNS2D::getBarcodeSVG($package->barcode, 'QRCODE') !!}</div>
                    </div>
                    
                    <div class="search-form-group amb-16">
                        <div class="search-label d-none d-sm-block"></div>
                        <div class="search-input text-center text-sm-left">
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewBarcode('barcode-preview')">
                                {{ __('Print Code') }}
                            </button>
                        </div>
                    </div>

                    <div class="d-none" id="info-barcode" style="height:120px; width: 170px">
                        <div style="word-wrap: break-word; font-size:8px">
                            <b>Product:</b> {{ $package['packageGroup']['name'] ?? '' }}
                        </div>
                        <div style="word-wrap: break-word; font-size:8px">
                            <b>Number of unit:</b> {{ $package['unit_number'] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('User') }}</b></label>
                        <div class="col-form-label">
                            <a href="{{ route('admin.user.profile', ['id' => $package['user']['id']  ])}}">{{ $package['user']['email'] }}</a>
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Unit') }}</b></label>
                        <div class="col-form-label">
                            {{ $package['received_unit_number'] ?? 0 }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Status') }}</b></label>
                        <div class="col-form-label">
                            {{ App\Models\Package::$statusName[$package['status']] }}@if(isset($package['deleted_at']))<span class="atext-red-500">(Deleted)</span>@endif
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Warehouse Area') }}</b></label>
                        <div class="col-form-label">
                            {{ $package['warehouseArea']['name'] ?? '' }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Created') }}</b></label>
                        <div class="col-form-label">
                            {{ $package['created_at'] }}
                        </div>
                    </div>

                    @if(isset($package['unit_barcode']))
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Unit Code') }}</b></label>
                            <div class="col-form-label">
                                {{ $package['unit_barcode'] }}
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Preview U.Code') }}</b></label>
                            <div class="col-form-label" id="unit-barcode-preview">{!! DNS2D::getBarcodeSVG($package['unit_barcode'], 'QRCODE') !!}</div>
                        </div>
                        
                        <div class="search-form-group amb-16">
                            <div class="search-label d-none d-sm-block"></div>
                            <div class="search-input text-center text-sm-left">
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewPDF('unit-barcode-preview')">
                                    {{ __('Print Code') }}
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-package-detail-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Weight') }}</th>
                                <th>{{ __('Length') }}</th>
                                <th>{{ __('Width') }}</th>
                                <th>{{ __('Height') }}</th>
                                <th>{{ __('Cuft') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groups as $detail)
                            <tr>
                                <td>{{ ($groups->currentPage() - 1) * $groups->perPage() + $loop->iteration }}</td>
                                <td>{{ $detail->name }}</td>
                                <td>
                                    {{ $detail->barcode }}
                                    @if(isset($detail->barcode))
                                    <div id="barcode-{{ $loop->iteration }}" class="d-none">
                                        
                                        {!! DNS2D::getBarcodeSVG($detail->barcode, 'QRCODE') !!}
                                    </div>

                                    <div class="d-none" id="info-barcode-{{ $loop->iteration }}" style="height:120px">
                                        <div style="word-wrap: break-word; font-size:10px">
                                            <b>Product:</b> {{ $detail->name ?? '' }}
                                        </div>
                                    </div>
                                    @endif
                                </td>
                                <td>{{ $detail->unit_weight }}</td>
                                <td>{{ $detail->unit_length }}</td>
                                <td>{{ $detail->unit_width }}</td>
                                <td>{{ $detail->unit_height }}</td>
                                <td>
                                @php
                                    $heightS = $detail->unit_height ?? 0;
                                    $widthS = $detail->unit_width ?? 0;
                                    $lengthS = $detail->unit_length ?? 0;
                                    $cuftS = ($heightS * $widthS * $lengthS) / (12*12*12);
                                @endphp
                                    {{  round($cuftS, 4) }}
                                </td>
                                <td>
                                    @if(isset($detail->barcode))
                                        <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewPackageBarcode('barcode-{{ $loop->iteration }}')">
                                            Print Code
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center justify-content-md-end amt-16">
                    {{ $groups->appends(request()->all())->links('components.pagination') }}
                </div>
            </div> 
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-md-6">
                    <h2>User measure</h2>
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Package width') }}</b></label>
                        <div class="col-form-label">
                            {{ $package['width'] ?? 0 }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Package height') }}</b></label>
                        <div class="col-form-label">
                            {{ $package['height'] ?? 0  }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Package length') }}</b></label>
                        <div class="col-form-label">
                            {{ $package['length'] ?? 0  }}
                        </div>
                    </div>

                    
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Package weight') }}</b></label>
                        <div class="col-form-label">
                            {{ $package['weight'] ?? 0  }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Cuft') }}</b></label>
                        <div class="col-form-label">
                            @php
                                $cuft = ($package['width'] * $package['height'] * $package['length'] ) / (12 * 12 * 12 )
                            @endphp
                            {{ round($cuft, 4) }}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h2>Staff measure</h2>
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Package width') }}</b></label>
                        <div class="col-form-label">
                            {{ $package['width_staff'] ?? 0 }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Package height') }}</b></label>
                        <div class="col-form-label">
                            {{ $package['height_staff'] ?? 0 }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Package length') }}</b></label>
                        <div class="col-form-label">
                            {{ $package['length_staff'] ?? 0 }}
                        </div>
                    </div>

                    
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Package weight') }}</b></label>
                        <div class="col-form-label">
                            {{ $package['weight_staff'] ?? 0}}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Cuft') }}</b></label>
                        <div class="col-form-label">
                            @php
                                $cuft = ($package['width_staff'] * $package['height_staff'] * $package['length_staff'] ) / (12 * 12 * 12 )
                            @endphp
                            {{ round($cuft, 4) }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Status') }}</b></label>
                        <div class="col-form-label">
                            {{ App\Models\Package::$statusName[$package['status']] }}@if(isset($package['deleted_at']))<span class="atext-red-500">(Deleted)</span>@endif
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Warehouse Area') }}</b></label>
                        <div class="col-form-label">
                            {{ $package['warehouseArea']['name'] ?? '' }}
                        </div>
                    </div>
                </div>
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
                @if(!isset($package['deleted_at']))
                    Are you sure for delete this package?
                    <br />
                    <span class="atext-red-500"><b>(*)If this package is in new or inprogress request, you can't delete this package</b></span>
                @else 
                    Are you sure for restore this package?
                @endif
            </div>
            <div class="modal-footer btn-delete-area">
                <button type="button" class="btn btn-default " data-dismiss="modal">Cancel</button>
                @if(!isset($package['deleted_at']))
                    <button class="btn btn-danger" onclick="deletePackage()">Delete</button>
                @else 
                    <button class="btn btn-primary" onclick="deletePackage()">Restore</button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body">
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
    function deletePackage() {
        $('#delete-form').submit()
    }

    function previewPDF(id) {
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

    function previewBarcode(id) {
        $(".modal-body").find("embed").remove();
        const { jsPDF } = window.jspdf;
        let doc = new jsPDF("l", "mm", "a4");
        const infoPackage = document.getElementById("info-barcode");
        let svgHtml = $(`#${id}`).html();
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
                clonedDoc.getElementById(`info-barcode`).style.display = 'block';
                clonedDoc.getElementById(`info-barcode`).classList.remove("d-none");
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
