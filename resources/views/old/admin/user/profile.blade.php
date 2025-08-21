@extends('layouts.admin')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('admin.dashboard')
        ],
        [
            'text' => 'User',
            'url' => route('admin.user.list')
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
    <form action="{{ route('admin.user.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h2 class="mb-0">{{ __('User Profile') }}</h2>
                @if(isset($userInfo['deleted_at']))
                    <input  type="submit" hidden id="submit-user-delete" value="restore" name="delete" />
                    <div class="btn btn-primary" data-toggle="modal" data-target=".modal" onclick="callModal('user')">{{ __('Restore') }}</div>
                @else
                    <input type="submit" hidden id="submit-user-delete" value="delete" name="delete" />
                    <div class="btn btn-danger" data-toggle="modal" data-target=".modal" onclick="callModal('user', 0)">{{ __('Delete') }}</div>
                @endif
            </div>
            <div class="row card-body">
                <input type="hidden" name="id" value="{{ $userInfo['id'] }}">
                <div class="col-md-5">
                    <div id="img-preview" class="img-preview show" data-bgSize="contain
                    " data-size="250px" data-init="
                    @if(isset($userInfo['profile']['avatar']))
                    {{
                        asset($userInfo['profile']['avatar'])
                    }}
                    @else
                    {{
                        asset('images/default.jpg')
                    }}
                    @endif
                    "></div>
                </div>

                <div class="col-md-7">
                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label mb-0"><b>{{ __('First name') }}</b> </label>
                        <div class="search-input col-form-label">
                            {{ $userInfo['profile']['first_name'] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label mb-0"><b>{{ __('Last name') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $userInfo['profile']['last_name'] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label mb-0"><b>{{ __('Role') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ ucfirst(App\Models\User::$roleName[$userInfo['role']]) }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label mb-0"><b>{{ __('Gender') }}</b></label>
                        <div class="search-input col-form-label">
                            @if($userInfo['profile']['gender'] == 0)
                                {{__('Male')}}
                            @else
                                {{__('Female')}}
                            @endif
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label mb-0"><b>{{ __('Birthday') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ date('d-m-Y', strtotime($userInfo['profile']['birthday'])) }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label class="col-form-label search-label mb-0"><b>{{ __('Phone Number') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ $userInfo['profile']['phone'] }}
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label for="status" class="col-form-label search-label mb-0"><b>{{ __('Status') }}</b></label>
                        <div class="search-input col-form-label">
                            @if(isset($userInfo['deleted_at']))
                                Deleted
                            @else
                                In use
                            @endif
                        </div>
                    </div>

                    <div class="search-form-group align-items-center">
                        <label class="col-form-label search-label mb-0"><b>{{ __('Verify At') }}</b></label>
                        <div class="col-form-label d-flex align-items-center">
                            @if(isset($userInfo['email_verified_at']))
                                <div class="amr-20">
                                        {{ date('Y-m-d', strtotime($userInfo['email_verified_at'])) }}
                                </div>
                            @endif
                            @if(isset($userInfo['email_verified_at']))
                                <input type="submit" hidden id="submit-verify" value="0" name="verify" />
                                <div class="btn btn-danger" data-toggle="modal" data-target=".modal" onclick="callModal('verify' , 0)">{{ __('Remove') }}</div>
                            @else
                                <input  type="submit" hidden id="submit-verify" value="1" name="verify" />
                                <div class="btn btn-primary" data-toggle="modal" data-target=".modal" onclick="callModal('verify')">{{ __('Set verify') }}</div>
                            @endif
                        </div>
                    </div>

                    @if($userInfo['role'] == App\Models\User::ROLE_USER)
                        <div class="apy-6 search-form-group align-items-center">
                            <label for="membership" class="col-form-label search-label mb-0 "><b>{{ __('Membership At') }}</b></label>
                            <div class="col-form-label d-flex align-items-center">
                                @if(isset($userInfo['profile']['membership_at']))
                                    <div class="amr-20">
                                            {{ date('Y-m-d', strtotime($userInfo['profile']['membership_at'])) }}
                                    </div>
                                @endif
                                @if(isset($userInfo['profile']['membership_at']))
                                    <input type="submit" hidden id="submit-membership" value="0" name="membership" />
                                    <div class="btn btn-danger" data-toggle="modal" data-target=".modal" onclick="callModal('membership' , 0)">{{ __('Remove') }}</div>
                                @else
                                    <input  type="submit" hidden id="submit-membership" value="1" name="membership" />
                                    <div class="btn btn-primary" data-toggle="modal" data-target=".modal" onclick="callModal('membership')">{{ __('Set membership') }}</div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </form>

    @if($userInfo['role'] == App\Models\User::ROLE_USER)
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
                                        <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width16px; height16px">
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
                                        <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width16px; height16px">
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
                                        <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width16px; height16px">
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
                                        <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width16px; height16px">
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
                                        <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width16px; height16px">
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
                    <div class="row justify-content-center justify-content-md-between">
                        <div class="col-sm-6 col-md-4 amb-24">
                            <div class="card arounded-6 border-dark h-100">
                                <div class="card-body text-center apx-8">
                                    <div class="font-64 atext-blue-500">{{ $workInfo['requestTotal'] }}</div>
                                    <div class="text-uppercase flex-center atext-gray-600 font-weight-medium">
                                        <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width16px; height16px">
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
                                    <div class="font-64 atext-red-500">{{ $workInfo['requestCount'][App\Models\UserRequest::STATUS_NEW] ?? 0 }}</div>
                                    <div class="text-uppercase flex-center atext-gray-600 font-weight-medium">
                                        <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width16px; height16px">
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
                                        <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width16px; height16px">
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
                                        <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width16px; height16px">
                                            <i class="fa fa-check font-8 atext-gray-400"></i>
                                        </span>
                                        Request Done
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(in_array($userInfo['role'], [App\Models\User::ROLE_PICKER, App\Models\User::ROLE_PACKER, App\Models\User::ROLE_RECEIVER, App\Models\User::ROLE_STAFF, App\Models\User::ROLE_STAFF_EPACKET]))
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Staff work</h2>
            </div>
            <div class="card-body">
                <div class="px-4">
                    <h2>Request Summary</h2>
                    <hr>
                    <div class="row justify-content-center justify-content-md-between">
                        <div class="col-sm-6 col-md-4 amb-24">
                            <div class="card arounded-6 border-dark h-100">
                                <div class="card-body text-center apx-8">
                                    <div class="font-64 atext-blue-500">{{ $workInfo['requestTotal'] }}</div>
                                    <div class="text-uppercase flex-center atext-gray-600 font-weight-medium">
                                        <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width16px; height16px">
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
                                    <div class="font-64 atext-red-500">{{ $workInfo['requestCount'][App\Models\UserRequest::STATUS_NEW] ?? 0 }}</div>
                                    <div class="text-uppercase flex-center atext-gray-600 font-weight-medium">
                                        <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width16px; height16px">
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
                                        <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width16px; height16px">
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
                                        <span class="flex-center amr-8 border rounded-circle abd-gray-400" style="width16px; height16px">
                                            <i class="fa fa-check font-8 atext-gray-400"></i>
                                        </span>
                                        Request Done
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                WARNING
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer btn-delete-area">
                <button type="button" class="btn btn-default " data-dismiss="modal">Cancel</button>

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
  <script>
      function callModal(type, status = 1) {
        const element = $(".btn-delete-area");
        let btn
        $(".btn-ok").remove();

        switch(type) {
            case 'membership': {
                $(".modal-body").text('Are you sure set membership this user?')
                btn = "<button class='btn btn-primary btn-ok' onclick='deleteMembership()'>Set membership</button>"
                if(status == 0) {
                    $(".modal-body").text('Are you sure delete membership this user?')
                    btn = "<button class='btn btn-danger btn-ok' onclick='deleteMembership()'>Delete</button>"
                }
            }
            break

            case 'user': {
                $(".modal-body").text('Are you sure restore this user?')
                btn = "<button class='btn btn-primary btn-ok' onclick='deleteUser()'>Restore</button>"
                if(status == 0) {
                    $(".modal-body").text('Are you sure delete this user?')
                    btn = "<button class='btn btn-danger btn-ok' onclick='deleteUser()'>Delete</button>"
                }
            }
            break

            case 'verify': {
                $(".modal-body").text('Are you sure unverified this user?')
                btn = "<button class='btn btn-primary btn-ok' onclick='deleteVerify()'>Verify</button>"
                if(status == 0) {
                    $(".modal-body").text('Are you sure verify this user?')
                    btn = "<button class='btn btn-danger btn-ok' onclick='deleteVerify()'>Unverified</button>"
                }
            }
            break
        }

        element.append(btn);
      }

      function deleteUser() {
        $('#submit-user-delete').click()
      }

      function deleteMembership() {
        $('#submit-membership').click()
      }

      function deleteVerify() {
        $('#submit-verify').click()
      }
  </script>
@endsection
