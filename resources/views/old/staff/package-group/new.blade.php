@extends('layouts.staff')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('staff.dashboard')
        ],
        [
            'text' => 'Package Group',
            'url' => route('staff.package-group.list')
        ],
        [
            'text' => 'Create package group',
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

    <form action="{{ route('staff.package-group.create') }}" method="POST" enctype="multipart/form-data">
    @csrf
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h2 class="mb-0">{{ __('Create Package Group') }}</h2>
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
                    <label class="col-form-label search-label"><b>{{ __('Barcode') }}</b></label>
                    <div class="search-input">
                        <input id="name" type="text" class="form-control w-100 @error('barcode') is-invalid @enderror" name="barcode">
                        @error('barcode')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('File') }}</b></label>
                    <div class="search-input">
                    <input id="file" hidden type="file" class="img-picker @error('file') is-invalid @enderror" name="file">
                        <div class="btn btn-info" onclick="upload()">Upload file</div>
                        @error('file')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Unit weight') }}</b></label>
                    <div class="search-input">
                        <input id="unit_weight" type="text" class="form-control w-100 @error('unit_weight') is-invalid @enderror" name="unit_weight">
                        @error('unit_weight')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Unit width') }}</b></label>
                    <div class="search-input">
                        <input id="unit_width" type="text" class="form-control w-100 @error('unit_width') is-invalid @enderror" name="unit_width">
                        @error('unit_width')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Unit height') }}</b></label>
                    <div class="search-input">
                        <input id="unit_height" type="text" class="form-control w-100 @error('unit_height') is-invalid @enderror" name="unit_height">
                        @error('unit_height')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Unit length') }}</b></label>
                    <div class="search-input">
                        <input id="unit_length" type="text" class="form-control w-100 @error('unit_length') is-invalid @enderror" name="unit_length">
                        @error('unit_length')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Image') }}</b></label>
                    <div class="search-input">
                        <input id="image" hidden type="file" accept="image/*" class="img-picker @error('image') is-invalid @enderror" name="image" onchange="readURL(this);">
                        <div class="btn btn-info" onclick="uploadImage()">Upload image</div>
                        @error('image')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"></label>
                    <div class="search-input">
                        <img id="image-upload" class="d-none" src="#" alt="your image" height="200"/>
                    </div>
                </div>

                <div class="search-form-group">
                    <div class="search-label d-none d-sm-block"></div>
                    <div class="search-input text-center text-sm-left">
                        <input class="btn btn-primary" name="create-only" type="submit" value="{{ __('Create') }}">
                        <input class="btn btn-success" name="redirect" type="submit" value="{{ __('Create and Add package') }}">
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

    function upload() {
        $('#file').click()
    }

    function uploadImage() {
        $('#image').click()
    }

    function readURL(input) {
        if (input.files && input.files[0]) {
            let reader = new FileReader();

            reader.onload = function (e) {
                $('#image-upload').removeClass('d-none');
                $('#image-upload').attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
  </script>
@endsection
