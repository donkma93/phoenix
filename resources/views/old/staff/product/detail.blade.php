@extends('layouts.staff')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('staff.dashboard')
        ],
        [
            'text' => 'Product',
            'url' => route('staff.product.list')
        ],
        [
            'text' => $product['id']
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
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('Product detail') }}</h2>
            <div class="search-form-group">
                @if(!isset($product->inventory->sku))
                    <form action="{{ route('staff.product.createSKU') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                        <input type="hidden" value="{{ $product['id'] }}" name="id" />
                        <input class="btn btn-info" type="submit" value="{{ __('Create SKU') }}">
                    </form>    
                @endif
                @if($product->package_group_id)
                <a class="btn btn-success aml-8" href="{{ route('staff.package-group.detail', ['id' => $product->package_group_id]) }}">
                    {{ __('Package Group') }}
                </a>
                @endif
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('staff.product.update') }}" method="POST" enctype="multipart/form-data">
            <input type="hidden" value="{{ $product['id'] }}" name="id" />
            @csrf
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
                            <label class="col-form-label search-label"><b>{{ __('Product SKU') }}</b></label>
                            <div class="search-input col-form-label">
                                {{ $product->inventory->sku ?? '' }}
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

                        @if(isset($product['deleted_at']))
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
                        @else
                            <div class="form-group search-form-group">
                                <label class="col-form-label search-label"><b>{{ __('Status') }}</b></label>
                                <div class="search-input">
                                    <select name="status" class="form-control w-100 @error('status') is-invalid @enderror" name="status">
                                        @foreach (App\Models\Product::$statusName as $key => $status)
                                            <option value="{{ $key }}"
                                            @if($product['status'] == $key)
                                                selected
                                            @endif>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group search-form-group">
                                <label class="col-form-label search-label"><b>{{ __('Category') }}</b></label>
                                <div class="search-input">
                                    <input type="input" class="form-control w-100 @error('category') is-invalid @enderror" id="category-input" list="dropdown-category" name="category"
                                        value="{{ $product->category->name ?? '' }}" autocomplete="off" />
                                    @error('category')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-6">
                        @if(isset($product['deleted_at']))
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
                        @else
                            <div class="form-group search-form-group">
                                <label class="col-form-label search-label"><b>{{ __('Fulfullment Fee') }}</b></label>
                                <div class="search-input">
                                    <input type="input" class="form-control w-100 @error('fulfillment_fee') is-invalid @enderror" name="fulfillment_fee" value="{{ $product->fulfillment_fee }}" />
                                    @error('fulfillment_fee')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group search-form-group">
                                <label class="col-form-label search-label"><b>{{ __('Extra pick Fee') }}</b></label>
                                <div class="search-input">
                                    <input type="input" class="form-control w-100 @error('extra_pick_fee') is-invalid @enderror" name="extra_pick_fee" value="{{ $product->extra_pick_fee }}" />
                                    @error('extra_pick_fee')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group search-form-group">
                                <div class="search-label d-none d-sm-block"></div>
                                <div class="search-input text-center text-sm-left">
                                    <input class="btn btn-info" type="submit" value="{{ __('Update') }}">
                                </div>
                            </div>
                        @endif

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Image') }}</b></label>
                            @if(!isset($product->image_url))
                                {{ __('No image') }}
                            @endif
                        </div>
                        @if(isset($product->image_url))
                            <div class="form-group search-form-group">
                                <img  width="300" height="300" src="{{ asset($product->image_url) }}" alt="Product image" class="img-fluid">
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('Component') }}</h2>
        </div>

        <div class="card-body">
            <form action="{{ route('staff.product.createKitComponent') }}" method="POST" id="create-kit-form" enctype="multipart/form-data">
                @csrf
                <input type="hidden" value="{{ $product['id'] }}" name="id" />
                <input type="hidden" value="{{ $product['user_id'] }}" name="user_id" />
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
                                        <a href="{{ route('staff.product.detail', ['id' => $component->component->id]) }}">{{  $component->component->name }}</a>
                                        
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
</div>
@endsection

@section('scripts')
<script>
    let categories = @php echo json_encode($categories) @endphp;
    filterInput(document.getElementById("category-input"), categories, 'dropdown-category');

    let componentList = @php echo json_encode($componentKit) @endphp;
    filterInput(document.getElementById("component-name"), componentList, 'dropdown-component');
            
    async function deleteComponent(id) {
        await $.ajax({
            type: 'POST',
            url: "{{ route('staff.product.deleteKitComponent') }}",
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
            url: "{{ route('staff.product.updateKitComponent') }}",
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
@endsection
