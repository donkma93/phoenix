@extends('layouts.admin')

@section('breadcrumb')
    @include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('admin.dashboard')
        ],
        [
            'text' => 'Request list'
        ]
    ]
])
@endsection

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-header">
            <h2 class="mb-0">{{ __('Request list') }}</h2>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('admin.request.list') }}" class="form-horizontal" role="form">
                <div class="form-group search-form-group">
                    <label for="type" class="col-form-label search-label"><b>{{ __('Email') }}</b></label>
                    <div class="search-input position-relative">
                        <input type="input" class="form-control w-100" id="email-input" list="dropdown-email" name="email" value="@if (isset($oldInput['email'])){{$oldInput['email']}}@endif" autocomplete="off" />
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="type" class="col-form-label search-label"><b>{{ __('Type') }}</b></label>
                    <div class="search-input">
                        <select id="type" name="type" class="form-control w-100">
                            <option selected></option>
                            @foreach ($requestTypes as $type)
                                <option value="{{ $type }}"
                                    @if (isset($oldInput['type']) && $oldInput['type'] == $type)
                                        selected="selected"
                                    @endif
                                >{{  ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label for="status" class="col-form-label search-label"><b>{{ __('Status') }}</b></label>
                    <div class="search-input">
                        <select id="type" name="status" class="form-control w-100">
                            <option selected></option>
                            @foreach (App\Models\UserRequest::$statusName as $key => $status)
                                <option value="{{ $key }}"
                                    @if (isset($oldInput['status']) && $oldInput['status'] == $key)
                                        selected="selected"
                                    @endif
                                >{{ $status }}</option>
                            @endforeach
                        </select>
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
            @if (count($requests) == 0)
                <div class="text-center">{{ __('No data.') }}</div>
            @else
                <div class="table-responsive">
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="admin-request-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Group Names') }}</th>
                                <th>{{ __('Option') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Created') }}</th>
                                <th>{{ __('Start') }}</th>
                                <th>{{ __('End') }}</th>
                                <th @if (isset($oldInput['status']) && $oldInput['status'] == "2") class="th-done" @endif></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                            <tr class="row-{{ $request->id }}">
                                <td>{{ ($requests->currentPage() - 1) * $requests->perPage() + $loop->iteration }}</td>
                                <td>{{ $request->user->email }}</td>
                                <td>{{ ucfirst($request->mRequestType->name) ?? '' }}</td>
                                <td>{{ $groupNames[$request->id] ?? '' }}</td>
                                <td>{{ App\Models\UserRequest::$optionName[$request->option] ?? '' }}</td>
                                <td>
                                    @foreach (App\Models\UserRequest::$statusName as $key => $status)
                                        @if ($request->status == $key)
                                            {{ $status }}
                                        @endif
                                    @endforeach
                                </td>
                                <th>{{ $request->created_at }}</th>
                                <th>{{ $request->start_at }}</th>
                                <th>{{ $request->finish_at }}</th>
                                <td>
                                    <a class="btn action-btn btn-info" href="{{
                                    route('admin.request.detail', ['id' => $request->id]) }}">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center justify-content-md-end amt-16">
                    {{ $requests->appends(request()->all())->links('components.pagination') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let emails = @php echo json_encode($emails) @endphp;
    filterInput(document.getElementById("email-input"), emails, 'dropdown-email');
</script>
@endsection
