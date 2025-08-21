@extends('layouts.admin')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('admin.dashboard')
        ],
        [
            'text' => 'Warehouse',
            'url' => route('admin.warehouse.list')
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

@error('avatar')
@section('flash')
@include('layouts.partials.flash', [
    'messages' => [
        [
            'content' => $message,
            'type' => 'error'
        ]
    ]
])
@endsection
@enderror

@if(session('updateUnitSuccess'))
@section('flash')
@include('layouts.partials.flash', [
    'messages' => [
        [
            'content' => session('updateUnitSuccess')
        ]
    ]
])
@endsection
@endif

@if(session('updateUnitFail'))
@section('flash')
@include('layouts.partials.flash', [
    'messages' => [
        [
            'content' => session('updateUnitFail'),
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
            <h2 class="mb-0">{{ __('Warehouse detail') }}</h2>
            <form action="{{ route('admin.warehouse.delete') }}" id="delete-warehouse-form" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $warehouse['id'] }}">
                @if(!isset($warehouse['deleted_at']))
                    <div class="btn btn-danger" data-toggle="modal" data-target=".modal" onclick="callModal('warehouse' , 0)">Delete</div>
                @else
                    <div class="btn btn-primary" data-toggle="modal" data-target=".modal" onclick="callModal('warehouse' , 1)">Restore</div>
                @endif
            </form>
        </div>
        <div class="card-body">
            <div class="form-group search-form-group">
                <label for="first_name" class="col-form-label search-label"><b>{{ __('Warehouse name') }}</b> </label>
                <div class="col-form-label">
                    {{ $warehouse['name'] }}
                </div>
            </div>

            <div class="form-group search-form-group">
                <label for="last_name" class="col-form-label search-label"><b>{{ __('Address') }}</b></label>
                <div class="col-form-label">
                    {{ $warehouse['address']}}
                </div>
            </div>
            
            <div class="form-group search-form-group">
                <label for="last_name" class="col-form-label search-label"><b>{{ __('Total Area') }}</b></label>
                <div class="col-form-label amr-16">
                    {{ $totalArea }}
                </div>
                <a class="btn btn-success" href="{{ route('admin.warehouseArea.list', ['warehouse' => $warehouse['name']])}}">Check</a>
            </div>

            <div class="form-group search-form-group">
                <label for="last_name" class="col-form-label search-label"><b>{{ __('Status') }}</b></label>
                <div class="col-form-label">
                    @if(isset($warehouse['deleted_at']))
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
            <div class="modal-footer btn-delete-warehouse">
                <button type="button" class="btn btn-default " data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')
    <script>
        function callModal(type, isDelete = 0) {
            const element = $(".btn-delete-warehouse");
            $(".btn-ok").remove();
            let btn = "<button class='btn btn-danger btn-ok' onclick='deleteWarehouse()'>Delete</button>"
            $(".modal-body").text('Are you sure to delete this warehouse?')

            if(isDelete == 1) {
                $(".modal-body").text('Are you sure to restore this warehouse?')
                btn = "<button class='btn btn-primary btn-ok' onclick='deleteWarehouse()'>Restore</button>"
            }
            element.append(btn);
        }
        function callUnitModal(id) {
            const element = $(".btn-delete-warehouse");
            $(".btn-ok").remove();
            let btn = `<button class="btn btn-danger btn-ok" onclick="deletePrice(${id})"">Delete</button>`
            $(".modal-body").text('Are you sure for delete this price?')

            element.append(btn);
        }

        function deleteWarehouse() {
            $('#delete-warehouse-form').submit();
        }

        function deletePrice(id) {
            $('#delete-unit-form-' + id).submit();
        }
    </script>
@endsection
