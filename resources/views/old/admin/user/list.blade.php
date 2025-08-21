@extends('layouts.admin')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('admin.dashboard')
        ],
        [
            'text' => 'User'
        ]
    ]
])
@endsection

@section('content')
    <div class="fade-in">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="mb-0">{{ __('User list') }}</h2>
                <a class="btn btn-success" href="{{ route('admin.user.new') }}">
                    {{ __('New User') }}
                </a>
            </div>
            <form method="GET" action="{{ route('admin.user.list') }}" class="form-horizontal" role="form">
                <div class="card-body">
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
                                <input class="form-check-input" type="radio" name="isVerify" value=""
                                @if(!isset($oldInput['isVerify']))
                                    checked
                                @endif
                                >
                                <label class="form-check-label" for="all-verify">All</label>
                            </div>
                            <div class="form-check form-check-inline amr-20">
                                <input class="form-check-input" type="radio" value="1" name="isVerify"
                                @if(isset($oldInput['isVerify']) && $oldInput['isVerify'] == 1)
                                    checked
                                @endif>
                                <label class="form-check-label" for="verify-only">Verify only</label>
                            </div>
                            <div class="form-check form-check-inline amr-20">
                                <input class="form-check-input" type="radio" value="0" name="isVerify"
                                @if(isset($oldInput['isVerify']) && $oldInput['isVerify'] == 0)
                                    checked
                                @endif>
                                <label class="form-check-label" for="not-verify">Not verify</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label for="is_delete" class="col-form-label search-label"><b>{{ __('Show Deleted') }}</b></label>
                        <div class="search-input search-radio">
                            <div class="form-check form-check-inline amr-20">
                                <input class="form-check-input" id="all-verify" type="radio" value="0" name="onlyDeleted"
                                @if(isset($oldInput['onlyDeleted']) && $oldInput['onlyDeleted'] == 0)
                                    checked
                                @endif
                                >
                                <label class="form-check-label" for="all-member">All</label>
                            </div>
                            <div class="form-check form-check-inline amr-20">
                                <input class="form-check-input" id="verify-only" type="radio" value="1" name="onlyDeleted"
                                @if(isset($oldInput['onlyDeleted']) && $oldInput['onlyDeleted'] == 1)
                                    checked
                                @endif>
                                <label class="form-check-label" for="only-deleted">Only deleted</label>
                            </div>
                            <div class="form-check form-check-inline amr-20">
                                <input class="form-check-input" id="not-verify" type="radio" value="" name="onlyDeleted"
                                @if(!isset($oldInput['onlyDeleted']))
                                    checked
                                @endif>
                                <label class="form-check-label" for="not-delete">Not deleted</label>
                            </div>
                        </div>
                    </div>


                    <div class="search-form-group">
                        <div class="search-label d-none d-sm-block"></div>
                        <div class="search-input text-center text-sm-left">
                            <input class="btn btn-primary" type="submit" value="{{ __('Search') }}">
                        </div>
                    </div>
                </div>
            </form>

            <div class="card-footer">
                @if (count($users) == 0)
                    <div class="text-center">{{ __('No data.') }}</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-align-middle table-bordered table-striped table-sm" id="admin-user-list-table">
                            <thead>
                                <tr>
                                    <th>No</th>
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
                                        <a class="btn btn-info" href="{{ route('admin.user.profile', ['id' => $user->id]) }}">Detail</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center justify-content-md-end amt-16">
                        {{ $users->appends(request()->all())->links('components.pagination') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
  <script>
    let users = @php echo json_encode($userList) @endphp;
    filterInput(document.getElementById("email-input"), users, 'dropdown-email');
  </script>
@endsection
