@extends('layouts.app',[
'class' => '',
'folderActive' => 'order_management',
'elementActive' => 'import_label'
])

@section('styles')
    <style>
        .card .card-header {
            padding: 0.75rem 1.25rem;
            margin-bottom: 0;
            border-bottom: 1px solid;
            background-color: #fff;
            border-color: #d8dbe0;
        }

        .search-form-group {
            display: flex;
            align-items: center;
        }

        .search-form-group .search-label {
            min-width: 160px;
        }

        .form-horizontal .col-form-label {
            padding-top: calc(.375rem + 1px);
            padding-bottom: calc(.375rem + 1px);
            padding-left: 0;
            padding-right: 0;
            text-align: left;
            margin-bottom: 0;
            font-size: inherit;
            line-height: 1.5;
        }

        .pointer {
            cursor: pointer !important;
        }

        i.pointer {
            padding: 8px;
        }

        .form-control {
            height: calc(1.5em + 1rem + 5px) !important;
            padding: 0.625rem 0.75rem !important;
        }

        /*CSS for select2*/
        .select2-container--default .select2-selection--single {
            height: calc(1.5em + 1rem + 5px) !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: unset !important;
            padding-top: 0.625rem !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: unset !important;
            top: 1.25rem !important;
        }
    </style>
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
    <div class="content">
        @if (session('success'))
            <div class="row justify-content-end">
                <div class="col-md-4">
                    <div class="alert alert-success alert-dismissible fade show">
                        <button type="button" aria-hidden="true" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="nc-icon nc-simple-remove"></i>
                        </button>
                        <span>
                            <b> Success - </b>
                            {{ session('success') }}
                        </span>
                    </div>
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="row justify-content-end">
                <div class="col-md-4">
                    <div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" aria-hidden="true" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="nc-icon nc-simple-remove"></i>
                        </button>
                        <span>
                            <b> Error - </b>
                            {{ session('error') }}
                        </span>
                    </div>
                </div>
            </div>
        @endif
        <div class="fade-in">
            <div class="card px-4 py-2">
                <div class="card-header">
                    <h2 class="mb-0">{{ __('Import create labels') }}</h2>
                    <a download href="{{ asset('templates/pnx-import-labels.xlsx') }}">Download import template</a>
                </div>

                {{-- Via G7 --}}
                <div class="card-body">
                    <div>
                        <form method="POST" action="{{ route('staff.labels.import.excel.g7') }}" enctype="multipart/form-data"
                              class="prevent-double-click">
                            @csrf

                            <div>
                                <div class="d-flex justify-content-between align-items-center amb-12 apb-4">
                                    <h4 class="amb-4 mt-0">{{ __('Buy labels via G7') }}</h4>
                                </div>

                                <div class="form-group search-form-group mt-3">
                                    <label for="image" class="search-label col-form-label">
                                        <b>{{ __('File import') }}</b>
                                    </label>
                                    <div class="search-input">
                                        <input type="file"
                                               accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                               hidden id="label_file_g7" name="label_file"
                                               class="btn-primary form-control">
                                        <span id="label_file_name_g7">No file selected</span>
                                        <div class="btn w-100" onclick="uploadFileG7()"> Upload File</div>
                                        @if ($errors->has('label_file_g7'))
                                            <p class="text-danger mb-0">
                                                {{ $errors->first('label_file_g7') }}
                                            </p>
                                        @endif

                                        @if (session('csvErrorsG7') !== null)
                                            @foreach (session('csvErrorsG7') as $index => $error)
                                                @php
                                                    $line = $index + 2;
                                                @endphp
                                                <p class="text-danger mb-0">
                                                    {{ "Line {$line}: {$error}" }}
                                                </p>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group search-form-group">
                                    <label for="image" class="search-label col-form-label">
                                        <b>{{ __('') }}</b>
                                    </label>
                                    <div class="form-group mb-0">
                                        <button type="submit" class="btn btn-info w-100">
                                            {{ __('Create Labels') }}
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

                <hr class="my-0">

                {{-- Via Shippo --}}
                <div class="card-body">
                    <div>
                        <form method="POST" action="{{ route('staff.labels.import.excel.shippo') }}" enctype="multipart/form-data"
                              class="prevent-double-click">
                            @csrf

                            <div>
                                <div class="d-flex justify-content-between align-items-center amb-12 apb-4">
                                    <h4 class="amb-4 mt-0">{{ __('Buy labels via Shippo') }}</h4>
                                </div>

                                <div class="form-group search-form-group mt-3">
                                    <label for="image" class="search-label col-form-label">
                                        <b>{{ __('File import') }}</b>
                                    </label>
                                    <div class="search-input">
                                        <input type="file"
                                               accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                               hidden id="label_file_shippo" name="label_file"
                                               class="btn-primary form-control">
                                        <span id="label_file_name_shippo">No file selected</span>
                                        <div class="btn w-100" onclick="uploadFileShippo()"> Upload File</div>
                                        @if ($errors->has('label_file_shippo'))
                                            <p class="text-danger mb-0">
                                                {{ $errors->first('label_file_shippo') }}
                                            </p>
                                        @endif

                                        @if (session('csvErrorsShippo') !== null)
                                            @foreach (session('csvErrorsShippo') as $index => $error)
                                                @php
                                                    $line = $index + 2;
                                                @endphp
                                                <p class="text-danger mb-0">
                                                    {{ "Line {$line}: {$error}" }}
                                                </p>
                                            @endforeach
                                        @endif

                                        @if (session('errorsForeachShippo') !== null)
                                            @foreach (session('errorsForeachShippo') as $index => $error)
                                                @php
                                                    $line = $index + 2;
                                                    $error = json_encode($error);
                                                @endphp
                                                <p class="text-danger mb-0">
                                                    {{ "Line {$line}: {$error}" }}
                                                </p>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group search-form-group">
                                    <label for="image" class="search-label col-form-label">
                                        <b>{{ __('') }}</b>
                                    </label>
                                    <div class="form-group mb-0">
                                        <button type="submit" class="btn btn-info w-100">
                                            {{ __('Create Labels') }}
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

                <hr class="my-0">

                {{-- Via Myib --}}
                <div class="card-body">
                    <div>
                        <form method="POST" action="{{ route('staff.labels.import.excel.myib') }}" enctype="multipart/form-data"
                              class="prevent-double-click">
                            @csrf

                            <div>
                                <div class="d-flex justify-content-between align-items-center amb-12 apb-4">
                                    <h4 class="amb-4 mt-0">{{ __('Buy labels via Myib') }}</h4>
                                </div>

                                <div class="form-group search-form-group mt-3">
                                    <label for="image" class="search-label col-form-label">
                                        <b>{{ __('File import') }}</b>
                                    </label>
                                    <div class="search-input">
                                        <input type="file"
                                               accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                               hidden id="label_file_myib" name="label_file"
                                               class="btn-primary form-control">
                                        <span id="label_file_name_myib">No file selected</span>
                                        <div class="btn w-100" onclick="uploadFileMyib()"> Upload File</div>
                                        @if ($errors->has('label_file_myib'))
                                            <p class="text-danger mb-0">
                                                {{ $errors->first('label_file_myib') }}
                                            </p>
                                        @endif

                                        @if (session('csvErrorsMyib') !== null)
                                            @foreach (session('csvErrorsMyib') as $index => $error)
                                                @php
                                                    $line = $index + 2;
                                                @endphp
                                                <p class="text-danger mb-0">
                                                    {{ "Line {$line}: {$error}" }}
                                                </p>
                                            @endforeach
                                        @endif

                                        @if (session('errorsForeachMyib') !== null)
                                            @foreach (session('errorsForeachMyib') as $index => $error)
                                                @php
                                                    $line = $index + 2;
                                                    $error = json_encode($error);
                                                @endphp
                                                <p class="text-danger mb-0">
                                                    {{ "Line {$line}: {$error}" }}
                                                </p>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group search-form-group">
                                    <label for="image" class="search-label col-form-label">
                                        <b>{{ __('') }}</b>
                                    </label>
                                    <div class="form-group mb-0">
                                        <button type="submit" class="btn btn-info w-100">
                                            {{ __('Create Labels') }}
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            //
            $('#label_file_shippo').change(function () {
                try {
                    $('#label_file_name_shippo').text($('#label_file_shippo')[0].files[0].name);
                } catch (error) {
                    $('#label_file_name_shippo').text("No file selected");
                }
            });

            $('#label_file_g7').change(function () {
                try {
                    $('#label_file_name_g7').text($('#label_file_g7')[0].files[0].name);
                } catch (error) {
                    $('#label_file_name_g7').text("No file selected");
                }
            });

            $('#label_file_myib').change(function () {
                try {
                    $('#label_file_name_myib').text($('#label_file_myib')[0].files[0].name);
                } catch (error) {
                    $('#label_file_name_myib').text("No file selected");
                }
            });
        });

        function uploadFileG7() {
            $('#label_file_g7').click();
        }

        function uploadFileShippo() {
            $('#label_file_shippo').click();
        }

        function uploadFileMyib() {
            $('#label_file_myib').click();
        }
    </script>
@endpush
