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
            <form action="{{ route('staff.priceTable.store') }}" method="POST">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h2 class="mb-0 px-30">{{ __('Create Price Table') }}</h2>
                    </div>
                    <div class="card-body">
                        <div class="px-30">
                            <div class="form-group search-form-group">
                                <label for="email" class="col-form-label search-label">{{ __('Price table name *') }} </label>
                                <div class="search-input">
                                    <input id="name" type="text" class="form-control w-100" name="name" value="{{ old('name') }}">
                                    @error('name')
                                    <span class="text-danger mt-1">
                                        {{ $message }}
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            {{--<div class="form-group search-form-group">
                                <label for="password_confirmation" class="col-form-label search-label">{{ __('Price table status *') }}</label>
                                <div class="search-input">
                                    <select id="status" name="status" class="form-control w-100">
                                        <option value="">Select</option>
                                        @foreach (\App\Models\PriceList::$price_table_status as $k => $v)
                                                <option value="{{ $k }}"
                                                        {{ old('status') !== null && old('status') == $k ? 'selected' : '' }}
                                                >{{ ucfirst($v) }}</option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                    <span class="text-danger mt-1">
                                        {{ $message }}
                                    </span>
                                    @enderror
                                </div>
                            </div>--}}

                            <div class="search-form-group">
                                <div class="search-label d-none d-sm-block"></div>
                                <div class="search-input text-center text-sm-left">
                                    <input class="btn btn-round btn-primary" type="submit" value="{{ __('Create Table') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>

                    <div class="card-header">
                        <h2 class="mb-0 px-30">{{ __('Price Table List') }}</h2>
                    </div>
                    <div class="card-footer">
                        @if (count($tables) == 0)
                            <div class="text-center">{{ __('No data.') }}</div>
                        @else
                            <div class="table-responsive">
                                <table class="table" id="admin-user-list-table">
                                    <thead>
                                    <tr class="text-primary">
                                        <th>#</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Table Id') }}</th>
                                        <th>{{ __('Created At') }}</th>
                                        <th>{{ __('User Create') }}</th>
                                        {{--<th>{{ __('Status') }}</th>--}}
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $i = $tables->currentPage() !== null ? ($tables->currentPage() - 1) * $tables->perPage() + 1 : 1;
                                    @endphp
                                    @foreach($tables as $table)
                                        <tr id="table-id-{{ $table->id }}">
                                            <td>{{ $i }}</td>
                                            <td>{{ ucfirst($table->name) }}</td>
                                            <td>{{ $table->id }}</td>
                                            <td>{{ $table->date_created }}</td>
                                            <td>{{ $users[$table->user_create] }}</td>
                                            {{--<td>{{ ucfirst(\App\Models\PriceList::$price_table_status[$table->status]) }}</td>--}}
                                            <td>
                                                <a class="btn btn-sm btn-round btn-primary" href="{{ route('staff.prices.list', ['id' => $table->id]) }}">Detail</a>
                                                {{--<button type="button" data-table-id="{{ $table->id }}" class="btn btn-sm btn-round btn-warning delete-price-item">Delete</button>--}}
                                                {{--@if($table->status == 1)
                                                    <button type="button" data-table-id="{{ $table->id }}" data-to-status="2" class="btn btn-sm btn-round btn-info change-status-table">Inactive</button>
                                                @elseif($table->status == 2)
                                                    <button type="button" data-table-id="{{ $table->id }}" data-to-status="1" class="btn btn-sm btn-round btn-success change-status-table">Active</button>
                                                @endif--}}
                                            </td>
                                        </tr>

                                        @php
                                            $i++;
                                        @endphp
                                    @endforeach
                                    </tbody>
                                </table>
                                <div>
                                    {{ $tables->links() }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {


            {{--
            $('.delete-price-item').click(function () {
                let isConfirm = confirm('Do you want to delete this record?');
                if (isConfirm) {
                    let tableId = $(this).data('table-id');

                    if (!!tableId) {
                        $.ajax({
                            url: '{{ route('staff.pricesTable.delete') }}',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                _token: '{{ csrf_token() }}',
                                table_id: tableId
                            },
                            success: function (res) {
                                console.log(res)
                                if (!!res.status && res.status === 'success') {
                                    document.querySelector('tr#table-id-' + tableId).outerHTML = '';
                                    // alert(res.message);
                                } else {
                                    alert(res.message);
                                }
                            },
                            error: function (err) {
                                console.log(err)
                            }
                        })
                    } else {
                        alert('lhópgjpọepfjspfj');
                    }
                }
            })
            --}}


            $('.change-status-table').click(function () {
                let isConfirm = confirm('Do you want to change status this table?');
                if (isConfirm) {
                    let tableId = $(this).data('table-id');
                    let status = $(this).data('to-status');

                    if (!!tableId && !!status) {
                        $.ajax({
                            url: '{{ route('staff.pricesTable.changeStatus') }}',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                _token: '{{ csrf_token() }}',
                                table_id: tableId,
                                status: status
                            },
                            success: function (res) {
                                console.log(res)
                                if (!!res.status && res.status === 'success') {
                                    // alert(res.message);
                                    location.reload();
                                } else {
                                    alert(res.message);
                                }
                            },
                            error: function (err) {
                                console.log(err)
                            }
                        })
                    } else {
                        alert('sađìôlsfl;pgpsfspf');
                    }
                }
            })
        })
    </script>
@endpush
