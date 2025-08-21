@extends('layouts.admin')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('admin.dashboard')
        ],
        [
            'text' => 'Product'
        ]
    ]
])
@endsection

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Product list</h2>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('admin.product.list') }}" class="form-horizontal" role="form">
                <div class="form-group search-form-group">
                    <label for="email" class="col-form-label search-label"><b>{{ __('Email') }}</b></label>
                    <div class="search-input">
                        <input type="email" class="form-control" name="email" id="email-input" list="dropdown-email" value="@if (isset($oldInput['email'])){{$oldInput['email']}}@endif" />
                    </div>
                </div>
                <div class="form-group search-form-group">
                    <label for="name" class="col-form-label search-label"><b>{{ __('Name') }}</b></label>
                    <div class="search-input">
                        <input type="text" class="form-control" name="name" value="@if (isset($oldInput['name'])){{$oldInput['name']}}@endif" />
                    </div>
                </div>
                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Status') }}</b></label>
                    <div class="search-input">
                        <select id="status" name="status" class="form-control w-100">
                            <option selected></option>
                            @foreach (App\Models\Product::$statusName as $key => $status)
                                <option value="{{ $key }}"
                                    @if (isset($oldInput['status']) && $oldInput['status'] == $key)
                                        selected="selected"
                                    @endif
                                >{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group search-form-group">
                    <label for="email" class="col-form-label search-label"><b>{{ __('Category') }}</b></label>
                    <div class="search-input">
                        <input type="text" class="form-control" name="category" id="category-input" list="dropdown-category" value="@if (isset($oldInput['category'])){{$oldInput['category']}}@endif" />
                    </div>
                </div>
                <div class="form-group search-form-group">
                    <label for="is_delete" class="col-form-label search-label"><b>{{ __('Show Deleted') }}</b></label>
                    <div class="search-input search-radio">
                        <div class="form-check form-check-inline amr-20">
                            <input class="form-check-input" id="all-verify" type="radio" value="" name="onlyDeleted"
                            @if(!isset($oldInput['onlyDeleted']))
                                checked
                            @endif
                            >
                            <label class="form-check-label" for="all-member">All</label>
                        </div>
                        <div class="form-check form-check-inline amr-20">
                            <input class="form-check-input" id="verify-only" type="radio" value="1" name="onlyDeleted"
                            @if(isset($oldInput['onlyDeleted']) && $oldInput['onlyDeleted'] == 1)
                                checked
                            @endif
                            >
                            <label class="form-check-label" for="only-deleted">Only deleted</label>
                        </div>
                        <div class="form-check form-check-inline amr-20">
                            <input class="form-check-input" id="not-verify" type="radio" value="0" name="onlyDeleted"
                            @if(isset($oldInput['onlyDeleted']) && $oldInput['onlyDeleted'] == 0)
                                checked
                            @endif>
                            <label class="form-check-label" for="not-delete">Not deleted</label>
                        </div>
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
            @if (count($products) == 0)
                <div class="text-center">{{ __('No data.') }}</div>
            @else
                <div class="table-responsive">
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="admin-product-list-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Image') }}</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Category') }}</th>
                                <th>{{ __('Fulfillment Fee') }}</th>
                                <th>{{ __('Extra pick Fee') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Use status') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            <tr>
                                <td>{{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}</td>
                                <td>{{ $product->name }}</td>
                                <td>@if(isset($product->image_url))<img  width="177" height="110" src="{{ asset($product->image_url) }}" alt="Product image" class="img-fluid">@endif</td>
                                <td>{{ $product->user->email ?? '' }}</td>
                                <td>{{ $product->category->name ?? '' }}</td>
                                <td>{{ $product->fulfillment_fee }}</td>
                                <td>{{ $product->extra_pick_fee }}</td>
                                <td>{{ App\Models\Product::$statusName[$product->status] }}</td>
                                <td>
                                    @if(isset($product->deleted_at))
                                        {{ __('Deleted') }}
                                    @else
                                        {{ __('In use') }}
                                    @endif
                                </td>
                                <td>
                                    <a class="btn btn-info" href="{{ route('admin.product.detail', ['id' => $product->id]) }}">Detail</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center justify-content-md-end amt-16">
                    {{ $products->appends(request()->all())->links('components.pagination') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let email = @php echo json_encode($users) @endphp;
    filterInput(document.getElementById("email-input"), email, 'dropdown-email');

    let categories = @php echo json_encode($categories) @endphp;
    filterInput(document.getElementById("category-input"), categories, 'dropdown-category');
</script>
@endsection
