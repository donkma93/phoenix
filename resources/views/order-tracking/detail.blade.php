@extends('layouts.admin')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('admin.dashboard')
        ],
        [
            'text' => 'Order Inventory',
            'url' => route('admin.partner.list')
        ],
        [
            'text' => $partner['id']
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
            <h2 class="mb-0">{{ __('Partner detail') }}</h2>
            <form action="{{ route('admin.partner.delete') }}" id="delete-partner-form" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $partner['id'] }}">
                @if(!isset($partner['deleted_at']))
                    <div class="btn btn-danger" data-toggle="modal" data-target="#confirm-delete" onclick="callModal(0)">Delete</div>
                @else
                    <div class="btn btn-primary" data-toggle="modal" data-target="#confirm-delete" onclick="callModal(1)">Restore</div>
                @endif
            </form>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.partner.update') }}" method="POST" enctype="multipart/form-data">
            <input type="hidden" value="{{ $partner['id'] }}" name="id" />
            @csrf

                  <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Partner Code') }}</b></label>
                    <div class="search-input">
                        <input id="partner_code"
                            value=" {{ $partner->partner_code }}"
                         type="text" class="form-control w-100 @error('partner_code') is-invalid @enderror" name="partner_code">
                        @error('partner_code')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Partner Name') }}</b></label>
                    <div class="search-input">
                        <input id="partner_name" type="text" 
                        value=" {{ $partner->partner_name }}"
                        class="form-control w-100 @error('partner_name') is-invalid @enderror" name="partner_name">
                        @error('partner_name')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Phone') }}</b></label>
                    <div class="search-input">
                        <input id="phone"
                           value=" {{ $partner->phone }}"
                         type="text" class="form-control w-100 @error('phone') is-invalid
                         @enderror" name="phone">
                        @error('phone')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

            

                 <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Address') }}</b></label>
                    <div class="search-input">
                        <input id="address" type="text"
                           value=" {{ $partner->address }}"
                         class="form-control w-100 @error('address') is-invalid
                         @enderror" name="address">
                        @error('address')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                        <div class="search-form-group">
                            <div class="search-label d-none d-sm-block"></div>
                            <div class="search-input text-center text-sm-left">
                                <input type="submit" value="{{ __('Update') }}" class="btn btn-primary" />
                            </div>
                        </div>
           
            </form>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body">
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div id="scan-modal" class="modal fade bd-example-scan-lg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <video id="video" style="border: 1px solid gray; width: 100%; height: 100%"></video>
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
            <div class="modal-footer btn-delete-partner">
                <button type="button" class="btn btn-default " data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
@endsection

