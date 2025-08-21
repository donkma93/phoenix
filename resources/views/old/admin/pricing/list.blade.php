@extends('layouts.admin')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('admin.dashboard')
        ],
        [
            'text' => 'Pricing'
        ]
    ]
])
@endsection

@section('content')
    <div class="fade-in">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="mb-0">{{ __('Pricing list') }}</h2>
            </div>
            <form method="GET" action="{{ route('admin.pricing.list') }}" class="form-horizontal" role="form">
                <div class="card-body">
                    <div class="form-group search-form-group">
                        <label for="email" class="col-form-label search-label"><b>{{ __('Email') }}</b></label>
                        <div class="search-input">
                            <input type="input" id="email-input"  list="dropdown-email" class="form-control w-100" name="email" value="@if (isset($oldInput['email'])){{$oldInput['email']}}@endif" />
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label for="is_delete" class="col-form-label search-label"><b>{{ __('Reply') }}</b></label>
                        <div class="search-input search-radio">
                            <div class="form-check form-check-inline amr-20">
                                <input class="form-check-input" id="all-verify" type="radio" value="0" name="isDone"
                                @if(isset($oldInput['isDone']) && $oldInput['isDone'] == 0)
                                    checked
                                @endif
                                >
                                <label class="form-check-label" for="all-member">Not Send</label>
                            </div>
                            <div class="form-check form-check-inline amr-20">
                                <input class="form-check-input" id="verify-only" type="radio" value="1" name="isDone"
                                @if(isset($oldInput['isDone']) && $oldInput['isDone'] == 1)
                                    checked
                                @endif>
                                <label class="form-check-label" for="only-deleted">Sent</label>
                            </div>
                            <div class="form-check form-check-inline amr-20">
                                <input class="form-check-input" id="not-verify" type="radio" value="" name="isDone"
                                @if(!isset($oldInput['isDone']))
                                    checked
                                @endif>
                                <label class="form-check-label" for="not-delete">All</label>
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
                @if (count($pRequests) == 0)
                    <div class="text-center">{{ __('No data.') }}</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-align-middle table-bordered table-striped table-sm" id="admin-pricing-list-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Company') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Services') }}</th>
                                    <th>{{ __('Request date') }}</th>
                                    <th>{{ __('Note') }}</th>
                                    <th></th>
                                    <th>{{ __('Is sent') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pRequests as $request)
                                    <form method="POST" action="{{ route('admin.pricing.update') }}" class="form-horizontal" role="form">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $request->id }}" />
                                        @if(isset($request->user_id) && $request->user_id != null)
                                            <tr>
                                                <td>{{ ($pRequests->currentPage() - 1) * $pRequests->perPage() + $loop->iteration }}</td>
                                                <td>{{ $request->user->email }}</td>
                                                <td>{{ $request->user->profile->company_name }}</td>
                                                <td>{{ $request->user->profile->first_name ?? ""}} {{ $request->user->profile->last_name ?? ""}}</td>
                                                <td>{{ $request->user->profile->phone ?? "" }}</td>
                                                <td>{{ $request->services }}</td>
                                                <td>{{ $request->created_at }}</td>
                                                <td>{{ $request->note }}</td>
                                                <td>
                                                    <div id="copy-button-{{ $request->id }}" class="btn btn-success" onclick="copyToClipboard({{ $request->id }}, '{{ $request->user->email }}')">Copy email</div> 
                                                </td>
                                                <td>
                                                    @if($request->is_done == 0)
                                                        <input type="submit" class="btn btn-info" value="Sent" /> 
                                                    @else
                                                        Done
                                                    @endif
                                                </td>
                                            </tr>
                                        @else 
                                            <tr>
                                                <td>{{ ($pRequests->currentPage() - 1) * $pRequests->perPage() + $loop->iteration }}</td>
                                                <td>{{ $request->email }}</td>
                                                <td>{{ $request->company }}</td>
                                                <td>{{ $request->name }}</td>
                                                <td>{{ $request->phone }}</td>
                                                <td>{{ $request->services }}</td>
                                                <td>{{ $request->created_at }}</td>
                                                <td>{{ $request->note }}</td>
                                                <td>
                                                    <div id="copy-button-{{ $request->id }}" class="btn btn-success" onclick="copyToClipboard({{ $request->id }}, '{{ $request->email }}')">Copy email</div> 
                                                </td>
                                                <td>
                                                    @if($request->is_done == 0)
                                                        <input type="submit" class="btn btn-info" value="Sent" /> 
                                                    @else
                                                        Done
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    </form>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center justify-content-md-end amt-16">
                        {{ $pRequests->appends(request()->all())->links('components.pagination') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
  <script>
    let users = @php echo json_encode($users) @endphp;
    filterInput(document.getElementById("email-input"), users, 'dropdown-email');

    function copyToClipboard(index, text) {
        document.getElementById(`copy-button-${index}`).remove();
        navigator.clipboard.writeText(text);
    }
  </script>
@endsection
