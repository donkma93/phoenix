@extends('layouts.admin')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('admin.dashboard')
        ],
        [
            'text' => 'Warehouse Area',
            'url' => route('admin.warehouseArea.list')
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
    <form action="{{ route('admin.warehouseArea.update') }}" id="delete-wa-form" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" value="{{ $areaInfo['id'] }}">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="mb-0">{{ __('Warehouse Area detail') }}</h2>
                @if(!isset($areaInfo['deleted_at']))
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirm-delete" onclick="callModal('delete')">Delete</button>
                @else
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#confirm-delete" onclick="callModal('restore')">Restore</button>
                @endif
                <input type="submit" name="delete" id="delete-submit" style="display:none"/>
            </div>
            <div class="card-body">
                <div class="form-group search-form-group">
                    <label for="first_name" class="col-form-label search-label"><b>{{ __('Area name') }}</b> </label>
                    <div class="col-form-label">
                        {{ $areaInfo['name'] }}
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Area code') }}</b> </label>
                    <div class="col-form-label amr-32">
                        {{ $areaInfo['barcode'] }}
                    </div>
                </div>

                @if(isset($areaInfo['barcode']))
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Preview code') }}</b> </label>
                            <div class="col-form-label amr-32" id="barcode-preview">{!! DNS2D::getBarcodeSVG($areaInfo['barcode'], 'QRCODE') !!}</div>
                    </div>

                    <div class="search-form-group">
                        <div class="search-label d-none d-sm-block"></div>
                        <div class="search-input text-center text-sm-left">
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewPDF()">
                                {{ __('Preview code') }}
                            </button>
                        </div>
                    </div>
                @endif

                <div class="form-group search-form-group">
                    <label for="warehouse-name" class="col-form-label search-label"><b>{{ __('Warehouse name') }}</b></label>
                    <div class="col-form-label">
                        {{ $areaInfo['warehouse']['name'] }}
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="address" class="col-form-label search-label"><b>{{ __('Address') }}</b></label>
                    <div class="col-form-label">
                        {{ $areaInfo['warehouse']['address'] }}
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="package-stored" class="col-form-label search-label"><b>{{ __('Package stored') }}</b></label>
                    <div class="col-form-label amr-32">
                        {{ $areaInfo['packages_count'] }}
                    </div>
                    <a href="{{ route('admin.package.list', ['warehouse' => $areaInfo['name']]) }}" class="btn btn-success">Check</a>
                </div>

                <div class="form-group search-form-group">
                    <label for="last_name" class="col-form-label search-label"><b>{{ __('Status') }}</b></label>
                    <div class="col-form-label">
                        @if(isset($areaInfo['deleted_at']))
                            Deleted
                        @else
                            In use
                        @endif
                    </div>
                </div>
                
                <div class="form-group search-form-group">
                    <label for="last_name" class="col-form-label search-label"><b>{{ __('Is Full') }}</b></label>
                    <div class="col-form-label amr-32">
                        @if($areaInfo['is_full'])
                            Full
                        @else 
                            Not Full
                        @endif
                    </div>
                    <input class="btn btn-primary" type="submit" name="isFull" value="@if($areaInfo['is_full'])Set Not Full @else Set Full @endif"/>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                WARNING
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer btn-delete-area">
                <button type="button" class="btn btn-default " data-dismiss="modal">Cancel</button>
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
    function callModal(type) {
        const element = $(".btn-delete-area");
        $(".btn-ok").remove();
        let btn = "<button class='btn btn-danger btn-ok' onclick='deleteArea()'>Delete</button>"
        $(".modal-body").text('Are you sure for delete this area?')
        if(type == 'restore') {
            $(".modal-body").text('Are you sure for restore this area?')
            btn = "<button class='btn btn-primary btn-ok' onclick='deleteArea()'>Restore</button>"
        }
        element.append(btn);
    }

    function deleteArea() {
        $('#delete-submit').click()
    }

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
