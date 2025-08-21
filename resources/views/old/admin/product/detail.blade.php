@extends('layouts.admin')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('admin.dashboard')
        ],
        [
            'text' => 'Product',
            'url' => route('admin.product.list')
        ],
        [
            'text' => $product['id']
        ]
    ]
])
@endsection

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('Product detail') }}</h2>
            @if($product->package_group_id)
                <a class="btn btn-success" href="{{ route('admin.package-group.detail', ['id' => $product->package_group_id]) }}">
                    {{ __('Package Group') }}
                </a>
            @endif
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('User') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $product->user->email }}
                        </div>
                    </div>
                    
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Product Name') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $product->name }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Using status') }}</b></label>
                        <div class="search-input col-form-label">
                            @if(isset($product['deleted_at']))
                                Deleted
                            @else 
                                In use
                            @endif
                        </div>
                    </div>
                    
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Status') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ App\Models\Product::$statusName[$product->status] }}
                        </div>
                    </div>
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Category') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $product->category->name ?? '' }}
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Fulfullment Fee') }}</b></label>
                        <div class="search-input">
                            {{ $product->fulfillment_fee }}
                        </div>
                    </div>
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Extra pick Fee') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $product->extra_pick_fee }}
                        </div>
                    </div>
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Image') }}</b></label>
                        @if(!isset($product->image_url))
                            <span id="no-image-span">{{ __('No image') }}</span>
                        @endif
                    </div>
                    @if(isset($product->image_url))
                        <div class="form-group search-form-group">
                            <img id="image-upload" width="300" height="300" src="{{ asset($product->image_url) }}" alt="Product image" class="img-fluid">
                        </div>
                    @else 
                        <div class="form-group search-form-group">
                            <img id="image-upload" height="300" class="d-none" src="#" alt="your image"">
                        </div>
                    @endif

                    <form action="{{ route('admin.product.uploadImage') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" value="{{ $product['id'] }}" name="id" />
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
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('Component') }}</h2>
        </div>

        <div class="card-body">
            @if (count($components) == 0)
                <div class="text-center">{{ __('No data.') }}</div>
            @else
                <div class="table-responsive">
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-product-component-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>{{ __('Image') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Quantity') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($components as $component)
                                <tr>
                                    <td>{{ $loop->iteration  }}</td>
                                    <td>@if(isset($component->component->image_url))<img  width="177" height="110" src="{{ asset($component->component->image_url) }}" alt="Product image" class="img-fluid">@endif</td>
                                    <td>
                                        <a href="{{ route('admin.product.detail', ['id' => $component->component->id]) }}">{{  $component->component->name }}</a>
                                        
                                    </td>
                                    <td> {{ $component->quantity }} </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let categories = @php echo json_encode($categories) @endphp;

    filterInput(document.getElementById("category-input"), categories, 'dropdown-category');

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
</script>
@endsection
