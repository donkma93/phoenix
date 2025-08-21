@extends('layouts.staff')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('staff.dashboard')
        ],
        [
            'text' => 'User',
            'url' => route('staff.user.list')
        ],
        [
            'text' => 'Profile'
        ]
    ]
])
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
<div class="fade-in">
    <form action="{{ route('staff.user.update') }}" id="user-info-form" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">{{ __('User profile') }}</h2>
            </div>
            <div class="card-body row">
                <input type="hidden" name="id" value="{{ $userInfo['id'] }}">
                <div class="col-md-5">
                    <div id="img-preview" class="img-preview show" data-size="250px" data-init="@if(isset($userInfo['profile']['avatar'])){{
                        asset($userInfo['profile']['avatar'])
                    }}
                    @else
                    {{
                        asset('images/default.jpg')
                    }}
                    @endif"></div>
                </div>

                <div class="col-md-7">
                    <div class="search-form-group apy-6">
                        <label for="first_name" class="search-label"><b>{{ __('First name') }}</b> </label>
                        <div class="search-input">
                            {{ $userInfo['profile']['first_name'] }}
                        </div>
                    </div>

                    <div class="search-form-group apy-6">
                        <label for="last_name" class="search-label"><b>{{ __('Last name') }}</b></label>
                        <div class="search-input">
                            {{ $userInfo['profile']['last_name'] }}
                        </div>
                    </div>

                    <div class="search-form-group apy-6">
                        <label for="gender" class="search-label"><b>{{ __('Gender') }}</b></label>
                        <div class="search-input">
                            @if($userInfo['profile']['gender'] == 0)
                                {{__('Male')}}
                            @else
                                {{__('Female')}}
                            @endif
                        </div>
                    </div>

                    <div class="search-form-group apy-6">
                        <label for="birthday" class="search-label"><b>{{ __('Birthday') }}</b></label>
                        <div class="search-input">
                            {{ date('Y-m-d', strtotime($userInfo['profile']['birthday'])) }}
                        </div>
                    </div>

                    <div class="search-form-group apy-6">
                        <label for="phone" class="search-label"><b>{{ __('Phone Number') }}: </b></label>
                        <div class="search-input">
                            {{ $userInfo['profile']['phone'] }}
                        </div>
                    </div>

                    <div class="search-form-group apy-6">
                        <label for="phone" class="search-label"><b>{{ __('Verify at') }}: </b></label>
                        @if(isset($userInfo['email_verified_at']))
                            <div class="search-input">
                                {{ date('Y-m-d', strtotime($userInfo['email_verified_at'])) }}
                            </div>
                        @else 
                            <div class="btn btn-sm btn-info" data-toggle="modal" data-target=".modal" onclick="callModal('verify')">Verify</div>
                            <input type="submit" id="verify-submit" name="verify-submit" style="display:none" />
                        @endif
                    </div>

                    <div class="search-form-group apy-6">
                        <label for="membership" class="search-label"><b>{{ __('Membership At') }}: </b></label>
                        @if(isset($userInfo['profile']['membership_at']))
                            <div class="search-input">
                                {{ date('Y-m-d', strtotime($userInfo['profile']['membership_at'])) }}
                            </div>
                        @elseif (isset($userInfo['email_verified_at']))
                            <div class="btn btn-sm btn-success" data-toggle="modal" data-target=".modal" onclick="callModal('membership')">Set membership</div>
                            <input type="submit" id="membership-submit" name="membership-submit" style="display:none" />
                        @else
                            <div class="search-input">
                                Waiting for verify
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">User Package</h2>
        </div>
        <div class="card-body">
            <div class="px-4">
                <h2>Package Summary</h2>
                <hr>
                <div class="row">
                    <div class="col-sm-6 col-md-4 amb-24">
                        <div class="card arounded-6 border-info h-100">
                            <div class="card-body text-center apx-8">
                                <div class="font-64 atext-blue-500">{{ $workInfo['packageTotal'] }}</div>
                                <div class="text-uppercase flex-center atext-gray-600 font-weight-medium">
                                    <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width: 16px; height: 16px">
                                        <i class="fa fa-calculator font-8 atext-gray-400"></i>
                                    </span>
                                    Total Package
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4 amb-24">
                        <div class="card arounded-6 border-info h-100">
                            <div class="card-body text-center apx-8">
                                <div class="font-64 atext-red-500">{{ $workInfo['packageCount'][App\Models\Package::STATUS_SHIPPING] ?? 0 }}</div>
                                <div class="text-uppercase flex-center atext-gray-600 font-weight-medium">
                                    <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width: 16px; height: 16px">
                                        <i class="fa fa-truck font-8 atext-gray-400"></i>
                                    </span>
                                Package Shipping
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4 amb-24">
                        <div class="card arounded-6 border-info h-100">
                            <div class="card-body text-center apx-8">
                                <div class="font-64 atext-green-500">{{ $workInfo['packageCount'][App\Models\Package::STATUS_INBOUND] ?? 0 }}</div>
                                <div class="text-uppercase flex-center atext-gray-600 font-weight-medium">
                                    <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width: 16px; height: 16px">
                                        <i class="fa fa-cube font-8 atext-gray-400"></i>
                                    </span>
                                    Package Inbound
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4 amb-24">
                        <div class="card arounded-6 border-info h-100">
                            <div class="card-body text-center apx-8">
                                <div class="font-64 atext-yellow-500">{{ $workInfo['packageCount'][App\Models\Package::STATUS_STORED] ?? 0 }}</div>
                                <div class="text-uppercase flex-center atext-gray-600 font-weight-medium">
                                    <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width: 16px; height: 16px">
                                        <i class="fa fa-bookmark font-8 atext-gray-400"></i>
                                    </span>
                                Package Stored
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4 amb-24">
                        <div class="card arounded-6 border-info h-100">
                            <div class="card-body text-center apx-8">
                                <div class="font-64 atext-gray-500">{{ $workInfo['packageCount'][App\Models\Package::STATUS_OUTBOUND] ?? 0 }}</div>
                                <div class="text-uppercase flex-center atext-gray-600 font-weight-medium">
                                    <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width: 16px; height: 16px">
                                        <i class="fa fa-arrow-right font-8 atext-gray-400"></i>
                                    </span>
                                    Package Outbound
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br><br>

            <div class="px-4">
                <h2>Request Summary</h2>
                <hr>
                <div class="row">
                    <div class="col-sm-6 col-md-4 amb-24">
                        <div class="card arounded-6 border-dark h-100">
                            <div class="card-body text-center apx-8">
                                <div class="font-64 atext-blue-500">{{ $workInfo['requestTotal'] ?? 0 }}</div>
                                <div class="text-uppercase flex-center atext-gray-600 font-weight-medium">
                                    <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width: 16px; height: 16px">
                                        <i class="fa fa-calculator font-8 atext-gray-400"></i>
                                    </span>
                                    Total Request
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4 amb-24">
                        <div class="card arounded-6 border-dark h-100">
                            <div class="card-body text-center apx-8">
                                <div class="font-64 atext-gray-500">{{ $workInfo['requestCount'][App\Models\UserRequest::STATUS_NEW] ?? 0 }}</div>
                                <div class="text-uppercase flex-center atext-gray-600 font-weight-medium">
                                    <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width: 16px; height: 16px">
                                        <i class="fa fa-file-o font-8 atext-gray-400"></i>
                                    </span>
                                    Request New
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4 amb-24">
                        <div class="card arounded-6 border-dark h-100">
                            <div class="card-body text-center apx-8">
                                <div class="font-64 atext-green-500">{{ $workInfo['requestCount'][App\Models\UserRequest::STATUS_INPROGRESS] ?? 0 }}</div>
                                <div class="text-uppercase flex-center atext-gray-600 font-weight-medium">
                                    <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width: 16px; height: 16px">
                                        <i class="fa fa-spinner font-8 atext-gray-400"></i>
                                    </span>
                                    Request Inprogress
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4 amb-24">
                        <div class="card arounded-6 border-dark h-100">
                            <div class="card-body text-center apx-8">
                                <div class="font-64 atext-yellow-500">{{ $workInfo['requestCount'][App\Models\UserRequest::STATUS_DONE] ?? 0 }}</div>
                                <div class="text-uppercase flex-center atext-gray-600 font-weight-medium">
                                    <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width: 16px; height: 16px">
                                        <i class="fa fa-check font-8 atext-gray-400"></i>
                                    </span>
                                    Request Done
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-md-4 amb-24">
                        <div class="card arounded-6 border-dark h-100">
                            <div class="card-body text-center apx-8">
                                <div class="font-64 atext-red-500">{{ $workInfo['requestCount'][App\Models\UserRequest::STATUS_CANCEL] ?? 0 }}</div>
                                <div class="text-uppercase flex-center atext-gray-600 font-weight-medium">
                                    <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width: 16px; height: 16px">
                                        <i class="fa fa-check font-8 atext-gray-400"></i>
                                    </span>
                                    Request Cancel
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                WARNING
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer btn-update-user">
                <button type="button" class="btn btn-default " data-dismiss="modal">Cancel</button>

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        function callModal(type) {
            let btn
            const element = $(".btn-update-user");
            element.find(".btn-ok").remove();

            if(type == 'verify') {
                $('.modal-body').text('Are you sure verify for this user ?')
                btn = $('<div>').addClass('btn btn-primary btn-ok').text('Verify').on('click', setVerify);
            } else {
                $('.modal-body').text('Are you sure set membership for this user ?')
                btn = $('<div>').addClass('btn btn-primary btn-ok').text('Update').on('click', setMembership);
            }
            
            element.append(btn);
        }

        function setMembership() {
            $('#membership-submit').click();
        }
        
        function setVerify() {
            $('#verify-submit').click();
        }
    </script>
@endsection
