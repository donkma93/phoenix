@extends('layouts.staff')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('staff.dashboard')
        ],
        [
            'text' => 'Warehouse Area',
            'url' => route('staff.warehouseArea.list')
        ],
        [
            'text' => 'Detail'
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
    <form action="{{ route('staff.warehouseArea.update') }}" id="delete-wa-form" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" value="{{ $areaInfo['id'] }}">
        <input type="hidden" name="fromList" value="">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="mb-0">{{ __('Warehouse Area detail') }}</h2>
            </div>
            <div class="card-body">
                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Area name') }}</b> </label>
                    <div class="col-form-label">
                        {{ $areaInfo['name'] }}
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Area Code') }}</b> </label>
                    <div class="col-form-label amr-32">
                        {{ $areaInfo['barcode'] }}
                    </div>
                </div>
                @if(isset($areaInfo['barcode']))
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Preview Code') }}</b> </label>
                        @if(isset($areaInfo['barcode']))
                            <div class="col-form-label amr-32" id="barcode-preview">
                                @if(isset($areaInfo['barcode']))
                                    {!! DNS2D::getBarcodeSVG($areaInfo['barcode'], 'QRCODE') !!}
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="search-form-group">
                        <div class="search-label d-none d-sm-block"></div>
                        <div class="search-input text-center text-sm-left">
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewPDF()">
                                {{ __('Preview Code') }}
                            </button>
                        </div>
                    </div>
                @endif

                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Warehouse name') }}</b></label>
                    <div class="col-form-label">
                        {{ $areaInfo['warehouse']['name'] }}
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Address') }}</b></label>
                    <div class="col-form-label">
                        {{ $areaInfo['warehouse']['address'] }}
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Package stored') }}</b></label>
                    <div class="col-form-label amr-32">
                        {{ $areaInfo['packages_count'] }}
                    </div>
                    <a href="{{ route('staff.package.list', ['warehouse' => $areaInfo['name']]) }}" class="btn btn-success">Check</a>
                </div>

                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Status') }}</b></label>
                    <div class="col-form-label">
                        @if(isset($areaInfo['deleted_at']))
                            Deleted
                        @else
                            In use
                        @endif
                    </div>
                </div>
                
                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Is Full') }}</b></label>
                    <div class="col-form-label amr-32">
                        @if($areaInfo['is_full'])
                            Full
                        @else 
                            Not Full
                        @endif
                    </div>
                    @if(!$areaInfo['is_full'])
                        <input class="btn btn-primary" type="submit" name="isFull" value="Set Full"/>
                    @endif
                </div>
            </div>
        </div>
    </form>
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
    function previewPDF() {
        const { jsPDF } = window.jspdf;
        let doc = new jsPDF("p", "mm", "a4");
        let svgHtml = $(`#barcode-preview`).html();
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
