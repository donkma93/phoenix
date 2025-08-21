@extends('layouts.staff')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('staff.dashboard')
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
        <div class="card-header">
            <h2 class="mb-0">User list</h2>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('staff.user.list') }}" class="form-horizontal" role="form">
                <div class="form-group search-form-group">
                    <label for="email" class="col-form-label search-label"><b>{{ __('Email') }}</b></label>
                    <div class="search-input">
                        <input type="email" class="form-control" name="email" id="email-input" list="dropdown-email" value="@if (isset($oldInput['email'])){{$oldInput['email']}}@endif" />
                    </div>
                </div>
                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Membership') }}</b></label>
                    <div class="search-input search-radio">
                        <div class="form-check form-check-inline amr-20">
                            <input class="form-check-input" type="radio" id="all-verify" name="isMembership" value=""
                            @if(!isset($oldInput['isMembership']))
                                checked
                            @endif
                             />
                            <label class="form-check-label amb-2" for="all-verify">All</label>
                        </div>
                        <div class="form-check form-check-inline amr-20">
                            <input class="form-check-input" type="radio" id="verify-only" value="1" name="isMembership"
                            @if(isset($oldInput['isMembership']) && $oldInput['isMembership'] == 1)
                                checked
                            @endif>
                            <label class="form-check-label amb-2" for="verify-only">Membership only</label>
                        </div>
                        <div class="form-check form-check-inline amr-20">
                            <input class="form-check-input" type="radio" id="not-verify" value="0" name="isMembership"
                            @if(isset($oldInput['isMembership']) && $oldInput['isMembership'] == 0)
                                checked
                            @endif>
                            <label class="form-check-label amb-2" for="not-verify">Not membership</label>
                        </div>
                    </div>
                </div>
                <div class="search-form-group">
                    <div class="search-label d-none d-sm-block"></div>
                    <div class="search-input text-center text-sm-left">
                        <input class="btn btn-primary" type="submit" value="{{ __('Search') }}">
                    </div>
                </div>
            </form>
        </div>
        <div class="card-footer">
            @if (count($users) == 0)
                <div class="text-center">{{ __('No data.') }}</div>
            @else
                <div class="table-responsive">
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="user-list-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Created') }}</th>
                                <th>{{ __('Membership At') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->created_at }}</td>
                                <td>
                                    @if(isset($user->profile->membership_at))
                                        {{ $user->profile->membership_at }}
                                    @endif
                                </td>
                                <td>
                                    @if(isset($user->email_verified_at))
                                        Verified
                                    @else
                                        Not verified
                                    @endif
                                </td>
                                <td>
                                    <a class="btn btn-info" href="{{ route('staff.user.profile', ['id' => $user->id]) }}">Detail</a>
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
    let email = @php echo json_encode($email) @endphp;
    filterInput(document.getElementById("email-input"), email, 'dropdown-email');
</script>
@endsection
