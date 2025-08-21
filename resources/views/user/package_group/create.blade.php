@extends('layouts.app', [
'class' => '',
'folderActive' => '',
'elementActive' => 'package_group',
])

@section('styles')
<style>
    .table-responsive {
        overflow: unset;
    }
    .min-w-160 {
        min-width: 160px;
    }
    .card .card-footer {
        border-top: 1px solid #d8dbe0 !important;
    }
    .card .card-header {
        border-bottom: 1px solid #d8dbe0 !important;
    }
    .form-group {
        margin-bottom: 1rem;
    }
    .search-form-group {
        display: flex;
        flex-direction: column;
    }
    .search-form-group .search-input {
        flex: 1;
    }

    @media (min-width: 576px) {
        .search-form-group {
            flex-direction: row;
        }
        .search-form-group .search-input {
            max-width: 360px;
        }
    }
</style>
@endsection

@section('content')
<div class="content">
    <div class="fade-in">
        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">{{ __('Create New Package Group') }}</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('package_groups.store') }}" class="form-horizontal" role="form">
                    @csrf

                    <div class="form-group search-form-group">
                        <label for="name" class="col-form-label min-w-160 text-left search-label"><b>{{ __('Name') }}</b></label>
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
                        <label for="unit_width" class="search-label col-form-label min-w-160 text-left"><b>{{ __('Width') }}</b></label>
                        <div class="search-input">
                            <input id="unit_width" type="text" class="form-control" name="unit_width" value="{{ old('unit_width') }}">
                            @if ($errors->has('unit_width'))
                                <p class="text-danger mb-0">
                                    {{ $errors->first('unit_width') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label for="unit_height" class="search-label col-form-label min-w-160 text-left"><b>{{ __('Height') }}</b></label>
                        <div class="search-input">
                            <input id="unit_height" type="text" class="form-control" name="unit_height" value="{{ old('unit_height') }}">
                            @if ($errors->has('unit_height'))
                                <p class="text-danger mb-0">
                                    {{ $errors->first('unit_height') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label for="unit_length" class="search-label col-form-label min-w-160 text-left"><b>{{ __('Length') }}</b></label>
                        <div class="search-input">
                            <input id="unit_length" type="text" class="form-control" name="unit_length" value="{{ old('unit_length') }}">
                            @if ($errors->has('unit_length'))
                                <p class="text-danger mb-0">
                                    {{ $errors->first('unit_length') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label for="barcode" class="search-label col-form-label min-w-160 text-left"><b>{{ __('Barcode') }}</b></label>
                        <div class="search-input">
                            <input id="barcode" type="text" class="form-control" name="barcode" value="{{ old('barcode') }}">
                            @if ($errors->has('barcode'))
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
                        <div class="search-label d-none d-sm-block min-w-160"></div>
                        <div class="search-input text-center text-sm-left min">
                            <input class="btn btn-primary" type="submit" value="{{ __('Create') }}">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
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

@endpush
