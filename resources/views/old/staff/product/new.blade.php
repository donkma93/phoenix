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
            'text' => 'Create Product'
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

    <form action="{{ route('staff.product.create') }}" method="POST" enctype="multipart/form-data">
    @csrf
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h2 class="mb-0">{{ __('Create Product') }}</h2>
            </div>
            <div class="card-body">
                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('User') }}</b></label>
                    <div class="search-input position-relative">
                        <input id="email" type="text" class="form-control w-100 @error('email') is-invalid @enderror" list="dropdown-email" name="email" autocomplete="off">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Name') }}</b></label>
                    <div class="search-input">
                        <input id="name" type="text" class="form-control w-100 @error('name') is-invalid @enderror" name="name">
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Status') }}</b></label>
                    <div class="search-input">
                        <select name="status" class="form-control w-100 @error('status') is-invalid @enderror" name="status">
                            @foreach (App\Models\Product::$statusName as $key => $status)
                                <option value="{{ $key }}">{{ $status }}</option>
                            @endforeach
                        </select>
                        @error('status')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>
                
                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Fulfillment Fee') }}</b></label>
                    <div class="search-input">
                        <input id="name" type="text" class="form-control w-100 @error('fulfillment_fee') is-invalid @enderror" name="fulfillment_fee">
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
                        <input id="name" type="text" class="form-control w-100 @error('extra_pick_fee') is-invalid @enderror" name="extra_pick_fee">
                        @error('extra_pick_fee')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="search-form-group">
                    <div class="search-label d-none d-sm-block"></div>
                    <div class="search-input text-center text-sm-left">
                        <input class="btn btn-primary" type="submit" value="{{ __('Create') }}">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
  <script>
    let users = @php echo json_encode($users) @endphp;

    filterInput(document.getElementById("email"), users, 'dropdown-email'); 
  </script>
@endsection
