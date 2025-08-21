@extends('layouts.app', [
    'class' => '',
    'folderActive' => '',
    'elementActive' => 'prices',
])

@section('styles')
    <style>
        .px-30 {
            padding-left: 30px !important;
            padding-right: 30px !important;
        }

        .search-form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .col-form-label {
            padding-top: calc(0.375rem + 1px);
            padding-bottom: calc(0.375rem + 1px);
            margin-bottom: 0;
            font-size: inherit;
            line-height: 1.5;
        }

        .search-form-group .search-label {
            min-width: 160px;
            text-align: left;
        }

        .search-form-group .search-input {
            flex: 1;
        }

        .form-check .form-check-label {
            padding-left: 0;
        }

        .pagination {
            justify-content: center;
        }

        tr:hover {
            background-color: #d3d3d3;
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
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center px-30">
                        <h2 class="mb-0">{{ __('Price detail') }}</h2>
                        <div>
                            {{--<a class="btn btn-round btn-info" href="{{ route('staff.prices.add') }}">
                                {{ __('Add price') }}
                            </a>--}}
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="px-30">
                        <form method="GET" action="{{ route('staff.prices.list', ['id' => $table_id]) }}" class="form-horizontal w-50" role="form">
                            <div class="row">
                                <div class="col-12 col-xl-6">
                                    <div class="form-group search-form-group">
                                        <label for="role" class="col-form-label search-label"><b>{{ __('Weight (gr)') }}</b></label>
                                        <div class="search-input">
                                            <select id="weight" name="weight" class="form-control w-100">
                                                <option value="">All</option>
                                                @foreach ($weight_level as $v)
                                                    <option value="{{ $v->weight }}"
                                                        {{ (old('weight') !== null && old('weight') == $v->weight) ? 'selected' : '' }}
                                                    ><= {{ $v->weight }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-xl-6">
                                    <div class="form-group search-form-group">
                                        <label for="role" class="col-form-label search-label"><b>{{ __('Destination') }}</b></label>
                                        <div class="search-input">
                                            <select id="destination" name="destination" class="form-control w-100">
                                                <option value="">All</option>
                                                @foreach ($destination_list as $v)
                                                        <option value="{{ $v->destination }}"
                                                            {{ (old('destination') !== null && old('destination') == $v->destination) ? 'selected' : '' }}
                                                        >{{ strtoupper($v->destination) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="search-form-group">
                                <div class="search-label d-none d-sm-block"></div>
                                <div class="search-input text-center text-sm-left">
                                    <input class="btn btn-round btn-primary" type="submit" value="{{ __('Search') }}">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card-footer">
                    @if (count($prices) == 0)
                        <div class="text-center">{{ __('No data.') }}</div>
                        @if($show_import_excel === true)
                            <hr>
                            <div class="row">
                                <div class="col-12 col-lg-6">
                                    <form method="POST" action="{{ route('staff.importPricesExcel') }}"  enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="table_id" value="{{ $table_id }}" />

                                        <div>
                                            <div class="form-group search-form-group">
                                                <label for="image" class="search-label col-form-label min-w-160">
                                                    <b>{{ __('') }}</b>
                                                </label>
                                                <div class="search-input">
                                                    <input type="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" hidden id="prices_file" name="prices_file"
                                                           class="btn-primary form-control">
                                                    <span id="prices_file_name">No file selected</span>
                                                    <div class="btn btn-info btn-round w-100" onclick="uploadOrderFile()">Upload File</div>

                                                    @if ($errors->has('prices_file'))
                                                        <p class="text-danger mb-0">
                                                            {{ $errors->first('prices_file') }}
                                                        </p>
                                                    @endif

                                                    @if (session('csvErrors') !== null)
                                                        @foreach (session('csvErrors') as $index => $error)
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
                                                <label for="image" class="search-label col-form-label min-w-160">
                                                    <b>{{ __('') }}</b>
                                                </label>
                                                <div class="form-group mb-0">
                                                    <button type="submit" class="btn btn-primary btn-round w-100">
                                                        {{ __('Create Table Via Excel') }}
                                                    </button>
                                                </div>
                                            </div>

                                        </div>
                                    </form>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <a download id="" class="btn btn-primary btn-round" href="{{ asset('templates/import-prices.xlsx') }}">Download template import</a>
                                </div>

                            </div>
                        @endif


                    @else
                        <div class="table-responsive">
                            <div class="d-flex justify-content-between align-items-center">
                                <h2 class="mb-0 mt-0">{{ ucfirst($table_info->name) }}</h2>
                                <h4 class="mb-0 mt-0">Status: {{ ucfirst(\App\Models\PriceList::$price_table_status[$table_info->status]) }}</h4>
                            </div>
                            <table class="table" id="admin-user-list-table">
                                <thead>
                                <tr class="text-primary">
                                    <th>#</th>
                                    {{--<th>{{ __('Price Type') }}</th>--}}
                                    <th>{{ __('Weight (gr)') }}</th>
                                    <th>{{ __('Price (VND)') }}</th>
                                    <th>{{ __('Destination') }}</th>
                                    {{--<th>{{ __('Status') }}</th>--}}
                                    {{--<th>{{ __('Action') }}</th>--}}
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $i = $prices->currentPage() !== null ? ($prices->currentPage() - 1) * $prices->perPage() + 1 : 1;
                                @endphp
                                @foreach($prices as $price)
                                    <tr id="price-id-{{ $price->id }}">
                                        <td>{{ $i }}</td>
                                        {{--<td>{{ ucfirst($price->name) }}</td>--}}
                                        <td>{{ number_format($price->weight) }}</td>
                                        <td>{{ number_format($price->price) }}</td>
                                        <td>{{ strtoupper($price->destination) }}</td>
                                        {{--<td>{{ ucfirst(\App\Models\PriceList::$price_table_status[$price->status]) }}</td>--}}
                                        {{--<td>
                                            <button type="button" data-price-id="{{ $price->id }}" class="btn btn-sm btn-round btn-warning delete-price-item">Delete</button>
                                        </td>--}}
                                    </tr>

                                    @php
                                        $i++;
                                    @endphp
                                @endforeach
                                </tbody>
                            </table>
                            <div class="text-center">
                                {{ $prices->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>

        function uploadOrderFile() {
            $('#prices_file').click();
        }

        $(document).ready(function () {

            $('#prices_file').change(function() {
                try {
                    $('#prices_file_name').text($('#prices_file')[0].files[0].name);
                } catch (error) {
                    $('#prices_file_name').text("No file selected");
                }
            });


            {{--
            $('.delete-price-item').click(function () {
                let isConfirm = confirm('Do you want to delete this record?');
                if (isConfirm) {
                    let priceId = $(this).data('price-id');

                    $.ajax({
                        url: '{{ route('staff.prices.delete') }}',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            _token: '{{ csrf_token() }}',
                            price_id: priceId
                        },
                        success: function (res) {
                            console.log(res)
                            if (!!res.status && res.status === 'success') {
                                document.querySelector('tr#price-id-' + priceId).outerHTML = '';
                                // alert(res.message);
                            } else {
                                alert(res.message);
                            }
                        },
                        error: function (err) {
                            console.log(err)
                        }
                    })
                }
            })
            --}}


        })
    </script>
@endpush
