@extends('layouts.staff')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('staff.dashboard')
        ],
        [
            'text' => 'Inventory'
        ]
    ]
])
@endsection

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Inventory list</h2>
            <form method="POST" action="{{ route('staff.inventory.updateAvailable') }}" class="form-horizontal" role="form">
                @csrf
                <input type="hidden" name="sku" id="barcode" readonly/>
                <input type="submit" style="display:none" id="submit"/>
                <div id="startButton" class="btn btn-success" data-toggle="modal" data-target="#scan-modal">Update inventory</div>
            </form>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('staff.inventory.list') }}" class="form-horizontal" role="form">
                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Product') }}</b></label>
                    <div class="search-input">
                        <input type="text" class="form-control" name="product" id="product-input" list="dropdown-product"  value="@if (isset($oldInput['product'])){{$oldInput['product']}}@endif" autocomplete="off" />
                    </div>
                </div>
                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Sku') }}</b></label>
                    <div class="search-input position-relative">
                        <input type="input" id="sku" class="form-control w-100" name="sku" value="@if (isset($oldInput['sku'])){{$oldInput['sku']}}@endif" />
                        <button type="button" id="scan-sku-button" class="btn scan-btn apy-4" data-toggle="modal" data-target="#scan-sku-modal"><i class="fa fa-qrcode font-20"></i></button>
                    </div>
                </div>
                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Store Name') }}</b></label>
                    <div class="search-input">
                        <input type="text" class="form-control" name="store" id="store-name-input" list="dropdown-store-name"  value="@if (isset($oldInput['store'])){{$oldInput['store']}}@endif" autocomplete="off" />
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
            @if (count($inventories) == 0)
                <div class="text-center">{{ __('No data.') }}</div>
            @else
                <div class="table-responsive">
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-inventory-list-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>{{ __('Product') }}</th>
                                <th>{{ __('Image') }}</th>
                                <th>{{ __('Sku') }}</th>
                                <th>{{ __('Store') }}</th>
                                <th>{{ __('Incoming') }}</th>
                                <th>{{ __('Available') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventories as $inventory)
                            <tr>
                                <td>{{ ($inventories->currentPage() - 1) * $inventories->perPage() + $loop->iteration }}</td>
                                <td>{{ $inventory->product->name }}</td>
                                <td>@if(isset($inventory->product->image_url))<img  width="177" height="110" src="{{ asset($inventory->product->image_url) }}" alt="Product image" class="img-fluid">@endif</td>
                                <td>{{ $inventory->sku }}</td>
                                <td>{{ $inventory->storeFulfill->name ?? '' }}</td>
                                <td>{{ $inventory->incoming }}</td>
                                <td>{{ $inventory->available }}</td>
                                <td>
                                    <a class="btn btn-info" href="{{ route('staff.inventory.detail', ['id' => $inventory->id]) }}">Detail</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center justify-content-md-end amt-16">
                    {{ $inventories->appends(request()->all())->links('components.pagination') }}
                </div>
            @endif
        </div>
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

<div id="scan-sku-modal" class="modal fade bd-example-scan-lg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <video id="video" style="border: 1px solid gray; width: 100%; height: 100%"></video>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
  <script type="text/javascript">
    let products = @php echo json_encode($products) @endphp;
    let stores = @php echo json_encode($stores) @endphp;

    filterInput(document.getElementById("product-input"), products, 'dropdown-product');
    filterInput(document.getElementById("store-name-input"), stores, 'dropdown-store-name');

    window.addEventListener('load', function () {
      try {
        let selectedDeviceId;
        const codeReader = new window.zxing.BrowserMultiFormatReader()
        codeReader.getVideoInputDevices()
        .then((videoInputDevices) => {
            if (videoInputDevices.length < 1) {
                console.log('No video devices found');
                return;
            }
            selectedDeviceId = videoInputDevices[0].deviceId;
            $('#scan-modal').on('hidden.coreui.modal', function (e) {
                codeReader.reset();
            })

            $('#scan-sku-modal').on('hidden.coreui.modal', function (e) {
                codeReader.reset();
            })

            document.getElementById('scan-sku-button').addEventListener('click', () => {
                codeReader.decodeFromVideoDevice(selectedDeviceId, 'video', (result, err) => {
                    if (result) {
                        $('#sku').val(result.text);

                        $('#scan-sku-modal').modal('hide');
                        codeReader.reset();
                    }
                    if (err && !(err instanceof window.zxing.NotFoundException)) {
                        console.log(err);
                        $('#scan-sku-modal').modal('hide');
                        codeReader.reset();
                    }
                })
            })

            document.getElementById('startButton').addEventListener('click', () => {
                codeReader.decodeFromVideoDevice(selectedDeviceId, 'video', (result, err) => {
                    if (result) {
                        $('#barcode').val(result.text);

                        $('#scan-modal').modal('hide');
                        codeReader.reset();
                        $('#submit').click();
                    }
                    if (err && !(err instanceof window.zxing.NotFoundException)) {
                        console.log(err);
                        $('#scan-modal').modal('hide');
                        codeReader.reset();
                    }
                })
            })
        }).catch((err) => { console.log(err)})
      } catch(err){
        console.log(err)
      }
    })
  </script>
@endsection
