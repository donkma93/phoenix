@extends('layouts.user')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('dashboard')
        ],
        [
            'text' => 'Package Group'
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
    $fields = [__('No'), __('Group name'), __('Image'), __('Barcode'), __('File'),__('Total package'),__('Total unit'), ''];
@endphp

<div class="fade-in">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('Package Group list') }}</h2>
            <div>
                @if(count($remind))
                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#remind-modal">You have reorder remind</button>
                @endif
                <a class="btn btn-success" href="{{ route('package_groups.create') }}">
                    {{ __('New Package Group') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('package_groups.index') }}" class="form-horizontal" role="form">
                <div class="form-group search-form-group">
                    <label for="name" class="col-form-label search-label"><b>{{ __('Name') }}</b></label>
                    <div class="search-input">
                        <input type="input" class="form-control w-100" name="name" value="@if (isset($oldInput['name'])){{$oldInput['name']}}@endif" />
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="barcode" class="col-form-label search-label"><b>{{ __('Barcode') }}</b></label>
                    <div class="search-input position-relative">
                        <input type="input" id="barcode" class="form-control w-100" name="barcode" value="@if (isset($oldInput['barcode'])){{ $oldInput['barcode'] }}@endif" />
                        <button type="button" id="start-button" class="btn scan-btn apy-4" data-toggle="modal" data-target="#scan-modal"><i class="fa fa-qrcode font-20"></i></button>
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
            @if (count($packageGroups))
                <div class="table-responsive">
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="user-package-table">
                        <thead>
                            <tr>
                                @foreach ($fields as $field)
                                    <th>{{ $field }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($packageGroups as $packageGroup)
                                <tr>
                                    <td>{{ ($packageGroups->currentPage() - 1) * $packageGroups->perPage() + $loop->iteration }}</td>
                                    <td>{{ $packageGroup->name }}</td>
                                    <td>@if(isset($packageGroup->product->image_url))<img  width="177" height="110" src="{{ asset($packageGroup->product->image_url) }}" alt="Product image" class="img-fluid">@endif</td>
                                    <td>{{ $packageGroup->barcode }}</td>
                                    <td class="text-center">
                                        @if(isset($packageGroup->file))
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewPDF(`{{ asset($packageGroup->file) }}`)">
                                                Preview file
                                            </button>
                                        @endif
                                    </td>
                                    <td>{{ $packageGroup->package_details_count + $packageGroup->packages_count }} </td>
                                    @php
                                        $packageUnitNumber = $packageGroup->packages_sum_unit_number ?? 0;
                                        $packageDetailUnitNumber = $packageGroup->package_details_sum_unit_number ?? 0;
                                    @endphp
                                    <td>{{ $packageUnitNumber + $packageDetailUnitNumber }} </td>
                                    <td>
                                        <a class="btn btn-block btn-info" href="{{ route('package_groups.show', ['packageId' => $packageGroup->id ]) }}">
                                                {{ __('Detail') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center justify-content-md-end amt-16">
                    {{ $packageGroups->appends(request()->all())->links('components.pagination') }}
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

<!-- Modal -->
<div id="remind-modal" class="modal fade bd-example-scan-lg modal-fullsize" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reorder remind</h5>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-inventory-list-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>{{ __('Product') }}</th>
                                <th>{{ __('Sku') }}</th>
                                <th>{{ __('Store') }}</th>
                                <th>{{ __('Incoming') }}</th>
                                <th>{{ __('Available') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($remind as $inventory)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $inventory->product->name }}</td>
                                <td>{{ $inventory->sku }}</td>
                                <td>{{ $inventory->storeFulfill->name ?? '' }}</td>
                                <td>{{ $inventory->incoming }}</td>
                                <td>{{ $inventory->available }}</td>
                                {{-- <td style="text-align: center">{{ $inventory->incoming }}</td> --}}
                                <td>
                                    <a class="btn btn-info" href="{{ route('inventories.show', ['id' => $inventory->id]) }}">Detail</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
