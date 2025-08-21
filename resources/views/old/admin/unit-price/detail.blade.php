@extends('layouts.admin')

@section('breadcrumb')
    @include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('admin.dashboard')
        ],
        [
            'text' => 'Unit Price',
            'url' => route('admin.unit-price.list')
        ],
        [
            'text' => isset($mRequestType['name']) ? ($mRequestType['name'] != 'add package' ? ucfirst($mRequestType['name']) : 'Inbound') : 'Storage'
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
            <h2 class="mb-0">{{ __('Unit Price Detail') }}</h2>
            {{-- <button class="btn btn-info" data-toggle="modal" data-target="#modal-add-new">
                {{ __('New Unit Price') }}
            </button> --}}
        </div>
        <div class="card-body">
            @if (is_array($unitPrices) && count($unitPrices) == 0)
                <div class="text-center">No data.</div>
            @else
                <form action="{{ route('admin.unit-price.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                        @if(isset($mRequestType['id']))
                            <input type="hidden" name="type" value="{{ $mRequestType['name'] }}" />
                            @if(in_array($mRequestType['name'], ['relabel', 'removal']))
                                <div class="table-responsive">
                                    <table class="table table-align-middle table-bordered table-striped table-sm" id="admin-request-detail-table">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                {{-- <th>{{ __('Length') }}</th>
                                                <th>{{ __('Weight') }}</th> --}}
                                                <th>{{ __('Min Unit') }}</th>
                                                <th>{{ __('Max Unit') }}</th>
                                                <th>{{ __('Unit Price') }}</th>
                                                <th>{{ __('Oversize Unit Price') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($unitPrices as $unitPrice)
                                            <tr>
                                                <input type="hidden" name="{{ 'unit['.$loop->iteration.'][id]' }}" value="{{ $unitPrice->id }}" />
                                                <td>{{ ($unitPrices->currentPage() - 1) * $unitPrices->perPage() + $loop->iteration }}</td>
                                                {{-- <td>
                                                    <input type="text" class="form-control w-100 @error('unit.'.$loop->iteration.'.length') is-invalid @enderror" name="{{ 'unit['.$loop->iteration.'][length]' }}" value="{{ $unitPrice->length }}">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control w-100 @error('unit.'.$loop->iteration.'.weight') is-invalid @enderror" name="{{ 'unit['.$loop->iteration.'][weight]' }}" value="{{ $unitPrice->weight }}">
                                                </td> --}}
                                                <td>
                                                    {{-- <input type="number" class="form-control w-100 @error('unit.'.$loop->iteration.'.min_unit') is-invalid @enderror"
                                                        name="{{ 'unit['.$loop->iteration.'][min_unit]' }}" value="{{ $unitPrice->min_unit }}"> --}}

                                                    <div>
                                                        {{ $unitPrice->min_unit }}
                                                    </div>
                                                </td>
                                                <td>
                                                    {{-- <input type="number" class="form-control w-100 @error('unit.'.$loop->iteration.'.max_unit') is-invalid @enderror"
                                                        name="{{ 'unit['.$loop->iteration.'][max_unit]' }}" value="{{ $unitPrice->max_unit }}"> --}}

                                                    <div>
                                                        {{ $unitPrice->max_unit }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control w-100 @error('unit.'.$loop->iteration.'.min_size_price') is-invalid @enderror"
                                                        name="{{ 'unit['.$loop->iteration.'][min_size_price]' }}" value="{{ $unitPrice->min_size_price }}">
                                                    @error('unit.'.$loop->iteration.'.min_size_price')
                                                        <span class="invalid-feedback" role="alert">
                                                            {{ $message }}
                                                        </span>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control w-100 @error('unit.'.$loop->iteration.'.max_size_price') is-invalid @enderror"
                                                        name="{{ 'unit['.$loop->iteration.'][max_size_price]' }}" value="{{ $unitPrice->max_size_price }}">
                                                    @error('unit.'.$loop->iteration.'.max_size_price')
                                                        <span class="invalid-feedback" role="alert">
                                                            {{ $message }}
                                                        </span>
                                                    @enderror
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    <div class="search-form-group">
                                        <input class="btn btn-block btn-success" type="submit" value="{{ __('Save Price') }}">
                                    </div>

                                    <br>
                                    <div>
                                        <b>Note:</b> Oversize Unit if size >= 12 Inch or weight >= 2 Lbs
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center justify-content-md-end amt-16">
                                    {{ $unitPrices->appends(request()->all())->links('components.pagination') }}
                                </div>
                            @elseif ($mRequestType['name'] == "repack")
                                <input type="hidden" name="id" value="{{ $unitPrices->id }}" />
                                <div class="form-group search-form-group">
                                    <label class="col-form-label search-label"><b>{{ __('Price per Unit') }}</b></label>
                                    <div class="search-input position-relative">
                                        <input type="text" class="form-control w-100 @error('min_size_price') is-invalid @enderror"
                                            name="min_size_price" value="{{ $unitPrices['min_size_price'] ?? 0 }}"/>
                                        @error('min_size_price')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group search-form-group">
                                    <label class="col-form-label search-label"><b>{{ __('Price per Package') }}</b></label>
                                    <div class="search-input position-relative">
                                        <input type="text" class="form-control w-100 @error('max_size_price') is-invalid @enderror"
                                            name="max_size_price" value="{{ $unitPrices['max_size_price'] ?? 0 }}"/>
                                        @error('max_size_price')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="search-form-group">
                                    <div class="search-label d-none d-sm-block"></div>
                                    <div class="search-input text-center text-sm-left">
                                        <input class="btn btn-primary" type="submit" value="{{ __('Save Price') }}">
                                    </div>
                                </div>
                            @elseif ($mRequestType['name'] == "warehouse labor")
                                <input type="hidden" name="id" value="{{ $unitPrices->id }}" />
                                <div class="form-group search-form-group">
                                    <label class="col-form-label search-label"><b>{{ __('Price per Hour') }}</b></label>
                                    <div class="search-input position-relative">
                                        <input type="text" class="form-control w-100 @error('min_size_price') is-invalid @enderror"
                                            name="min_size_price" value="{{ $unitPrices['min_size_price'] ?? 0 }}"/>
                                        @error('min_size_price')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group search-form-group">
                                    <label class="col-form-label search-label"><b>{{ __('Price per Unit') }}</b></label>
                                    <div class="search-input position-relative">
                                        <input type="text" class="form-control w-100 @error('max_size_price') is-invalid @enderror"
                                            name="max_size_price" value="{{ $unitPrices['max_size_price'] ?? 0 }}"/>
                                        @error('max_size_price')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="search-form-group">
                                    <div class="search-label d-none d-sm-block"></div>
                                    <div class="search-input text-center text-sm-left">
                                        <input class="btn btn-primary" type="submit" value="{{ __('Save Price') }}">
                                    </div>
                                </div>
                            @else
                                <input type="hidden" name="id" value="{{ $unitPrices->id }}" />
                                <div class="form-group search-form-group">
                                    <label class="col-form-label search-label">
                                        <b>
                                            @if (in_array($mRequestType['name'], ['add package', 'outbound']))
                                                {{ __('Price per Package') }}
                                            @else
                                                {{ __('Price per Unit') }}
                                            @endif
                                        </b>
                                    </label>
                                    <div class="search-input position-relative">
                                        <input type="text" class="form-control w-100 @error('min_size_price') is-invalid @enderror"
                                            name="min_size_price" value="{{ $unitPrices['min_size_price'] ?? 0 }}"/>
                                        @error('min_size_price')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="search-form-group">
                                    <div class="search-label d-none d-sm-block"></div>
                                    <div class="search-input text-center text-sm-left">
                                        <input class="btn btn-primary" type="submit" value="{{ __('Save Price') }}">
                                    </div>
                                </div>
                            @endif
                        @elseif ($id == 0)
                            <input type="hidden" name="type" value="storage" />
                            {{-- Storage --}}
                            <div class="table-responsive">
                                <table class="table table-align-middle table-bordered table-striped table-sm" id="admin-request-detail-table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>{{ __('Min Month') }}</th>
                                            <th>{{ __('Price per Cuft') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($unitPrices as $unitPrice)
                                        <tr>
                                            <input type="hidden" name="{{ 'unit['.$loop->iteration.'][id]' }}" value="{{ $unitPrice->id }}" />
                                            <td>{{ ($unitPrices->currentPage() - 1) * $unitPrices->perPage() + $loop->iteration }}</td>
                                            <td>
                                                {{-- <input type="number" class="form-control w-100 @error('unit.'.$loop->iteration.'.month') is-invalid @enderror"
                                                    name="{{ 'unit['.$loop->iteration.'][month]' }}" value="{{ $unitPrice->month }}"> --}}
                                                <div>
                                                    {{ $unitPrice->month }}
                                                </div>

                                                {{-- @error('unit.'.$loop->iteration.'.month')
                                                    <span class="invalid-feedback" role="alert">
                                                        {{ $message }}
                                                    </span>
                                                @enderror --}}
                                            </td>
                                            <td>
                                                <input type="text" class="form-control w-100 @error('unit.'.$loop->iteration.'.price') is-invalid @enderror" name="{{ 'unit['.$loop->iteration.'][price]' }}" value="{{ $unitPrice->price }}">
                                                @error('unit.'.$loop->iteration.'.price')
                                                    <span class="invalid-feedback" role="alert">
                                                        {{ $message }}
                                                    </span>
                                                @enderror
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <div class="search-form-group">
                                    <input class="btn btn-block btn-success" type="submit" value="{{ __('Save Price') }}">
                                </div>
                            </div>
                            <div class="d-flex justify-content-center justify-content-md-end amt-16">
                                {{ $unitPrices->appends(request()->all())->links('components.pagination') }}
                            </div>
                        @elseif ($id == App\Models\MTax::TAX_ID)
                            <input type="hidden" name="type" value="tax" />
                            <div class="form-group search-form-group">
                                <label class="col-form-label search-label">
                                    <b>
                                        {{ __('Tax') }}
                                    </b>
                                </label>
                                <div class="search-input position-relative">
                                    <input type="text" class="form-control w-100 @error('tax') is-invalid @enderror"
                                        name="tax" value="{{ $unitPrices[0] }}"/>
                                    @error('tax')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="search-form-group">
                                <div class="search-label d-none d-sm-block"></div>
                                <div class="search-input text-center text-sm-left">
                                    <input class="btn btn-primary" type="submit" value="{{ __('Save Tax') }}">
                                </div>
                            </div>
                        @endif
                </form>
            @endif
        </div>
    </div>
</div>

<!-- Modal -->
<div id="modal-add-new" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create new price</h5>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.unit-price.create') }}" method="POST" enctype="multipart/form-data">
                @csrf
                    <input type="hidden" name="type" value="{{ $mRequestType['id'] ?? 0 }}" />
                    @if(isset($mRequestType['id']))
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Length') }}</b></label>
                            <div class="search-input position-relative">
                                <input type="text" class="form-control w-100" name="length" />
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Weight') }}</b></label>
                            <div class="search-input position-relative">
                                <input type="text" class="form-control w-100" name="weight" />
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Min') }}</b></label>
                            <div class="search-input position-relative">
                                <input type="number" class="form-control w-100" name="min_unit" />
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Max') }}</b></label>
                            <div class="search-input position-relative">
                                <input type="number" class="form-control w-100" name="max_unit" />
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Hour') }}</b></label>
                            <div class="search-input position-relative">
                                <input type="number" class="form-control w-100" name="hour" />
                            </div>
                        </div>
                    @else
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Month') }}</b></label>
                            <div class="search-input position-relative">
                                <input type="number" class="form-control w-100" name="month" />
                            </div>
                        </div>
                    @endif

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label"><b>{{ __('Price') }}</b></label>
                        <div class="search-input position-relative">
                            <input type="text" class="form-control w-100" name="price" />
                        </div>
                    </div>

                    <div class="search-form-group">
                        <div class="search-label d-none d-sm-block"></div>
                        <div class="search-input text-center text-sm-left">
                            <input class="btn btn-primary" type="submit" value="{{ __('Add') }}">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
