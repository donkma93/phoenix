@extends('layouts.user')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('dashboard')
        ],
        [
            'text' => 'Request'
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
@php
    $fields = [__('No'), __('Type'), __('Status'), __('Package Group'), __('Package'), __('Start'), __('End'), __('Date'),  ''];
@endphp
    <div class="fade-in">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="mb-0">{{ __('Request list') }}</h2>
                <a class="btn btn-success" href="{{ route('requests.create') }}">
                    {{ __('New Request') }}
                </a>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('requests.index') }}" class="form-horizontal" role="form">
                    <div class="form-group search-form-group">
                        <label for="type" class="col-form-label search-label"><b>{{ __('Type') }}</b></label>
                        <div class="search-input">
                            <select id="type" name="type" class="form-control w-100">
                                <option selected></option>
                                @foreach ($requestTypes as $type)
                                    <option value="{{ $type }}"
                                        @if (isset($oldInput['type']) && $oldInput['type'] == $type)
                                            selected="selected"
                                        @endif
                                    >{{ ucfirst($type) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label for="status" class="col-form-label search-label"><b>{{ __('Status') }}</b></label>
                        <div class="search-input">
                            <select id="status" name="status" class="form-control w-100">
                                <option selected></option>
                                @foreach (App\Models\UserRequest::$statusName as $key => $status)
                                    <option value="{{ $key }}"
                                        @if (isset($oldInput['status']) && $oldInput['status'] == $key)
                                            selected="selected"
                                        @endif
                                    >{{ $status }}</option>
                                @endforeach
                            </select>
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
                @if (count($userRequests))
                    <div class="table-responsive">
                        <table class="table table-align-middle table-bordered table-striped table-sm" id="user-request-table">
                            <thead>
                                <tr>
                                    @foreach ($fields as $field)
                                        <th>{{ $field }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($userRequests as $index => $userRequest)
                                    @php
                                        $group = [];
                                        $groupCount = count($userRequest->requestPackageGroups);

                                        $totalCount = 0;
                                        foreach ($userRequest->requestPackageGroups as $key => $requestPackageGroup) {
                                            $group[] = count($requestPackageGroup->requestPackages);
                                            $totalCount += count($requestPackageGroup->requestPackages);
                                        }

                                        $totalCount = $totalCount > 0 ? $totalCount : 1;
                                        $requestIteration = $loop->iteration;

                                        $type = $userRequest->mRequestType->name;
                                    @endphp

                                    @if ($type == "outbound")
                                        <tr>
                                            <td>{{ ($userRequests->currentPage() - 1) * $userRequests->perPage() + $requestIteration }}</td>
                                            <td>{{  ucfirst($type) }}</td>
                                            <td>{{  App\Models\UserRequest::$statusName[$userRequest->status] }}</td>
                                            <td>
                                                <div  style="text-align: left">
                                                    <b>{{ __('Name:') }}</b> {{ $requestPackageGroup->packageGroupWithTrashed->name ?? 'Unknown' }}
                                                    @if ($requestPackageGroup->packageGroupWithTrashed->deleted_at != null)
                                                        <span class="atext-red-500">(Deleted)</span>
                                                    @endif
                                                </div>
                                                <div  style="text-align: left"> <b>{{ __('Unit Width:') }}</b> {{ $requestPackageGroup->packageGroupWithTrashed->unit_width ?? 'Unknown' }} </div>
                                                <div  style="text-align: left"> <b>{{ __('Unit Weight:') }}</b> {{ $requestPackageGroup->packageGroupWithTrashed->unit_weight ?? 'Unknown' }} </div>
                                                <div  style="text-align: left"> <b>{{ __('Unit Height:') }}</b> {{ $requestPackageGroup->packageGroupWithTrashed->unit_height ?? 'Unknown' }} </div>
                                                <div  style="text-align: left"> <b>{{ __('Unit Length:') }}</b> {{ $requestPackageGroup->packageGroupWithTrashed->unit_length ?? 'Unknown' }} </div>

                                                @if (isset($requestPackageGroup->packageGroupWithTrashed->barcode))
                                                    <div class="row amx-n16 amt-8">
                                                        <div class="col rq-pkg-field apx-16" style="text-align: left">
                                                            <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewBarcode('outbound-group-code-{{ $userRequest->id }}-{{ $index }}')" style="min-width: 115px">
                                                                Preview Code
                                                            </button>
                                                        </div> <br><br>
                                                        <div class="col apx-16" id="outbound-group-code-{{ $userRequest->id }}-{{ $index }}">{!! DNS2D::getBarcodeSVG($requestPackageGroup->packageGroupWithTrashed->barcode, 'QRCODE') !!}</div>
                                                    </div>
                                                @elseif(isset($requestPackageGroup->file))
                                                    <div class="row amx-n16 amb-8">
                                                        <div class="rq-pkg-field apx-16">
                                                            <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewPDF(`{{ asset($requestPackageGroup->file) }}`)" >
                                                                Preview PDF
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div> <b>{{ __('Ship mode: ') }}</b> {{ App\Models\UserRequest::$shipModes[$userRequest->ship_mode] ?? '' }} </div>
                                                <div> <b>{{ __('Buy insurance: ') }}</b> {{ $userRequest->is_insurance ? "Yes" : "No" }} </div>
                                            </td>
                                            <td>{{ $userRequest->start_at }}</td>
                                            <td>{{ $userRequest->finish_at }}</td>
                                            <td>{{ $userRequest->created_at }}</td>
                                            <td>
                                                <a class="btn btn-block btn-info" href="{{
                                                    route('requests.show', ['userRequestId' => $userRequest->id]) }}">
                                                        {{ __('Detail') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @else
                                        @foreach ($userRequest->requestPackageGroups as $i => $requestPackageGroup)
                                            <tr>
                                                <td rowspan="{{ $totalCount }}">{{ ($userRequests->currentPage() - 1) * $userRequests->perPage() + $requestIteration }}</td>
                                                <td rowspan="{{ $totalCount }}">{{  ucfirst($type) }}</td>
                                                <td rowspan="{{ $totalCount }}">{{  App\Models\UserRequest::$statusName[$userRequest->status] }}</td>
                                                @if(count($requestPackageGroup->requestPackages) > 0)
                                                    @foreach ($requestPackageGroup->requestPackages as $ii => $requestPackage)
                                                        @if (!$ii)
                                                            <td rowspan="{{ $group[$i] }}">
                                                                <div  style="text-align: left">
                                                                    <b>{{ __('Name:') }}</b> {{ $requestPackageGroup->packageGroupWithTrashed->name ?? 'Unknown' }}
                                                                    @if ($requestPackageGroup->packageGroupWithTrashed->deleted_at != null)
                                                                        <span class="atext-red-500">(Deleted)</span>
                                                                    @endif
                                                                </div>
                                                                <div  style="text-align: left"> <b>{{ __('Unit Width:') }}</b> {{ $requestPackageGroup->packageGroupWithTrashed->unit_width ?? 'Unknown' }} </div>
                                                                <div  style="text-align: left"> <b>{{ __('Unit Weight:') }}</b> {{ $requestPackageGroup->packageGroupWithTrashed->unit_weight ?? 'Unknown' }} </div>
                                                                <div  style="text-align: left"> <b>{{ __('Unit Height:') }}</b> {{ $requestPackageGroup->packageGroupWithTrashed->unit_height ?? 'Unknown' }} </div>
                                                                <div  style="text-align: left"> <b>{{ __('Unit Length:') }}</b> {{ $requestPackageGroup->packageGroupWithTrashed->unit_length ?? 'Unknown' }} </div>

                                                                @if (isset($requestPackageGroup->packageGroupWithTrashed->barcode))
                                                                    <div class="row amx-n16 amt-8">
                                                                        <div class="col rq-pkg-field apx-16" style="text-align: left">
                                                                            <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewBarcode('group-code-{{ $userRequest->id }}-{{ $i }}-{{ $ii }}')" style="min-width: 115px">
                                                                                Preview Code
                                                                            </button>
                                                                        </div> <br><br>
                                                                        <div class="col apx-16" id="group-code-{{ $userRequest->id }}-{{ $i }}-{{ $ii }}">{!! DNS2D::getBarcodeSVG($requestPackageGroup->packageGroupWithTrashed->barcode, 'QRCODE') !!}</div>
                                                                    </div>
                                                                @elseif(isset($requestPackageGroup->file))
                                                                    <div class="row amx-n16 amb-8">
                                                                        <div class="rq-pkg-field apx-16">
                                                                            <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewPDF(`{{ asset($requestPackageGroup->file) }}`)" >
                                                                                Preview PDF
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </td>
                                                        @endif

                                                        <td style="text-align: left">
                                                            @if (in_array($type, ["add package"]))
                                                                <div> <b>{{ __('Number Package:') }}</b> {{ $requestPackage->package_number }} </div>
                                                                <div> <b>{{ __('Number Unit per Package:') }}</b> {{ $requestPackage->unit_number }} </div>
                                                            @endif

                                                            @if (in_array($type, ["removal", "return"]))
                                                                <div> <b>{{ __('Unit Number:') }}</b> {{ $requestPackage->unit_number }} </div>
                                                            @endif

                                                            @if (in_array($type, ["relabel", "repack", "outbound", "warehouse labor"]))
                                                                <div> <b>{{ __('Number Package:') }}</b> {{ $requestPackage->package_number }} </div>
                                                                <div> <b>{{ __('Number Unit:') }}</b> {{ $requestPackage->unit_number }} </div>
                                                            @endif

                                                            @if ($type != "repack")
                                                                <div class="row amx-n16 amb-8">
                                                                    <div class="col rq-pkg-field apx-16">
                                                                        <b>{{ __('Unit QR code:') }}</b>
                                                                    </div>
                                                                    <div class="col apx-16">{{ $requestPackageGroup->barcode }}</div>
                                                                </div>
                                                                @if(isset($requestPackageGroup->barcode))
                                                                    <div class="row amx-n16 amb-8">
                                                                        <div class="col rq-pkg-field apx-16">
                                                                            <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewBarcode('unit-code-{{ $i }}-{{ $ii }}')" style="min-width: 115px">
                                                                                Preview Code
                                                                            </button>
                                                                        </div>
                                                                        <div class="col apx-16" id="unit-code-{{ $i }}-{{ $ii }}">{!! DNS2D::getBarcodeSVG($requestPackageGroup->barcode, 'QRCODE') !!}</div>
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
                                                        </td>

                                                        <td>{{ $userRequest->start_at }}</td>
                                                        <td>{{ $userRequest->finish_at }}</td>
                                                        <td>{{ $userRequest->created_at }}</td>

                                                        @if (!$i && !$ii)
                                                            <td rowspan="{{ $totalCount }}">
                                                                @if ($userRequest->status == App\Models\UserRequest::STATUS_NEW && $type == "add package")
                                                                    <a class="btn btn-block btn-primary"
                                                                        href="{{ route('requests.edit', ['userRequestId' => $userRequest->id]) }}">
                                                                            {{ __('Edit') }}
                                                                    </a>
                                                                @endif

                                                                <a class="btn btn-block btn-info" href="{{
                                                                    route('requests.show', ['userRequestId' => $userRequest->id]) }}">
                                                                        {{ __('Detail') }}
                                                                </a>
                                                            </td>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <td>
                                                        @foreach($userRequest->requestPackages[0]->packageDetails as $packageDetail) 
                                                            <div  style="text-align: left">
                                                                <b>{{ __('Name:') }}</b> {{ $packageDetail->packageGroup->name ?? 'Unknown' }}
                                                                @if ($packageDetail->packageGroup->deleted_at != null)
                                                                    <span class="atext-red-500">(Deleted)</span>
                                                                @endif
                                                            </div>
                                                            <div  style="text-align: left"> <b>{{ __('Unit Width:') }}</b> {{ $packageDetail->packageGroup->unit_width ?? 'Unknown' }} </div>
                                                            <div  style="text-align: left"> <b>{{ __('Unit Weight:') }}</b> {{ $packageDetail->packageGroup->unit_weight ?? 'Unknown' }} </div>
                                                            <div  style="text-align: left"> <b>{{ __('Unit Height:') }}</b> {{ $packageDetail->packageGroup->unit_height ?? 'Unknown' }} </div>
                                                            <div  style="text-align: left"> <b>{{ __('Unit Length:') }}</b> {{ $packageDetail->packageGroup->unit_length ?? 'Unknown' }} </div>

                                                            @if (isset($packageDetail->packageGroup->barcode))
                                                                <div class="row amx-n16 amt-8">
                                                                    <div class="col rq-pkg-field apx-16" style="text-align: left">
                                                                        <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewBarcode('group-code-{{ $userRequest->id }}-{{ $i }}-{{ $packageDetail->id }}')" style="min-width: 115px">
                                                                            Preview Code
                                                                        </button>
                                                                    </div> <br><br>
                                                                    <div class="col apx-16" id="group-code-{{ $userRequest->id }}-{{ $i }}-{{ $packageDetail->id }}">{!! DNS2D::getBarcodeSVG($packageDetail->packageGroup->barcode, 'QRCODE') !!}</div>
                                                                </div>
                                                            @elseif(isset($requestPackageGroup->file))
                                                                <div class="row amx-n16 amb-8">
                                                                    <div class="rq-pkg-field apx-16">
                                                                        <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewPDF(`{{ asset($requestPackageGroup->file) }}`)" >
                                                                            Preview PDF
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </td>
                                                    <td style="text-align: left">
                                                        @if (in_array($type, ["add package"]))
                                                            <div> <b>{{ __('Number Package:') }}</b> {{ count($userRequest->requestPackages) }} </div>
                                                            <div> <b>{{ __('Number Unit per Package:') }}</b> {{ $userRequest->requestPackages[0]->unit_number }} </div>
                                                        @endif

                                                        @if (in_array($type, ["removal", "return"]))
                                                            @php
                                                                $unitNumber = 0;
                                                                foreach($userRequest->requestPackages as $package) {
                                                                    $unitNumber += $package->unit_number;
                                                                }
                                                            @endphp
                                                            <div> <b>{{ __('Unit Number:') }}</b> {{ $unitNumber }} </div>
                                                        @endif

                                                        @if (in_array($type, ["relabel", "repack", "outbound", "warehouse labor"]))
                                                            @php
                                                                $unitNumber = 0;
                                                                foreach($userRequest->requestPackages as $package) {
                                                                    $unitNumber += $package->unit_number;
                                                                }
                                                            @endphp
                                                            <div> <b>{{ __('Number Package:') }}</b> {{ count($userRequest->requestPackages) }} </div>
                                                            <div> <b>{{ __('Number Unit:') }}</b> {{ $unitNumber }} </div>
                                                        @endif

                                                        @if ($type != "repack")
                                                            <div class="row amx-n16 amb-8">
                                                                <div class="col rq-pkg-field apx-16">
                                                                    <b>{{ __('Unit QR code:') }}</b>
                                                                </div>
                                                                <div class="col apx-16">{{ $requestPackageGroup->barcode }}</div>
                                                            </div>
                                                            @if(isset($requestPackageGroup->barcode))
                                                                <div class="row amx-n16 amb-8">
                                                                    <div class="col rq-pkg-field apx-16">
                                                                        <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewBarcode('unit-code-{{ $i }}')" style="min-width: 115px">
                                                                            Preview Code
                                                                        </button>
                                                                    </div>
                                                                    <div class="col apx-16" id="unit-code-{{ $i }}">{!! DNS2D::getBarcodeSVG($requestPackageGroup->barcode, 'QRCODE') !!}</div>
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
                                                    </td>

                                                    <td>{{ $userRequest->start_at }}</td>
                                                    <td>{{ $userRequest->finish_at }}</td>
                                                    <td>{{ $userRequest->created_at }}</td>

                                                    <td rowspan="{{ $totalCount }}">
                                                        @if ($userRequest->status == App\Models\UserRequest::STATUS_NEW && $type == "add package")
                                                            <a class="btn btn-block btn-primary"
                                                                href="{{ route('requests.edit', ['userRequestId' => $userRequest->id]) }}">
                                                                    {{ __('Edit') }}
                                                            </a>
                                                        @endif

                                                        <a class="btn btn-block btn-info" href="{{
                                                            route('requests.show', ['userRequestId' => $userRequest->id]) }}">
                                                                {{ __('Detail') }}
                                                        </a>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @endif

                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center justify-content-md-end amt-16">
                        {{ $userRequests->appends(request()->all())->links('components.pagination') }}
                    </div>
                @else
                    <div class="text-center">{{ __('No data.') }}</div>
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
</script>
@endsection
