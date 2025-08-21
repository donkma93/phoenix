@extends('layouts.user')

@section('breadcrumb')
    @include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('dashboard')
        ],
        [
            'text' => 'Package Group'
        ],
        [
            'text' => 'Create'
        ]
    ]
])
@endsection

@section('content')
    <div class="fade-in">
        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">{{ __('Create New Package Group') }}</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('package_groups.store') }}" class="form-horizontal" role="form">
                    @csrf

                    <div class="form-group search-form-group">
                        <label for="name" class="col-form-label search-label"><b>{{ __('Name') }}</b></label>
                        <div class="search-input">
                            <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}">
                            @if ($errors->has('name'))
                                <p class="text-danger mb-0">
                                    {{ $errors->first('name') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label for="width" class="search-label col-form-label"><b>{{ __('Width') }}</b></label>
                        <div class="search-input">
                            <input id="width" type="text" class="form-control" name="width" value="{{ old('width') }}">
                            @if ($errors->has('width'))
                                <p class="text-danger mb-0">
                                    {{ $errors->first('width') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label for="height" class="search-label col-form-label"><b>{{ __('Height') }}</b></label>
                        <div class="search-input">
                            <input id="height" type="text" class="form-control" name="height" value="{{ old('height') }}">
                            @if ($errors->has('height'))
                                <p class="text-danger mb-0">
                                    {{ $errors->first('height') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label for="length" class="search-label col-form-label"><b>{{ __('Length') }}</b></label>
                        <div class="search-input">
                            <input id="length" type="text" class="form-control" name="length" value="{{ old('length') }}">
                            @if ($errors->has('length'))
                                <p class="text-danger mb-0">
                                    {{ $errors->first('length') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label for="barcode" class="search-label col-form-label"><b>{{ __('Barcode') }}</b></label>
                        <div class="search-input">
                            <input id="barcode" type="text" class="form-control" name="barcode" value="{{ old('barcode') }}">
                            @if ($errors->has('length'))
                                <p class="text-danger mb-0">
                                    {{ $errors->first('barcode') }}
                                </p>
                            @endif
                        </div>

                        {{-- <label for="image" class="search-label col-form-label"><b>{{ __('Barcode') }}</b></label>
                        <div class="search-input">
                            <input type="file" accept="image/*" hidden id="fileUpload"
                                class="btn-primary form-control" onchange="loadFile(event)">
                            <div class="btn btn-info w-100" onclick="uploadImage()"> Upload image </div>
                            <div id="span-error" class="text-danger mb-0"></div>
                        </div> --}}
                    </div>

                    {{-- <div class="form-group search-form-group">
                        <label class="search-label col-form-label"><b>{{ __('Barcode from Image') }}</b></label>
                        <div class="search-input">
                            <div class="form-control" id="barcode_display"></div>
                            @if ($errors->has('barcode'))
                                <p class="text-danger mb-0">
                                    {{ $errors->first('barcode') }}
                                </p>
                            @endif
                            <input type="hidden" id="barcode" name="barcode">
                        </div>
                    </div> --}}

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
@endsection

@section('scripts')
<script>
    function uploadImage() {
        $("#fileUpload").click();
    }

    function loadFile(event) {
        let Quagga = window.quagga;

        Quagga.decodeSingle({
            src: URL.createObjectURL(event.target.files[0]),
             decoder: {
                readers: [
                    { format: 'code_128_reader', config: {} },
                    { format: 'ean_reader', config: {} },
                    { format: 'ean_8_reader', config: {} },
                    { format: 'codabar_reader', config: {} },
                    { format: 'i2of5_reader', config: {} },
                    { format: '2of5_reader', config: {} },
                    { format: 'code_93_reader', config: {} },
                    { format: 'code_39_reader', config: {} }
                ]
            },
            inputStream: {
                size: 1280
            },
            locate: true,
        }, function(result) {
            if(result && result.codeResult) {
                $('#barcode_display').text(result.codeResult.code);
                $('#barcode').val(result.codeResult.code);
                $('#span-error').text("");
            } else {
                $('#span-error').text(" (*)Please upload other image");
                $('#barcode_display').text('');
                $('#barcode').val('');
            }
        });
    }
</script>

@endsection
