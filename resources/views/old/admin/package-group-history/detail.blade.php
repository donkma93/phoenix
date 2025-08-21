@extends('layouts.admin')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('admin.dashboard')
        ],
        [
            'text' => 'Package Group History',
            'url' => route('admin.package-group-history.list')
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
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('Package Group History Detail') }}</h2>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Performer') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $history['staff']['email'] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Type') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ App\Models\PackageGroupHistory::$typeName[$history['type']] }}
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Date') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $history['created_at'] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Stage') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $history['stage'] }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Previous User') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $history['previousUser']['email'] ?? '' }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Previous Name') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $history['name'] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Previous code') }}</b></label>
                        <div class="search-input col-form-label">
                           {{ $history['previous_barcode'] }}
                        </div>
                    </div>

                    @if(isset($history['previous_barcode']))
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Preview code') }}</b></label>
                            <div class="col-form-label" id="previous-group-barcode-preview">{!! DNS2D::getBarcodeSVG($history['previous_barcode'], 'QRCODE') !!}</div>
                        </div>
                        
                        <div class="search-form-group amb-16">
                            <div class="search-label d-none d-sm-block"></div>
                            <div class="search-input text-center text-sm-left">
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewBarcode('previous-group-barcode-preview')">
                                    {{ __('Print code') }}
                                </button>
                            </div>
                        </div>
                    @endif

                    <div class="form-group search-form-group">
                        <label for="created" class="col-form-label search-label"><b>{{ __('Previous Width') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $history['previous_unit_width'] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label for="created" class="col-form-label search-label"><b>{{ __('Previous Length') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $history['previous_unit_length'] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label for="created" class="col-form-label search-label"><b>{{ __('Previous Height') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $history['previous_unit_height'] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label for="created" class="col-form-label search-label"><b>{{ __('Previous Weight') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $history['previous_unit_weight'] }}
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('User') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $history['user']['email'] ?? '' }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Name') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $history['name'] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Group code') }}</b></label>
                        <div class="search-input col-form-label">
                           {{ $history['barcode'] }}
                        </div>
                    </div>

                    @if(isset($history['barcode']))
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Preview code') }}</b></label>
                            <div class="col-form-label" id="group-barcode-preview">{!! DNS2D::getBarcodeSVG($history['barcode'], 'QRCODE') !!}</div>
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
                        <label for="created" class="col-form-label search-label"><b>{{ __('Width') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $history['unit_width'] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label for="created" class="col-form-label search-label"><b>{{ __('Length') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $history['unit_length'] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label for="created" class="col-form-label search-label"><b>{{ __('Height') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $history['unit_height'] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label for="created" class="col-form-label search-label"><b>{{ __('Weight') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $history['unit_weight'] }}
                        </div>
                    </div>
                </div>
            </div>
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
@endsection

@section('scripts')
<script>
    function previewBarcode(id) {
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

        $(".modal-body").find("embed").remove();
        let embed = "<embed src="+ imgSrc +" frameborder='0' width='100%' height='500px' type='application/pdf' class='preview-pdf'>"
        $(".modal-body").append(embed)
    }
</script>
@endsection
