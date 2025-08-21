@extends('layouts.user')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('dashboard')
        ],
        [
            'text' => 'Invoice',
            'url' => route('invoice.list')
        ],
        [
            'text' => 'Detail',
        ]
    ]
])
@endsection

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-header">
            <h2 class="mb-0">{{ __('Invoice detail') }}</h2>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Month') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $invoice['month'] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Year') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $invoice['year'] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewPDF(`{{ asset($invoice->file) }}`)">
                            {{ __('Preview') }}
                        </button>
                    </div>
                </div>

                <div class="col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Inbound') }}</b></label>
                        <div class="search-input col-form-label">
                            $ {{ round($invoice['inbound'], 4) ?? 0 }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Outbound') }}</b></label>
                        <div class="search-input col-form-label">
                            $ {{ round($invoice['outbound'], 4) ?? 0 }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Relabel') }}</b></label>
                        <div class="search-input col-form-label">
                            $ {{ round($invoice['relabel'], 4) ?? 0 }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Repack') }}</b></label>
                        <div class="search-input col-form-label">
                            $ {{ round($invoice['repack'], 4) ?? 0 }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Removal') }}</b></label>
                        <div class="search-input col-form-label">
                            $ {{ round($invoice['removal'], 4) ?? 0 }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Return') }}</b></label>
                        <div class="search-input col-form-label">
                            $ {{ round($invoice['return'], 4) ?? 0 }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Storage') }}</b></label>
                        <div class="search-input col-form-label">
                            $ {{ round($invoice['storage'], 4) ?? 0 }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Warehouse Labor') }}</b></label>
                        <div class="search-input col-form-label">
                            $ {{ round($invoice['warehouse_labor'], 4) ?? 0 }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Tax') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $invoice['tax'] ?? '' }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Balance') }}</b></label>
                        <div class="search-input col-form-label">
                            $ {{ round($invoice['balance'], 4) ?? 0 }}
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
      <div class="modal-body" id="preview-pdf">
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
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
            doc.addImage(file, 'JPEG',  0, 0, width, height);
            imgSrc = doc.output('bloburl');

        } else {
            imgSrc = file
        }

        $("#preview-pdf").find("embed").remove();
            let embed = "<embed src="+ imgSrc +" frameborder='0' width='100%' height='500px' type='application/pdf' class='preview-pdf'>"
        $("#preview-pdf").append(embed)
    }
</script>
@endsection
