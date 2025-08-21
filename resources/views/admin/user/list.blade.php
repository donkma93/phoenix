@extends('layouts.app', [
    'class' => '',
    'folderActive' => '',
    'elementActive' => 'users',
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
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center px-30">
                    <h2 class="mb-0">{{ __('User list') }}</h2>
                    <a class="btn btn-round btn-success" href="{{ route('staff.user.new') }}">
                        {{ __('New User') }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="px-30">
                    <form method="GET" action="{{ route('staff.user.list') }}" class="form-horizontal" role="form">
                        <div class="form-group search-form-group">
                            <label for="role" class="col-form-label search-label"><b>{{ __('Role') }}</b></label>
                            <div class="search-input">
                                <select id="type" name="role" class="form-control w-100">
                                    <option selected></option>
                                    @foreach (App\Models\User::$roleName as $key => $role)
                                        @if($key != App\Models\User::ROLE_ADMIN)
                                            <option value="{{ $key }}"
                                                    @if (isset($oldInput['role']) && $oldInput['role'] == $key)
                                                        selected="selected"
                                                @endif
                                            >{{ ucfirst($role) }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label for="email" class="col-form-label search-label"><b>{{ __('Email') }}</b></label>
                            <div class="search-input">
                                <input type="input" id="email-input"  list="dropdown-email" class="form-control w-100" name="email" value="@if (isset($oldInput['email'])){{$oldInput['email']}}@endif" />
                            </div>
                        </div>
                        <div class="form-group search-form-group">
                            <label for="email-verify" class="col-form-label search-label"><b>{{ __('Email verify') }}</b></label>
                            <div class="search-input search-radio">
                                <div class="form-check form-check-inline amr-20">
                                    <input class="form-check-input" type="radio" name="isVerify" value="" id="all-verify"
                                           @if(!isset($oldInput['isVerify']))
                                               checked
                                        @endif
                                    >
                                    <label class="form-check-label" for="all-verify">All</label>
                                </div>
                                <div class="form-check form-check-inline amr-20">
                                    <input class="form-check-input" type="radio" value="1" name="isVerify" id="verify-only"
                                           @if(isset($oldInput['isVerify']) && $oldInput['isVerify'] == 1)
                                               checked
                                        @endif>
                                    <label class="form-check-label" for="verify-only">Verified</label>
                                </div>
                                <div class="form-check form-check-inline amr-20">
                                    <input class="form-check-input" type="radio" value="0" name="isVerify" id="not-verify"
                                           @if(isset($oldInput['isVerify']) && $oldInput['isVerify'] == 0)
                                               checked
                                        @endif>
                                    <label class="form-check-label" for="not-verify">Unconfimred</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group search-form-group">
                            <label for="is_delete" class="col-form-label search-label"><b>{{ __('Show Deleted') }}</b></label>
                            <div class="search-input search-radio">
                                <div class="form-check form-check-inline amr-20">
                                    <input class="form-check-input" id="all-member" type="radio" value="0" name="onlyDeleted"
                                           @if(isset($oldInput['onlyDeleted']) && $oldInput['onlyDeleted'] == 0)
                                               checked
                                        @endif
                                    >
                                    <label class="form-check-label" for="all-member">All</label>
                                </div>
                                <div class="form-check form-check-inline amr-20">
                                    <input class="form-check-input" id="not-delete" type="radio" value="" name="onlyDeleted"
                                           @if(!isset($oldInput['onlyDeleted']))
                                               checked
                                        @endif>
                                    <label class="form-check-label" for="not-delete">Active</label>
                                </div>
                                <div class="form-check form-check-inline amr-20">
                                    <input class="form-check-input" id="only-deleted" type="radio" value="1" name="onlyDeleted"
                                           @if(isset($oldInput['onlyDeleted']) && $oldInput['onlyDeleted'] == 1)
                                               checked
                                        @endif>
                                    <label class="form-check-label" for="only-deleted">Deleted</label>
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
                @if (count($users) == 0)
                    <div class="text-center">{{ __('No data.') }}</div>
                @else
                    <div class="table-responsive">
                        <table class="table" id="admin-user-list-table">
                            <thead>
                                <tr class="text-primary">
                                    <th>#</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Role') }}</th>
                                    <th>{{ __('Created') }}</th>
                                    <th>{{ __('Verified At') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ ucfirst(App\Models\User::$roleName[$user->role]) }}</td>
                                    <td>{{ $user->created_at }}</td>
                                    <td>{{ $user->email_verified_at }}</td>
                                    <td>
                                        @if(isset($user->deleted_at))
                                            Deleted
                                        @else
                                            In use
                                        @endif
                                    </td>
                                    <td>
                                        <a class="btn btn-sm btn-round btn-info" href="{{ route('staff.user.profile', ['id' => $user->id]) }}">Detail</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center amt-16">
                        {{ $users->appends(request()->all())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    </div>
@endsection

@push('scripts')
  <script>
    let users = @php echo json_encode($userList) @endphp;
    filterInput(document.getElementById("email-input"), users, 'dropdown-email');
  </script>
@endpush
