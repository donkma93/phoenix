@extends('layouts.admin')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('admin.dashboard')
        ],
        [
            'text' => 'Store Fulfill',
            'url' => route('admin.storeFulfill.list')
        ],
        [
            'text' => 'Detail'
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
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('Store detail') }}</h2>
            <form action="{{ route('admin.storeFulfill.delete') }}" id="delete-store-form" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $store['id'] }}">
                @if(!isset($store['deleted_at']))
                    <div class="btn btn-danger" data-toggle="modal" data-target=".modal" onclick="callModal('store' , 0)">Delete</div>
                @else
                    <div class="btn btn-primary" data-toggle="modal" data-target=".modal" onclick="callModal('store' , 1)">Restore</div>
                @endif
            </form>
        </div>
        <div class="card-body">
            <div class="form-group search-form-group">
                <label for="first_name" class="col-form-label search-label"><b>{{ __('Name') }}</b> </label>
                <div class="col-form-label">
                    {{ $store['name'] }}
                </div>
            </div>

            <div class="form-group search-form-group">
                <label for="last_name" class="col-form-label search-label"><b>{{ __('Code') }}</b></label>
                <div class="col-form-label">
                    {{ $store['code']}}
                </div>
            </div>
            
            <div class="form-group search-form-group">
                <label for="last_name" class="col-form-label search-label"><b>{{ __('Status') }}</b></label>
                <div class="col-form-label">
                    @if(isset($store['deleted_at']))
                        {{ __('Deleted') }}
                    @else
                        {{ __('In use') }}
                    @endif
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
            <div class="modal-footer btn-delete-store">
                <button type="button" class="btn btn-default " data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')
    <script>
        function callModal(type, isDelete = 0) {
            const element = $(".btn-delete-store");
            $(".btn-ok").remove();
            let btn = "<button class='btn btn-danger btn-ok' onclick='deleteStore()'>Delete</button>"
            $(".modal-body").text('Are you sure to delete this store?')

            if(isDelete == 1) {
                $(".modal-body").text('Are you sure to restore this store?')
                btn = "<button class='btn btn-primary btn-ok' onclick='deleteStore()'>Restore</button>"
            }
            element.append(btn);
        }

        function deleteStore() {
            $('#delete-store-form').submit();
        }
    </script>
@endsection
