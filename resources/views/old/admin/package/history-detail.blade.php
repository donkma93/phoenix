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
            'text' => 'History',
            'url' => route('admin.package.history')
        ],
        [
            'text' => $history['id']
        ]
    ]
])
@endsection

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-header">
            <h2 class="mb-0">{{ __('Package history detail') }}</h2>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Previous Code') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $history['previous_barcode'] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Preview Code') }}</b></label>
                        <div class="col-form-label" id="previous-barcode-preview">{!! DNS2D::getBarcodeSVG($history['barcode'], 'QRCODE') !!}</div>
                    </div>
                    
                    <div class="search-form-group amb-16">
                        <div class="search-label d-none d-sm-block"></div>
                        <div class="search-input text-center text-sm-left">
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewPDF('previous-barcode-preview')">
                                {{ __('Print code') }}
                            </button>
                        </div>
                    </div>

                    <div class="d-none" id="info-previous-barcode-preview" style="height:120px; width: 170px">
                        <div style="word-wrap: break-word; font-size:8px">
                            <b>Product:</b> {{ $groupDetail['name'] ?? '' }}
                        </div>
                        <div style="word-wrap: break-word; font-size:8px">
                            <b>Number of unit:</b> {{ $history['package']['unit_number'] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Previous Status') }}</b></label>
                        <div class="col-form-label">
                            {{ App\Models\Package::$statusName[$history['previous_status']] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Warehouse Area') }}</b></label>
                        <div class="col-form-label">
                            {{ $history['warehouseArea']['name'] ?? '' }}
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Current Code') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $history['barcode'] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Preview Code') }}</b></label>
                        <div class="col-form-label" id="current-barcode-preview">{!! DNS2D::getBarcodeSVG($history['barcode'], 'QRCODE') !!}</div>
                    </div>

                    <div class="d-none" id="info-current-barcode-preview" style="height:120px; width: 170px">
                        <div style="word-wrap: break-word; font-size:8px">
                            <b>Product:</b> {{ $groupDetail['name'] ?? '' }}
                        </div>
                        <div style="word-wrap: break-word; font-size:8px">
                            <b>Number of unit:</b> {{ $history['package']['unit_number'] }}
                        </div>
                    </div>
                    
                    <div class="search-form-group amb-16">
                        <div class="search-label d-none d-sm-block"></div>
                        <div class="search-input text-center text-sm-left">
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewPDF('current-barcode-preview')">
                                {{ __('Print code') }}
                            </button>
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Current Status') }}</b></label>
                        <div class="col-form-label">
                            {{ App\Models\Package::$statusName[$history['status']] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Last Update') }}</b></label>
                        <div class="col-form-label">
                            {{ $history['previous_created_at'] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Stage') }}</b></label>
                        <div class="col-form-label">
                            {{ $history['stage'] }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <div class="row">
                <div class="col-md-6 col-lg-12 col-xl-6">
                    <h2>User measure</h2>
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Weight') }}</b></label>
                        <div class="col-form-label">
                            {{ $history['weight'] ?? 0 }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Width') }}</b></label>
                        <div class="col-form-label">
                            {{ $history['width'] ?? 0 }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Height') }}</b></label>
                        <div class="col-form-label">
                            {{ $history['height'] ?? 0 }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Length') }}</b></label>
                        <div class="col-form-label">
                            {{ $history['length'] ?? 0 }}
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-12 col-xl-6">
                    <h2>Staff measure</h2>
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Weight') }}</b></label>
                        <div class="col-form-label">
                            {{ $history['weight_staff'] ?? 0 }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Width') }}</b></label>
                        <div class="col-form-label">
                            {{ $history['width_staff'] ?? 0 }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Height') }}</b></label>
                        <div class="col-form-label">
                            {{ $history['height_staff'] ?? 0 }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Length') }}</b></label>
                        <div class="col-form-label">
                            {{ $history['length_staff'] ?? 0 }}
                        </div>
                    </div>
                </div>
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
    function previewPDF(id) {
        $(".modal-body").find("embed").remove();
        
        const { jsPDF } = window.jspdf;
        let doc = new jsPDF("l", "mm", "a4");
        const infoPackage = document.getElementById(`info-${id}`);
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
