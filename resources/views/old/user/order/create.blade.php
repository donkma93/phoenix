@extends('layouts.user')

@section('breadcrumb')
    @include('layouts.partials.breadcrumb', [
        'items' => [
            [
                'text' => 'Dashboard',
                'url' => route('dashboard'),
            ],
            [
                'text' => 'Order',
                'url' => route('orders.index'),
            ],
            [
                'text' => 'Create',
            ],
        ],
    ])
@endsection

@if (session('success'))
    @section('flash')
        @include('layouts.partials.flash', [
            'messages' => [
                [
                    'content' => session('success'),
                ],
            ],
        ])
    @endsection
@endif

@if (session('fail'))
    @section('flash')
        @include('layouts.partials.flash', [
            'messages' => [
                [
                    'content' => session('fail'),
                    'type' => 'error',
                ],
            ],
        ])
    @endsection
@endif

@php
    $needScroll = old('product') != null;
    $errorData = session('errorData')['request'] ?? null;
@endphp

@section('content')
    <div class="fade-in">
        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">{{ __('Create New Order') }}</h2>
            </div>

            {{-- Manual --}}
            <div class="card-body" id="manual">
                <div>
                    <form id="createForm" method="POST" action="{{ route('orders.store') }}" class="form-horizontal" role="form">
                        @csrf

                        <div id="content">
                            <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                                <h3 class="amb-4">{{ __('Create Manual - Receiver Information') }}</h3>
                            </div>

                            <div class="amb-8 apy-8 addition-form">
                                <div class="row amx-n4">
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Order Number') }}</b>
                                        <input type="text" class="form-control" name="order_number"
                                            value="{{ $errorData['order_number'] ?? (old('order_number') ?? '') }}" />
                                    </div>
                                </div>
                                <div class="row amx-n4 amb-20">
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        @if ($errors->has('order_number'))
                                            <div class="col-10 col-xl-8 apx-4">
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('order_number') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="amb-8 apy-8 addition-form">
                                <div class="row amx-n4">
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Shipping Name *') }}</b>
                                        <input type="text" class="form-control" name="shipping_name"
                                            value="{{ $errorData['shipping_name'] ?? (old('shipping_name') ?? '') }}" />
                                    </div>
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Shipping Company') }}</b>
                                        <input type="text" class="form-control" name="shipping_company"
                                            value="{{ $errorData['shipping_company'] ?? (old('shipping_company') ?? '') }}" />
                                    </div>
                                </div>
                                <div class="row amx-n4 amb-20">
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        @if ($errors->has('shipping_name'))
                                            <div class="col-10 col-xl-8 apx-4">
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('shipping_name') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        @if ($errors->has('shipping_company'))
                                            <div class="col-10 col-xl-8 apx-4">
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('shipping_company') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="amb-8 apy-8 addition-form">
                                <div class="row amx-n4">
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Shipping Country *') }}</b>
                                        <input type="text" class="form-control" name="shipping_country"
                                            placeholder="Example: 'US' or 'DE'"
                                            value="{{ $errorData['shipping_country'] ?? (old('shipping_country') ?? '') }}" />
                                    </div>
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Shipping Street *') }}</b>
                                        <input type="text" class="form-control" name="shipping_street"
                                            value="{{ $errorData['shipping_street'] ?? (old('shipping_street') ?? '') }}" />
                                    </div>
                                </div>
                                <div class="row amx-n4 amb-20">
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        @if ($errors->has('shipping_country'))
                                            <div class="col-10 col-xl-8 apx-4">
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('shipping_country') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        @if ($errors->has('shipping_street'))
                                            <div class="col-10 col-xl-8 apx-4">
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('shipping_street') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="amb-8 apy-8 addition-form">
                                <div class="row amx-n4">
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Shipping Address 1') }}</b>
                                        <input type="text" class="form-control" name="shipping_address1"
                                            value="{{ $errorData['shipping_address1'] ?? (old('shipping_address1') ?? '') }}" />
                                    </div>
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Shipping Address 2') }}</b>
                                        <input type="text" class="form-control" name="shipping_address2"
                                            value="{{ $errorData['shipping_address2'] ?? (old('shipping_address2') ?? '') }}" />
                                    </div>
                                </div>
                                <div class="row amx-n4 amb-20">
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        @if ($errors->has('shipping_address1'))
                                            <div class="col-10 col-xl-8 apx-4">
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('shipping_address1') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        @if ($errors->has('shipping_address2'))
                                            <div class="col-10 col-xl-8 apx-4">
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('shipping_address2') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="amb-8 apy-8 addition-form">
                                <div class="row amx-n4">
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Shipping Province *') }}</b>
                                        <input type="text" class="form-control" name="shipping_province"
                                            placeholder="Example: 'CA' or 'NY'"
                                            value="{{ $errorData['shipping_province'] ?? (old('shipping_province') ?? '') }}" />
                                    </div>
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Shipping City *') }}</b>
                                        <input type="text" class="form-control" name="shipping_city"
                                            placeholder="Example: 'SAN DIEGO'"
                                            value="{{ $errorData['shipping_city'] ?? (old('shipping_city') ?? '') }}" />
                                    </div>
                                </div>
                                <div class="row amx-n4 amb-20">
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        @if ($errors->has('shipping_province'))
                                            <div class="col-10 col-xl-8 apx-4">
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('shipping_province') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        @if ($errors->has('shipping_city'))
                                            <div class="col-10 col-xl-8 apx-4">
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('shipping_city') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="amb-8 apy-8 addition-form">
                                <div class="row amx-n4">
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Postal code / zip *') }}</b>
                                        <input type="text" class="form-control" name="shipping_zip"
                                            value="{{ $errorData['shipping_zip'] ?? (old('shipping_zip') ?? '') }}" />
                                    </div>
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Shipping Phone') }}</b>
                                        <input type="text" class="form-control" name="shipping_phone"
                                            value="{{ $errorData['shipping_phone'] ?? (old('shipping_phone') ?? '') }}" />
                                    </div>
                                </div>
                                <div class="row amx-n4 amb-20">
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        @if ($errors->has('shipping_zip'))
                                            <div class="col-10 col-xl-8 apx-4">
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('shipping_zip') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        @if ($errors->has('shipping_phone'))
                                            <div class="col-10 col-xl-8 apx-4">
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('shipping_phone') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                              <div class="amb-8 apy-8 addition-form">
                                <div class="row amx-n4">
                                   <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">

                                        <b>{{ __('Package width (inch)') }}</b>
                                        <input
                                            type="text" class="form-control"
                                            name="package_width"
                                            value="{{ $errorData['package_width'] ?? old('package_width') ?? '' }}"
                                        />
                                    </div>
                                    <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Package height (inch)') }}</b>
                                        <input
                                            type="text" class="form-control"
                                            name="package_height"
                                            value="{{ $errorData['package_height'] ?? old('package_height') ?? '' }}"
                                        />
                                    </div>
                                </div>
                                  <div class="row amx-n4">
                                   <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Package length (inch)') }}</b>
                                        <input
                                            type="text" class="form-control"
                                            name="package_length"
                                            value="{{ $errorData['package_length'] ?? old('package_length') ?? '' }}"
                                        />
                                    </div>
                                   <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                        <b>{{ __('Package weight (lbs)') }}</b>
                                        <input
                                            type="text" class="form-control"
                                            name="package_weight"
                                            value="{{ $errorData['package_weight'] ?? old('package_weight') ?? '' }}"
                                        />
                                    </div>
                                </div>
                           
                            </div>

                             @if (session('errorData') !== null)
                                <div class="amb-8 apy-8 addition-form">
                                    <div class="row amx-n4 amb-20">
                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                @foreach (session('errorData')['errorMsg'] as $index => $error)
                                                    <p class="text-danger mb-0">
                                                        {{ $error }}
                                                    </p>
                                                @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if (old('product'))
                                @foreach (old('product') as $index => $oldProduct)
                                    <div class="amb-8 apy-8 addition-form" id="{{ 'product_' . $index }}">
                                        <div id="{{ 'product_' . $index . '_form' }}">
                                            <div class="row amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <b>{{ __('Product Type *') }}</b>
                                                    <select id="{{ 'product_' . $index . '_select' }}" name="{{ 'product[' . $index . '][id]' }}" class="form-control">
                                                        @foreach ($products as $product)
                                                            <option value="{{ $product->id }}"
                                                                @if (isset($oldProduct['id']) && $oldProduct['id'] == $product->id)
                                                                    selected="selected"
                                                                @endif
                                                            >{{ $product->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <b>{{ __('Product Unit Number *') }}</b>
                                                    <input
                                                        type="number" class="form-control"
                                                        name="{{ 'product[' . $index . '][unit_number]' }}" placeholder="Product Unit Number" step="any" min="1"
                                                        value="{{ $oldProduct['unit_number'] ?? '' }}"
                                                    />
                                                </div>
                                                <div class="col d-flex align-items-center apx-12 amb-8">
                                                    <i class="fa fa-close atext-gray-500 font-20 pointer line-height-1 mt-3" onclick="deleteElement(`{{ 'product_' . $index }}`)"></i>
                                                </div>
                                            </div>
                                            <div class="row amx-n4 amb-20">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    @if ($errors->has('product.' . $index . '.id'))
                                                        <div class="col-10 col-xl-8 apx-4">
                                                            <p class="text-danger mb-0">
                                                                {{ $errors->first('product.' . $index . '.id') }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    @if ($errors->has('product.' . $index . '.unit_number'))
                                                        <div class="col-10 col-xl-8 apx-4">
                                                            <p class="text-danger mb-0">
                                                                {{ $errors->first('product.' . $index . '.unit_number') }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- <div class="row amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <b>{{ __('Product SKU *') }}</b>
                                                    <input
                                                        type="text" class="form-control"
                                                        name="{{ "product[{$index}][sku]" }}" placeholder="SKU"
                                                        value="{{ $oldProduct['sku'] ?? '' }}"
                                                    />
                                                </div>
                                            </div> --}}
                                            {{-- <div class="row amx-n4 amb-20">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    @if ($errors->has("product.{$index}.sku"))
                                                        <div class="col-10 col-xl-8 apx-4">
                                                            <p class="text-danger mb-0">
                                                                {{ $errors->first("product.{$index}.sku") }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div> --}}

                                            <hr>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Group Action') }}</b></label>
                            <div class="search-input">
                                <button type="button" class="btn btn-secondary apx-16 amr-8" onclick="addProduct()">
                                    {{ __('Add Product') }}
                                </button>
                                @if ($errors->has('product'))
                                    <p class="text-danger mb-0">
                                        {{ $errors->first('product') }}
                                    </p>
                                @endif
                            </div>
                        </div>


                            <div class="search-form-group">
                                <div class="search-label d-none d-sm-block"></div>
                                <div class="search-input text-center text-sm-left">
                                    <input class="btn btn-primary" type="submit" value="{{ __('Create') }}">
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let groupId = 0;

        function uploadImage() {
            $("#order_file").click();
        }

        $(document).ready(function() {

            const needScroll = "{{ $needScroll }}";
            if (needScroll) {
                $([document.documentElement, document.body]).animate({
                    scrollTop: $("#manual").offset().top
                }, 1000);
            }

            $('#order_file').change(function() {
                try {
                    $('#order_file_name').text($('#order_file')[0].files[0].name);
                } catch (error) {
                    $('#order_file_name').text("No file selected");
                }
            });
        });

        function deleteElement(id) {
            $(`#${id}`).remove();
        }

        function handleSelectProduct(groupId, item) {
            const sku = $('option:selected', item).attr('data-sku');
            $(`input[name='product[${groupId}][sku]']`).val(sku);
        }

        function addProduct() {
            while ($(`#product_${groupId}`).length) {
                groupId += 1;
            }

               $('#content').append(`
            <div class="amb-8 apy-8 addition-form" id="product_${groupId}">
                <div id="product_${groupId}_form">
                    <div class="row amb-20 amx-n4">
                        <div class="product-line col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <b>{{ __('Product Type *') }}</b>
                            <select 
                            
                            id="product_${groupId}_select" data-id="${groupId}" name="product[${groupId}][id]" class="form-control"
                               onchange="handleSelectProduct(${groupId}, this)"
                            >
                             <option 
                                    value=""
                                    >Select product</option>
                                @foreach ($products as $product)
                                    <option 
                                 
                                    value="{{ $product->id }}"
                                    data-sku="{{$product->inventory->sku}}"
                                    >{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <b>{{ __('Product Unit Number *') }}</b>
                            <input
                                type="number" class="form-control"
                                name="product[${groupId}][unit_number]" placeholder="Product Unit Number" step="any" min="1"
                            />
                        </div>
                        <div class="col d-flex align-items-center apx-12 amb-8">
                            <i class="fa fa-close atext-gray-500 font-20 pointer line-height-1 mt-3" onclick="deleteElement('product_${groupId}')"></i>
                        </div>
                    </div>


                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <b>{{ __('Product SKU *') }}</b>
                            <input
                                type="text" class="form-control"
                                name="product[${groupId}][sku]" placeholder="SKU"
                            />
                        </div>
                    </div>

                    <hr>
                </div>
            </div>
        `);
        }

        function uploadOrderFile() {
            $('#order_file').click();
        }
    </script>
@endsection
