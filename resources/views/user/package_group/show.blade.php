@extends('layouts.app', [
'class' => '',
'folderActive' => '',
'elementActive' => 'package_group',
])

@section('styles')
<style>
    .table-responsive {
        overflow: unset;
    }
    .min-w-160 {
        min-width: 160px;
    }
    .card .card-footer {
        border-top: 1px solid #d8dbe0 !important;
    }
    .card .card-header {
        border-bottom: 1px solid #d8dbe0 !important;
    }
    .form-group {
        margin-bottom: 1rem;
    }
    .search-form-group {
        display: flex;
        flex-direction: column;
    }
    .search-form-group .search-input {
        flex: 1;
    }

    @media (min-width: 576px) {
        .search-form-group {
            flex-direction: row;
        }
        .search-form-group .search-input {
            max-width: 360px;
        }
    }
</style>
@endsection

@section('content')
<div class="content">
<div class="fade-in">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('Package Group detail') }}</h2>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group search-form-group">
                        <label for="name" class="col-form-label search-label min-w-160"><b>{{ __('Name') }}</b> </label>
                        <div class="search-input col-form-label">
                            {{ $packageGroup['name'] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label for="total_package" class="col-form-label search-label min-w-160"><b>{{ __('Total package') }}</b> </label>
                        <div class="search-input col-form-label">
                            {{ $totalPackages }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label for="total_package" class="col-form-label search-label min-w-160"><b>{{ __('Total unit') }}</b> </label>
                        <div class="search-input  col-form-label">
                            {{ $totalUnit }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label for="barcode" class="col-form-label search-label min-w-160"><b>{{ __('Group Code') }}</b> </label>
                        <div class="search-input col-form-label">
                            {{ $packageGroup['barcode'] }}
                        </div>
                    </div>

                    @if(isset($packageGroup['barcode']))
                        <div class="form-group search-form-group">
                            <label for="barcode" class="col-form-label search-label min-w-160"><b>{{ __('Preview') }}</b> </label>
                            <div class="search-input col-form-label show-barcode" id="group-barcode-preview">
                                {!! DNS2D::getBarcodeSVG($packageGroup['barcode'], 'QRCODE') !!}
                            </div>
                        </div>

                        <div class="search-form-group">
                            <div class="search-label min-w-160 d-none d-sm-block"></div>
                            <div class="search-input text-center text-sm-left">
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewBarcode('group-barcode-preview')">
                                    {{ __('Print Code') }}
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="col-md-6 col-lg-12 col-xl-6">
                    <div class="form-group search-form-group">
                        <label for="width" class="col-form-label search-label min-w-160"><b>{{ __('Unit width') }}</b> </label>
                        <div class="search-input col-form-label">
                            {{ $packageGroup['unit_width'] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label for="length" class="col-form-label search-label min-w-160"><b>{{ __('Unit length') }}</b> </label>
                        <div class="search-input col-form-label">
                            {{ $packageGroup['unit_length'] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label for="height" class="col-form-label search-label min-w-160"><b>{{ __('Unit height') }}</b> </label>
                        <div class="search-input col-form-label">
                            {{ $packageGroup['unit_height'] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label for="height" class="col-form-label search-label min-w-160"><b>{{ __('Unit weight') }}</b> </label>
                        <div class="search-input col-form-label">
                            {{ $packageGroup['unit_weight'] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label min-w-160"><b>{{ __('Image') }}</b></label>
                        @if(!isset($packageGroup->product->image_url))
                            <span id="no-image-span">{{ __('No image') }}</span>
                        @endif
                    </div>
                    @if(isset($packageGroup->product->image_url))
                        <div class="form-group search-form-group">
                            <img id="image-upload" width="300" height="300" src="{{ asset($packageGroup->product->image_url) }}" alt="Product image" class="img-fluid">
                        </div>
                    @else
                        <div class="form-group search-form-group">
                            <img id="image-upload" height="300" class="d-none" src="#" alt="your image"">
                        </div>
                    @endif
                    <form action="{{ route('package_groups.uploadImage') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" value="{{ $packageGroup['id'] }}" name="id" />
                        <input type="hidden" value="{{ $packageGroup['name'] }}" name="name" />
                        <input type="hidden" value="{{ $packageGroup['user_id'] }}" name="user_id" />
                        <div class="form-group search-form-group">
                            <div class="search-input">
                                <input id="image" hidden type="file" accept="image/*" class="img-picker @error('image') is-invalid @enderror" name="image" onchange="readURL(this);">
                                <div class="btn btn-info" onclick="uploadImage()">Upload image</div>
                                <input type="submit" class="btn btn-primary d-none" value="Save" id="save-button"/>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="mb-0">{{ __('Package in group') }}</h2>
        </div>
        <div class="card-body">
            @if (count($packages) == 0)
                <div class="text-center">{{ __('No Package') }}</div>
            @else
                <div class="table-responsive">
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="user-pg-detail-table">
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
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($packages as $package)
                            <tr>
                                <td>{{ ($packages->currentPage() - 1) * $packages->perPage() + $loop->iteration }}</td>
                                <td>
                                    {{ $package['barcode'] }}
                                    <div class="d-none" id="barcode-{{ $loop->iteration }}">
                                        {!! DNS2D::getBarcodeSVG($package['barcode'], 'QRCODE') !!}
                                    </div>
                                </td>
                                <td>{{ $package['warehouseArea']['name'] ?? ''}}</td>
                                <td>{{ $package['unit_number'] }}</td>
                                <td>{{ $package['received_unit_number'] }}</td>
                                <td>{{ $package['weight'] }}</td>
                                <td>{{ $package['length'] }}</td>
                                <td>{{ $package['width'] }}</td>
                                <td>{{ $package['height'] }}</td>
                                <td>
                                @php
                                    $height = $package->height ?? 0;
                                    $width = $package->width ?? 0;
                                    $length = $package->length ?? 0;
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
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center justify-content-md-end amt-16">
                    {{ $packages->appends(request()->all())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="mb-0">{{ __('Component') }}</h2>
    </div>

    <div class="card-body">
        <form action="{{ route('package_groups.createKitComponent') }}" method="POST" id="create-kit-form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" value="{{ $packageGroup->product['id'] }}" name="id" />
            <div class="table-responsive">
                <table class="table table-align-middle table-bordered table-striped table-sm" id="admin-product-component-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>{{ __('Image') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Quantity') }}</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td></td>
                            <td><input type="input" class="form-control w-100" id="component-name" list="dropdown-component" name="name" value=""  autocomplete="off" /></td>
                            <td><input type="number" class="form-control w-100" id="component-quantity" name="quantity" value="1" /></td>
                            <td><div class="btn btn-info" onclick="create()"> {{ __('Add component') }} </div></td>
                            <td></td>
                        </tr>
                        @foreach ($components as $component)
                            <tr>
                                <td>{{ $loop->iteration  }}</td>
                                <td>@if(isset($component->component->image_url))<img  width="177" height="110" src="{{ asset($component->component->image_url) }}" alt="Product image" class="img-fluid">@endif</td>
                                <td>
                                    <a href="{{ route('package_groups.show', ['packageId' => $component->component->package_group_id]) }}">{{  $component->component->name }}</a>
                                </td>
                                <td>
                                    <input type="input" class="form-control w-100" id="quantity-{{ $component->id }}" name="group" value="{{ $component->quantity }}" />
                                </td>
                                <td>
                                    <div class="btn action-btn btn-success" onclick="updateComponent({{ $component->id }})">Update</div>
                                </td>
                                <td>
                                    <div class="btn action-btn btn-danger" onclick="deleteComponent({{ $component->id }})">Delete</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>
<!-- Modal -->
<div id="scan-modal" class="modal fade bd-example-scan-lg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <video id="video" style="border: 1px solid gray; width: 100%; height: 100%"></video>
            </div>
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
</div>
@endsection

@push('scripts')
<script>
    let componentList = @php echo json_encode($componentKit) @endphp;
    /*filterInput(document.getElementById("component-name"), componentList, 'dropdown-component');*/
    createSuggestBlock(document.getElementById("component-name"), componentList, 'dropdown-component');

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

    function uploadImage() {
        $('#image').click()
    }

    function readURL(input) {
        if (input.files && input.files[0]) {
            let reader = new FileReader();

            reader.onload = function (e) {
                $('#save-button').removeClass('d-none');
                $('#no-image-span').remove();
                $('#image-upload').removeClass('d-none');
                $('#image-upload').attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    async function deleteComponent(id) {
        await $.ajax({
            type: 'POST',
            url: "{{ route('package_groups.deleteKitComponent') }}",
            data: {
                id,
                _token: '{{csrf_token()}}'
            },
            success:function(data) {
                window.location.reload();
            },
            error: function(e) {
                loading(false);
                alert('Something wrong! Please contact admin for more information!')
            }
        });
    }

    async function updateComponent(id) {
        let quantity = $(`#quantity-${id}`).val()
        if(isNaN(quantity)) {
            createFlash([{type: 'error', content: 'Please enter number'}])

            return
        }

        if(quantity < 1) {
            createFlash([{type: 'error', content: 'Please enter more than 1'}])

            return
        }

        await $.ajax({
            type: 'POST',
            url: "{{ route('package_groups.updateKitComponent') }}",
            data: {
                id,
                quantity,
                _token: '{{csrf_token()}}'
            },
            success:function(data) {
                window.location.reload();
            },
            error: function(e) {
                loading(false);
                alert('Something wrong! Please contact admin for more information!')
            }
        });
    }

    function create() {
        $('#create-kit-form').submit();
    }
</script>
@endpush
