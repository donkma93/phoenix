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
            <form action="{{ route('staff.prices.store') }}" method="post">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h2 class="mb-0 px-30">{{ __('Add Price') }}</h2>
                    </div>
                    <div class="card-body">
                        <div class="px-30">
                            <div class="form-group search-form-group">
                                <label for="name" class="col-form-label search-label">{{ __('Price table name *') }}</label>
                                <div class="search-input">
                                    <select id="name" name="name" class="form-control w-100">
                                        <option value="">Select</option>
                                        @foreach ($tables as $v)
                                            <option value="{{ $v->id }}"
                                                {{ old('name') !== null && old('name') == $v->id ? 'selected' : '' }}
                                            >{{ ucfirst($v->name) }}</option>
                                        @endforeach
                                    </select>
                                    @error('name')
                                    <span class="text-danger mt-1">
                                        {{ $message }}
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <hr>

                            <div class="row">
                                <div class="col-12 col-lg-4">
                                    <div class="form-group search-form-group flex-column">
                                        <label for="destination" class="col-form-label search-label pt-0">{{ __('Destination *') }} </label>
                                        <div class="search-input">
                                            <input id="destination" type="text" class="form-control w-100" name="destination" value="{{ old('destination') }}">
                                            @error('destination')
                                            <span class="text-danger mt-1">
                                        {{ $message }}
                                    </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <div class="form-group search-form-group flex-column">
                                        <label for="weight" class="col-form-label search-label pt-0">{{ __('Weight (gr) *') }} </label>
                                        <div class="search-input">
                                            <input id="weight" type="number" min="0" step="1" class="form-control w-100" name="weight" value="{{ old('weight') }}">
                                            @error('weight')
                                            <span class="text-danger mt-1">
                                        {{ $message }}
                                    </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <div class="form-group search-form-group flex-column">
                                        <label for="price" class="col-form-label search-label pt-0">{{ __('Price (VND) *') }} </label>
                                        <div class="search-input">
                                            <input id="price" type="number" min="0" step="1" class="form-control w-100" name="price" value="{{ old('price') }}">
                                            @error('price')
                                            <span class="text-danger mt-1">
                                        {{ $message }}
                                    </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="search-form-group">
                                {{--<div class="search-label d-none d-sm-block"></div>--}}
                                <div class="search-input text-center text-sm-left">
                                    <input class="btn btn-round btn-primary" type="submit" value="{{ __('Submit') }}">
                                </div>
                            </div>
                        </div>
                    </div>


                    {{--<div class="card-footer">
                        @if (count($tables) == 0)
                            <div class="text-center">{{ __('No data.') }}</div>
                        @else
                            <div class="table-responsive">
                                <table class="table" id="admin-user-list-table">
                                    <thead>
                                    <tr class="text-primary">
                                        <th>#</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Created At') }}</th>
                                        <th>{{ __('User Create') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $i = 1;
                                    @endphp
                                    @foreach($tables as $table)
                                        <tr>
                                            <td>{{ $i }}</td>
                                            <td>{{ ucfirst($table->name) }}</td>
                                            <td>{{ $table->date_created }}</td>
                                            <td>{{ $users[$table->user_create] }}</td>
                                            <td>{{ ucfirst(\App\Models\PriceList::$price_table_status[$table->status]) }}</td>
                                            <td>
                                                <a class="btn btn-sm btn-round btn-info" href="{{ route('staff.user.profile', ['id' => $table->id]) }}">Edit</a>
                                                <a class="btn btn-sm btn-round btn-warning" href="{{ route('staff.user.profile', ['id' => $table->id]) }}">Delete</a>
                                            </td>
                                        </tr>

                                        @php
                                            $i++;
                                        @endphp
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>--}}
                </div>
            </form>
        </div>
    </div>
@endsection
