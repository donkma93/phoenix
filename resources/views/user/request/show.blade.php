@extends('layouts.user')

@section('breadcrumb')
    @include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('dashboard')
        ],
        [
            'text' => 'Request',
            'url' => route('requests.index')
        ],
        [
            'text' => $userRequest['id']
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

@section('styles')
<style>
.mh-form-control {
    min-height: calc(1.5em + 0.75rem + 2px);
    height: 100% !important;
}
</style>
@endsection

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-header">
            <h2 class="mb-0">{{ __('Request  detail') }}</h2>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Name') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ ucfirst($type) }}
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Status') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ App\Models\UserRequest::$statusName[$userRequest->status] }}
                        </div>
                        @if($userRequest->status == App\Models\UserRequest::STATUS_NEW)
                            <form id="user-cancel-request" method="POST" action="{{ route('requests.cancel') }}">
                                @csrf
                                <input type="hidden" value="{{ $userRequest->id }}" name="id" />
                            </form>

                            <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                                <div class="search-label d-none d-sm-block"></div>
                                <div class="search-input text-center text-sm-left">
                                    @if ($type == "add package")
                                        <div class="btn btn-info"> <a href="{{ route('requests.edit', ['userRequestId' => $userRequest->id]) }}"> {{ __('Edit request') }} </a> </div>
                                    @endif
                                    <div class="btn btn-danger" data-toggle="modal" data-target="#confirm-cancel"> {{ __('Cancel request') }} </div>
                                </div>
                            </div>
                        @endif
                        @if ($errors->has('id'))
                            <div class="col-10 col-xl-8 apx-4">
                                <p class="text-danger mb-0">
                                    {{ $errors->first('id') }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($type == "warehouse labor")
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                        <div class="form-group row">
                            <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Option') }}</b></label>
                            <div class="col-8 col-sm-9 form-control border-0">
                                {{ App\Models\UserRequest::$optionName[$userRequest->option] }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Note') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0 mh-form-control">
                            {{ $userRequest->note }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Staff') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0 mh-form-control">
                            {{  $userRequest->staff && $userRequest->staff->profile ? $userRequest->staff->profile->first_name . ' ' . $userRequest->staff->profile->last_name : '' }}
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Created At') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ $userRequest->created_at }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Started At') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ $userRequest->start_at }}
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group row">
                        <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Finished At') }}</b></label>
                        <div class="col-8 col-sm-9 form-control border-0">
                            {{ $userRequest->finish_at }}
                        </div>
                    </div>
                </div>
            </div>

            @if ($type == "outbound")
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                        <div class="form-group row">
                            <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Ship Mode') }}</b></label>
                            <div class="col-8 col-sm-9 form-control border-0">
                                {{ App\Models\UserRequest::$shipModes[$userRequest->ship_mode] ?? '' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                        <div class="form-group row">
                            <label class="col-4 col-sm-3 col-form-label"><b>{{ __('Buy Insurance') }}</b></label>
                            <div class="col-8 col-sm-9 form-control border-0">
                                {{ $userRequest->is_insurance ? "Yes" : "No" }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <hr>

            @if(in_array($type, ["add package", "removal", "return"]))
                @foreach ($userRequest->requestPackageGroups as $index => $requestPackageGroup)
                    <h3 class="amt-32">
                        <b>{{ __('Package Group') }}</b>
                        {{ ': ' . ($requestPackageGroup->packageGroupWithTrashed->name ?? 'Unknown') }}
                        @if ($requestPackageGroup->packageGroupWithTrashed->deleted_at != null)
                            <span class="atext-red-500">(Deleted)</span>
                        @endif
                    </h3>

                    <div class="ap-24">
                        <div class="row amx-n16 amb-8">
                            <b>{{ __('Package Group Infomation') }}</b>
                        </div>

                        <div class="row amx-n16 amb-8">
                            <div class="rq-pkg-field apx-16">
                                <b>{{ __('Unit Width:') }}</b>
                            </div>
                            <div class="col apx-16">{{ $requestPackageGroup->packageGroupWithTrashed->unit_width ?? '' }}</div>
                        </div>
                        <div class="row amx-n16 amb-8">
                            <div class="rq-pkg-field apx-16">
                                <b>{{ __('Unit Weight:') }}</b>
                            </div>
                            <div class="col apx-16">{{ $requestPackageGroup->packageGroupWithTrashed->unit_weight ?? '' }}</div>
                        </div>
                        <div class="row amx-n16 amb-8">
                            <div class="rq-pkg-field apx-16">
                                <b>{{ __('Unit Height:') }}</b>
                            </div>
                            <div class="col apx-16">{{ $requestPackageGroup->packageGroupWithTrashed->unit_height ?? '' }}</div>
                        </div>
                        <div class="row amx-n16 amb-8">
                            <div class="rq-pkg-field apx-16">
                                <b>{{ __('Unit Length:') }}</b>
                            </div>
                            <div class="col apx-16">{{ $requestPackageGroup->packageGroupWithTrashed->unit_length ?? '' }}</div>
                        </div>

                        <div class="row amx-n16 amb-8">
                            <div class="rq-pkg-field apx-16">
                                <b>{{ __('QR code:') }}</b>
                            </div>
                            <div class="col apx-16">{{ $requestPackageGroup->packageGroupWithTrashed->barcode ?? '' }}</div>
                        </div>
                        @if(isset($requestPackageGroup->packageGroupWithTrashed->barcode))
                            <div class="row amx-n16 amb-8">
                                <div class="rq-pkg-field apx-16">
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewBarcode('group-code-{{ $index }}')">
                                        Preview Code
                                    </button>
                                </div>
                                <div class="col apx-16" id="group-code-{{ $index }}">{!! DNS2D::getBarcodeSVG($requestPackageGroup->packageGroupWithTrashed->barcode, 'QRCODE') !!}</div>
                            </div>
                        @elseif(isset($requestPackageGroup->packageGroupWithTrashed->file))
                            <div class="row amx-n16 amb-8">
                                <div class="rq-pkg-field apx-16">
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewPDF(`{{ asset($requestPackageGroup->packageGroupWithTrashed->file) }}`)">
                                        Preview PDF
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="ap-24">
                        <div class="row amx-n16 amb-8">
                            <b>{{ __('Request Package Infomation') }}</b>
                        </div>

                        @foreach ($requestPackageGroup->requestPackages as $i => $requestPackage)
                            @if ($i)
                                <div class="row amx-n16 amb-8">
                                    <div class="rq-pkg-field apx-16">
                                        <hr>
                                    </div>
                                </div>
                            @endif

                            @if ($type == "add package")
                                <div class="row amx-n16 amb-8">
                                    <div class="rq-pkg-field apx-16">
                                        <b>{{ __('Package Width:') }}</b>
                                    </div>
                                    <div class="col apx-16">{{ $requestPackage->width }}</div>
                                </div>
                                <div class="row amx-n16 amb-8">
                                    <div class="rq-pkg-field apx-16">
                                        <b>{{ __('Package Weight:') }}</b>
                                    </div>
                                    <div class="col apx-16">{{ $requestPackage->weight }}</div>
                                </div>
                                <div class="row amx-n16 amb-8">
                                    <div class="rq-pkg-field apx-16">
                                        <b>{{ __('Package Height:') }}</b>
                                    </div>
                                    <div class="col apx-16">{{ $requestPackage->height }}</div>
                                </div>
                                <div class="row amx-n16 amb-8">
                                    <div class="rq-pkg-field apx-16">
                                        <b>{{ __('Package Length:') }}</b>
                                    </div>
                                    <div class="col apx-16">{{ $requestPackage->length }}</div>
                                </div>
                                @php
                                    $height = $package->height ?? 0;
                                    $width = $package->width ?? 0;
                                    $length = $package->length ?? 0;
                                    $cuft = ($height * $width * $length) / (12*12*12);
                                @endphp
                                <div class="row amx-n16 amb-8">
                                    <div class="rq-pkg-field apx-16">
                                        <b>{{ __('Cuft:') }}</b>
                                    </div>
                                    <div class="col apx-16">{{  round($cuft, 4) }}</div>
                                </div>

                                <div class="row amx-n16 amb-8">
                                    <div class="rq-pkg-field apx-16">
                                        <b>{{ __('Number Package:') }}</b>
                                    </div>
                                    <div class="col apx-16">{{ $requestPackage->package_number }}</div>
                                </div>
                            @endif

                            <div class="row amx-n16 amb-8">
                                <div class="rq-pkg-field apx-16">
                                    <b>{{ __($type == 'add package' ? 'Number Unit per Package:' : 'Unit Number' ) }}</b>
                                </div>
                                <div class="col apx-16">{{ $requestPackage->unit_number }}</div>
                            </div>

                            @if ($type == "add package")
                                <div class="row amx-n16 amb-8">
                                    <div class="rq-pkg-field apx-16">
                                        <b>{{ __('Received package:') }}</b>
                                    </div>
                                    <div class="col apx-16">{{ $requestPackage->received_package_number }}</div>
                                </div>
                            @endif

                            @if (in_array($type, ["add package", "removal",  "return"]))
                                <div class="row amx-n16 amb-8">
                                    <div class="rq-pkg-field apx-16">
                                        <b>{{ __($type == 'add package' ? 'Received unit:' : 'Unit completed:') }}</b>
                                    </div>
                                    <div class="col apx-16">{{ $requestPackage->received_unit_number }}</div>
                                </div>
                            @endif

                            @if ($type == "return" || $type == "removal")
                                <div class="row amx-n16 amb-8">
                                    <div class="rq-pkg-field apx-16">
                                        <b>{{ __('Unit QR code:') }}</b>
                                    </div>
                                    <div class="col apx-16">{{ $requestPackageGroup->barcode }}</div>
                                </div>
                                @if(isset($requestPackageGroup->barcode))
                                    <div class="row amx-n16 amb-8">
                                        <div class="rq-pkg-field apx-16">
                                            <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewBarcode('unit-code-{{ $index }}')">
                                                Preview Code
                                            </button>
                                        </div>
                                        <div class="col apx-16" id="unit-code-{{ $index }}">{!! DNS2D::getBarcodeSVG($requestPackageGroup->barcode, 'QRCODE') !!}</div>
                                    </div>
                                @elseif(isset($requestPackageGroup->file))
                                    <div class="row amx-n16 amb-8">
                                        <div class="rq-pkg-field apx-16">
                                            <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewPDF(`{{ asset($requestPackageGroup->file) }}`)">
                                                Preview PDF
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    </div>

                    @if(in_array($type, ["add package", "return"]) && count($requestPackageGroup->requestPackageTrackings))
                        <div class="ap-24">
                            <div class="row amx-n16 amb-8">
                                <b>{{ __('Tracking urls') }}</b>
                            </div>

                            <div class="col-sm apx-16">
                                @foreach ($requestPackageGroup->requestPackageTrackings as $ii => $tracking)
                                    <p class="m-0 apl-24 apl-sm-0">{{ $tracking->tracking_url }}</p>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(count($requestPackageGroup->requestPackageImages))
                        <div class="ap-24">
                            <div class="row amx-n16 amb-8">
                                <b>{{ __('Package images') }}</b>
                            </div>

                            <div class="col-sm apx-16">
                                @foreach ($requestPackageGroup->requestPackageImages as $ii => $image)
                                    <img  width="177" height="110" src="{{ asset($image->image_url) }}" alt="Package image" class="img-fluid">
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            @else
                @foreach ($userRequest->requestPackageGroups as $index => $requestPackageGroup)
                    <h3 class="amt-32">
                        <b>{{ __('Package Group') }}</b>
                        {{ ': ' . ($requestPackageGroup->packageGroupWithTrashed->name ?? 'Unknown') }}
                        @if ($requestPackageGroup->packageGroupWithTrashed->deleted_at != null)
                            <span class="atext-red-500">(Deleted)</span>
                        @endif
                    </h3>

                    <div class="ap-24">
                        <div class="row amx-n16 amb-8">
                            <b>{{ __('Package Group Infomation') }}</b>
                        </div>

                        <div class="row amx-n16 amb-8">
                            <div class="rq-pkg-field apx-16">
                                <b>{{ __('Unit Width:') }}</b>
                            </div>
                            <div class="col apx-16">{{ $requestPackageGroup->packageGroupWithTrashed->unit_width ?? '' }}</div>
                        </div>
                        <div class="row amx-n16 amb-8">
                            <div class="rq-pkg-field apx-16">
                                <b>{{ __('Unit Weight:') }}</b>
                            </div>
                            <div class="col apx-16">{{ $requestPackageGroup->packageGroupWithTrashed->unit_weight ?? '' }}</div>
                        </div>
                        <div class="row amx-n16 amb-8">
                            <div class="rq-pkg-field apx-16">
                                <b>{{ __('Unit Height:') }}</b>
                            </div>
                            <div class="col apx-16">{{ $requestPackageGroup->packageGroupWithTrashed->unit_height ?? '' }}</div>
                        </div>
                        <div class="row amx-n16 amb-8">
                            <div class="rq-pkg-field apx-16">
                                <b>{{ __('Unit Length:') }}</b>
                            </div>
                            <div class="col apx-16">{{ $requestPackageGroup->packageGroupWithTrashed->unit_length  ?? '' }}</div>
                        </div>


                        <div class="row amx-n16 amb-8">
                            <div class="rq-pkg-field apx-16">
                                <b>{{ __('QR code:') }}</b>
                            </div>
                            <div class="col apx-16">{{ $requestPackageGroup->packageGroupWithTrashed->barcode  ?? '' }}</div>
                        </div>
                        @if (isset($requestPackageGroup->packageGroupWithTrashed->barcode))
                            <div class="row amx-n16 amb-8">
                                <div class="rq-pkg-field apx-16">
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewBarcode('group-code-{{ $index }}')">
                                        Preview Code
                                    </button>
                                </div>
                                <div class="col apx-16" id="group-code-{{ $index }}">{!! DNS2D::getBarcodeSVG($requestPackageGroup->packageGroupWithTrashed->barcode, 'QRCODE') !!}</div>
                            </div>
                        @elseif(isset($requestPackageGroup->file))
                            <div class="row amx-n16 amb-8">
                                <div class="rq-pkg-field apx-16">
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewPDF(`{{ asset($requestPackageGroup->file) }}`)">
                                        Preview PDF
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="ap-24">
                        <div class="row amx-n16 amb-8">
                            <b>{{ __('Request Package Infomation') }}</b>
                        </div>

                        @foreach ($requestPackageGroup->requestPackages as $i => $requestPackage)
                            @if ($i)
                                <hr>
                            @endif

                            <div class="row amx-n16 amb-8">
                                <div class="rq-pkg-field apx-16">
                                    <b>{{ 'Number Package' }}</b>
                                </div>
                                <div class="col apx-16">{{ $requestPackage->package_number }}</div>
                            </div>

                            <div class="row amx-n16 amb-8">
                                <div class="rq-pkg-field apx-16">
                                    <b>{{ 'Number Unit' }}</b>
                                </div>
                                <div class="col apx-16">{{ $requestPackage->unit_number }}</div>
                            </div>
                        @endforeach

                        @if (in_array($type, ["relabel", "outbound", "repack", "warehouse labor"]))
                            <div class="row amx-n16 amb-8">
                                <div class="rq-pkg-field apx-16">
                                    <b>{{ __('Package completed:') }}</b>
                                </div>
                                <div class="col apx-16">{{ $requestPackage->received_package_number ?? '' }}</div>
                            </div>
                        @endif

                        @if ($type != "repack")
                            <div class="row amx-n16 amb-8">
                                <div class="rq-pkg-field apx-16">
                                    <b>{{ __('Unit QR code:') }}</b>
                                </div>
                                <div class="col apx-16">{{ $requestPackageGroup->barcode }}</div>
                            </div>
                            @if(isset($requestPackageGroup->barcode))
                                <div class="row amx-n16 amb-8">
                                    <div class="rq-pkg-field apx-16">
                                        <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewBarcode('unit-code-{{ $index }}')">
                                            Preview Code
                                        </button>
                                    </div>
                                    <div class="col apx-16" id="unit-code-{{ $index }}">{!! DNS2D::getBarcodeSVG($requestPackageGroup->barcode, 'QRCODE') !!}</div>
                                </div>
                            @elseif(isset($requestPackageGroup->file))
                                <div class="row amx-n16 amb-8">
                                    <div class="rq-pkg-field apx-16">
                                        <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewPDF(`{{ asset($requestPackageGroup->file) }}`)">
                                            Preview PDF
                                        </button>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                @endforeach
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

<div class="modal fade" id="confirm-cancel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                WARNING
            </div>
            <div class="modal-body">
                Are you sure for cancel request ?
            </div>
            <div class="modal-footer btn-update-package border-0">
                <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                <button type="button" class="btn btn-danger" id="cancel-button">Yes</button>
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

        $("#preview-barcode").find("embed").remove();
        let embed = "<embed src="+ imgSrc +" frameborder='0' width='100%' height='500px' type='application/pdf' class='preview-pdf'>"
        $("#preview-barcode").append(embed);
    }

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

        $("#preview-barcode").find("embed").remove();
        let embed = "<embed src="+ imgSrc +" frameborder='0' width='100%' height='500px' type='application/pdf' class='preview-pdf'>"
        $("#preview-barcode").append(embed)
    }

    $("#cancel-button").on("click", () => {
        if($("#user-cancel-request").length) {
            $("#user-cancel-request").submit()
        }
    })
</script>
@endsection
