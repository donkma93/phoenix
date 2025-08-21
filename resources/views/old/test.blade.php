@extends('layouts.admin')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'home',
            'url' => '#'
        ],
        [
            'text' => 'item'
        ]
    ]
])
@endsection

@section('content')
<div class="row">
    <div class="col-3">
        <div class="card arounded-6">
            <div class="card-body text-center">
                <div class="font-32 atext-blue-500">3057</div>
                <div class="font-14 atext-gray-500 amb-16">Qty</div>
                <div class="text-uppercase d-flex align-items-center justify-content-center atext-gray-600 font-weight-medium">
                    <span class="d-inline-flex justify-content-center align-items-center amr-8 border rounded-circle abd-gray-400" style="width: 16px; height: 16px">
                        <i class="fa fa-check font-8 atext-gray-400"></i>
                    </span>
                    to be packed
                </div>
            </div>
        </div>
    </div>
    <div class="col-3">
        <div class="card arounded-6">
            <div class="card-body text-center">
                <div class="font-32 atext-red-500">43</div>
                <div class="font-14 atext-gray-500 amb-16">Pkgs</div>
                <div class="text-uppercase d-flex align-items-center justify-content-center atext-gray-600 font-weight-medium">
                    <span class="d-inline-flex justify-content-center align-items-center amr-8 border rounded-circle abd-gray-400" style="width: 16px; height: 16px">
                        <i class="fa fa-cube font-8 atext-gray-400"></i>
                    </span>
                    to be shipped
                </div>
            </div>
        </div>
    </div>
    <div class="col-3">
        <div class="card arounded-6">
            <div class="card-body text-center">
                <div class="font-32 atext-green-500">25</div>
                <div class="font-14 atext-gray-500 amb-16">Pkgs</div>
                <div class="text-uppercase d-flex align-items-center justify-content-center atext-gray-600 font-weight-medium">
                    <span class="d-inline-flex justify-content-center align-items-center amr-8 border rounded-circle abd-gray-400" style="width: 16px; height: 16px">
                        <i class="fa fa-ellipsis-h font-8 atext-gray-400"></i>
                    </span>
                    to be delivered
                </div>
            </div>
        </div>
    </div>
    <div class="col-3">
        <div class="card arounded-6">
            <div class="card-body text-center">
                <div class="font-32 atext-yellow-500">4767</div>
                <div class="font-14 atext-gray-500 amb-16">Qty</div>
                <div class="text-uppercase d-flex align-items-center justify-content-center atext-gray-600 font-weight-medium">
                    <span class="d-inline-flex justify-content-center align-items-center amr-8 border rounded-circle abd-gray-400" style="width: 16px; height: 16px">
                        <i class="fa fa-file-o font-8 atext-gray-400"></i>
                    </span>
                    to be invoiced
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
