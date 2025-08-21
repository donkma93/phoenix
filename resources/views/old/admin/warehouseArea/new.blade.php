@extends('layouts.admin')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('admin.dashboard')
        ],
        [
            'text' => 'Warehouse Area',
            'url' => route('admin.warehouseArea.list')
        ],
        [
            'text' => 'Create warehouse area'
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
        <form action="{{ route('admin.warehouseArea.create') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">{{ __('Create Warehouse Area') }}</h2>
                </div>
                <div class="card-body">
                    <div class="form-group search-form-group">
                        <label for="name" class="col-form-label search-label"><b>{{ __('Name') }}</b></label>
                        <div class="search-input">
                            <input type="text" class="form-control w-100 @error('name') is-invalid @enderror" name="name">
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label for="warehouse" class="col-form-label search-label"><b>{{ __('Warehouse') }}</b></label>
                        <div class="search-input position-relative">
                            <input type="input" class="form-control w-100 @error('warehouse') is-invalid @enderror" id="warehouse-input" name="warehouse" list="dropdown-warehouse" autocomplete="off" />
                            @error('warehouse')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label for="warehouse" class="col-form-label search-label"><b>{{ __('Barcode') }}</b></label>
                        <div class="search-input position-relative">
                            <input type="input" class="form-control w-100 @error('barcode') is-invalid @enderror" name="barcode" value="{{ $barcode }}" autocomplete="off"  />
                            @error('barcode')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="search-form-group">
                        <div class="search-label d-none d-sm-block"></div>
                        <div class="search-input text-center text-sm-left">
                            <input class="btn btn-primary" type="submit" value="{{ __('Create Area') }}">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
<script>
    let warehouses = @php echo json_encode($warehouses['warehouses']) @endphp;
    filterInput(document.getElementById("warehouse-input"), warehouses, 'dropdown-warehouse');
</script>
@endsection
