@extends('layouts.user')

@section('breadcrumb')
    @include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('dashboard')
        ],
        [
            'text' => 'Request',
            'url' => route('requests.index')
        ],
        [
            'text' => 'Create'
        ]
    ]
])
@endsection


@section('styles')
<style>
.modal-body-scroll {
    max-height: calc(100vh - 210px);
    overflow-y: auto;
}

.img-unit {
    max-height: 250px;
    max-width: 250px;
    overflow: hidden;
    border: 1px solid black;
    margin: 10px;
}
</style>
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

@php
    $oldRequest = old('m_request_type_id');
    $hasOldRequest = isset($requestTypes[$oldRequest]);


    // $showChoicePackage = true;
    // if (!$hasOldRequest || ($hasOldRequest && !in_array($requestTypes[$oldRequest], ['relabel', 'repack', 'outbound', 'warehouse labor']))) {
    //     $showChoicePackage = false;
    // }

    // $showBarcode = true;
    // if (!$hasOldRequest || ($hasOldRequest && !in_array($requestTypes[$oldRequest], ['relabel', 'outbound', 'warehouse labor']))) {
    //     $showBarcode = false;
    // }

    // $showRadioType = true;
    // if (!$hasOldRequest || ($hasOldRequest && !in_array($requestTypes[$oldRequest], ['add package', 'removal', 'return']))) {
    //     $showRadioType = false;
    // }

    // $showAddNewPackage = true;
    // if (!$hasOldRequest || ($hasOldRequest && $requestTypes[$oldRequest] != "add package")) {
    //     $showAddNewPackage = false;
    // }

    $dataPackageGroup = $dataRemovalGroup = $dataReturnGroup = $dataUnitGroup = [];

    $u = [];
    if (old('unit_group') != null) {
        foreach (old('unit_group') as $groupId => $group) {
            if (isset($unitPackageGroups[$group['id']])) {
                $u[$groupId] = $unitPackageGroups[$group['id']]['packages'];
            }
        }
    }
@endphp



@section('content')
    <div class="fade-in">
        <div class="card">
            <div class="card-header">
               <h2 class="mb-0">Create New Request</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('requests.add_package.store') }}" class="form-horizontal" role="form" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group search-form-group">
                        <label for="m_request_type_id" class="search-label col-form-label"><b>{{ __('Type') }}</b></label>
                        <div class="search-input">
                            <select id="m_request_type_id" name="m_request_type_id" class="form-control w-75">
                                {{-- <option selected disabled></option> --}}
                                @foreach ($requestTypes as $id => $type)
                                    @if ($type == "add package")
                                        <option value="{{ $id }}"
                                            @if (old('m_request_type_id') == $id)
                                                selected="selected"
                                            @endif
                                        >{{ $type }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @if ($errors->has('m_request_type_id'))
                            <p class="text-danger mb-0">
                                {{ $errors->first('m_request_type_id') }}
                            </p>
                        @endif
                        </div>
                    </div>

                    <div class="form-group search-form-group">
                        <label for="note" class="col-form-label search-label"><b>{{ __('Note') }}</b></label>
                        <div class="search-input">
                            <textarea name="note" id="note" class="form-control">{{ old('note') }}</textarea>
                        </div>
                    </div>

                    <div id="size_radio_option">
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Size Type') }}</b></label>
                            <div class="search-input search-radio">
                                @foreach (App\Models\UserRequest::$sizeName as $value => $name)
                                    <div class="form-check d-inline-flex mr-3">
                                        <input class="form-check-input"
                                        @if (old('size_type') != null)
                                            {{ old('size_type') == $value ? 'checked' : '' }}
                                        @else
                                            {{ $value == App\Models\UserRequest::SIZE_INCH ? 'checked' : '' }}
                                        @endif
                                        type="radio" name="size_type" id="{{ 'size_type' . $value }}" value="{{ $value }}">
                                        <label class="form-check-label" for="{{ 'size_type' . $value }}">{{ $name }}</label>
                                    </div>
                                @endforeach

                                @if ($errors->has('size_type'))
                                    <p class="text-danger mb-0">
                                        {{ $errors->first('size_type') }}
                                    </p>
                                @endif
                            </div>
                        </div>


                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Weight Type') }}</b></label>
                            <div class="search-input search-radio">
                                @foreach (App\Models\UserRequest::$weightName as $value => $name)
                                    <div class="form-check d-inline-flex mr-3">
                                        <input class="form-check-input"
                                        @if (old('weight_type') != null)
                                            {{ old('weight_type') == $value ? 'checked' : '' }}
                                        @else
                                            {{ $value == App\Models\UserRequest::WEIGHT_POUND ? 'checked' : '' }}
                                        @endif
                                        type="radio" name="weight_type" id="{{ 'weight_type' . $value }}" value="{{ $value }}">
                                        <label class="form-check-label" for="{{ 'weight_type' . $value }}">{{ $name }}</label>
                                    </div>
                                @endforeach
                                @if ($errors->has('weight_type'))
                                    <p class="text-danger mb-0">
                                        {{ $errors->first('weight_type') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    @php
                        $sender = old('sender', []);
                    @endphp
                    <div id="sender">
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Sender Address') }}</b></label>
                            <div id="{{ 'ship_from_content' }}">
                                <div id="sender_content" class="row amx-n4 amb-8">
                                    <div class="col-10 col-xl-8 apx-4">
                                        <div class="row amx-n4">
                                            <div class="col-12 col-md-6 apx-4 amb-8">
                                                <label class="col-form-label search-label"><b>{{ __('Name') }}</b></label>
                                                <input type="text" class="form-control" name="sender[name]" value="{{ $sender['name'] ?? 'PHAN THI THANH HUYEN' }}" placeholder="Sender Name" />
                                                @if ($errors->has('sender.name'))
                                                    <p class="text-danger mb-0">
                                                        {{ $errors->first('sender.name') }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="col-12 col-md-6 apx-4 amb-8">
                                                <label class="col-form-label search-label"><b>{{ __('Company') }}</b></label>
                                                <input type="text" class="form-control" name="sender[company]" value="{{ $sender['company'] ?? 'Leuleullc' }}" placeholder="Sender Company" />
                                                @if ($errors->has('sender.company'))
                                                    <p class="text-danger mb-0">
                                                        {{ $errors->first('sender.company') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-10 col-xl-8 apx-4">
                                        <div class="row amx-n4">
                                            <div class="col-12 col-md-6 apx-4 amb-8">
                                                <label class="col-form-label search-label"><b>{{ __('Country') }}</b></label>
                                                <input type="text" class="form-control" name="sender[country]" value="{{ $sender['country'] ?? 'United States' }}" placeholder="Sender Country" />
                                                @if ($errors->has('sender.country'))
                                                    <p class="text-danger mb-0">
                                                        {{ $errors->first('sender.country') }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="col-12 col-md-6 apx-4 amb-8">
                                                <label class="col-form-label search-label"><b>{{ __('Street') }}</b></label>
                                                <input type="text" class="form-control" name="sender[street1]" value="{{ $sender['street1'] ?? '2248 Us Highway 9' }}" placeholder="Sender Street" />
                                                @if ($errors->has('sender.street1'))
                                                    <p class="text-danger mb-0">
                                                        {{ $errors->first('sender.street1') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-10 col-xl-8 apx-4">
                                        <div class="row amx-n4">
                                            <div class="col-12 col-md-6 apx-4 amb-8">
                                                <label class="col-form-label search-label"><b>{{ __('State') }}</b></label>
                                                <input type="text" class="form-control" name="sender[state]" value="{{ $sender['state'] ?? 'NJ' }}" placeholder="Sender State" />
                                                @if ($errors->has('sender.state'))
                                                    <p class="text-danger mb-0">
                                                        {{ $errors->first('sender.state') }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="col-12 col-md-6 apx-4 amb-8">
                                                <label class="col-form-label search-label"><b>{{ __('City') }}</b></label>
                                                <input type="text" class="form-control" name="sender[city]" value="{{ $sender['city'] ?? 'Howell' }}" placeholder="Sender City" />
                                                @if ($errors->has('sender.city'))
                                                    <p class="text-danger mb-0">
                                                        {{ $errors->first('sender.city') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-10 col-xl-8 apx-4">
                                        <div class="row amx-n4">
                                            <div class="col-12 col-md-6 apx-4 amb-8">
                                                <label class="col-form-label search-label"><b>{{ __('Zip Code') }}</b></label>
                                                <input type="text" class="form-control" name="sender[zip]" value="{{ $sender['zip'] ?? '07731' }}" placeholder="Sender Zip" />
                                                @if ($errors->has('sender.zip'))
                                                    <p class="text-danger mb-0">
                                                        {{ $errors->first('sender.zip') }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="col-12 col-md-6 apx-4 amb-8">
                                                <label class="col-form-label search-label"><b>{{ __('Phone number') }}</b></label>
                                                <input type="text" class="form-control" name="sender[phone]" value="{{ $sender['phone'] ?? '848.444.8939' }}" placeholder="Sender Phone" />
                                                @if ($errors->has('sender.phone'))
                                                    <p class="text-danger mb-0">
                                                        {{ $errors->first('sender.phone') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-10 col-xl-8 apx-4">
                                        <div class="row amx-n4">
                                            <div class="col-12 col-md-6 apx-4 amb-8">
                                                <label class="col-form-label search-label"><b>{{ __('Email') }}</b></label>
                                                <input type="text" class="form-control" name="sender[email]" value="{{ $sender['email'] ?? 'info@leuleullc.com' }}" placeholder="Sender Email" />
                                                @if ($errors->has('sender.email'))
                                                    <p class="text-danger mb-0">
                                                        {{ $errors->first('sender.email') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-10 col-xl-8 apx-4"><hr></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @php
                        $recipient = old('recipient', []);
                    @endphp
                    <div id="recipient">
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Recipient Address') }}</b></label>
                            <div id="{{ 'ship_to_content' }}">
                                <div id="recipient_content" class="row amx-n4 amb-8">
                                    <div class="col-10 col-xl-8 apx-4">
                                        <div class="row amx-n4">
                                            <div class="col-12 col-md-6 apx-4 amb-8">
                                                <label class="col-form-label search-label"><b>{{ __('Name') }}</b></label>
                                                <input type="text" class="form-control" name="recipient[name]" value="{{ $recipient['name'] ?? 'FBA: PHAN THI THANH HUYEN' }}" placeholder="Recipient Name" />
                                                @if ($errors->has('recipient.name'))
                                                    <p class="text-danger mb-0">
                                                        {{ $errors->first('recipient.name') }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="col-12 col-md-6 apx-4 amb-8">
                                                <label class="col-form-label search-label"><b>{{ __('Company') }}</b></label>
                                                <input type="text" class="form-control" name="recipient[company]" value="{{ $recipient['company'] ?? 'Amazon.com Services. Inc.' }}" placeholder="Recipient Company" />
                                                @if ($errors->has('recipient.company'))
                                                    <p class="text-danger mb-0">
                                                        {{ $errors->first('recipient.company') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-10 col-xl-8 apx-4">
                                        <div class="row amx-n4">
                                            <div class="col-12 col-md-6 apx-4 amb-8">
                                                <label class="col-form-label search-label"><b>{{ __('Country') }}</b></label>
                                                <input type="text" class="form-control" name="recipient[country]" value="{{ $recipient['country'] ?? 'United States' }}" placeholder="Recipient Country" />
                                                @if ($errors->has('recipient.country'))
                                                    <p class="text-danger mb-0">
                                                        {{ $errors->first('recipient.country') }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="col-12 col-md-6 apx-4 amb-8">
                                                <label class="col-form-label search-label"><b>{{ __('Street') }}</b></label>
                                                <input type="text" class="form-control" name="recipient[street1]" value="{{ $recipient['street1'] ?? '401 Independence Road' }}" placeholder="Recipient Street" />
                                                @if ($errors->has('recipient.street1'))
                                                    <p class="text-danger mb-0">
                                                        {{ $errors->first('recipient.street1') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-10 col-xl-8 apx-4">
                                        <div class="row amx-n4">
                                            <div class="col-12 col-md-6 apx-4 amb-8">
                                                <label class="col-form-label search-label"><b>{{ __('State') }}</b></label>
                                                <input type="text" class="form-control" name="recipient[state]" value="{{ $recipient['state'] ?? 'NJ' }}" placeholder="Recipient State" />
                                                @if ($errors->has('recipient.state'))
                                                    <p class="text-danger mb-0">
                                                        {{ $errors->first('recipient.state') }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="col-12 col-md-6 apx-4 amb-8">
                                                <label class="col-form-label search-label"><b>{{ __('City') }}</b></label>
                                                <input type="text" class="form-control" name="recipient[city]" value="{{ $recipient['city'] ?? 'Florence' }}" placeholder="Recipient City" />
                                                @if ($errors->has('recipient.city'))
                                                    <p class="text-danger mb-0">
                                                        {{ $errors->first('recipient.city') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-10 col-xl-8 apx-4">
                                        <div class="row amx-n4">
                                            <div class="col-12 col-md-6 apx-4 amb-8">
                                                <label class="col-form-label search-label"><b>{{ __('Zip Code') }}</b></label>
                                                <input type="text" class="form-control" name="recipient[zip]" value="{{ $recipient['zip'] ?? '08518-2200' }}" placeholder="Recipient Zip" />
                                                @if ($errors->has('recipient.zip'))
                                                    <p class="text-danger mb-0">
                                                        {{ $errors->first('recipient.zip') }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="col-12 col-md-6 apx-4 amb-8">
                                                <label class="col-form-label search-label"><b>{{ __('Phone number') }}</b></label>
                                                <input type="text" class="form-control" name="recipient[phone]" value="{{ $recipient['phone'] ?? '' }}" placeholder="Recipient Phone" />
                                                @if ($errors->has('recipient.phone'))
                                                    <p class="text-danger mb-0">
                                                        {{ $errors->first('recipient.phone') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-10 col-xl-8 apx-4">
                                        <div class="row amx-n4">
                                            <div class="col-12 col-md-6 apx-4 amb-8">
                                                <label class="col-form-label search-label"><b>{{ __('Email') }}</b></label>
                                                <input type="text" class="form-control" name="recipient[email]" value="{{ $recipient['email'] ?? '' }}" placeholder="Recipient Email" />
                                                @if ($errors->has('recipient.email'))
                                                    <p class="text-danger mb-0">
                                                        {{ $errors->first('recipient.email') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-10 col-xl-8 apx-4"><hr></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="extra">
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Shipment Information') }}</b></label>
                            <div class="row amx-n4 amb-8">
                                <div class="col-10 col-xl-8 apx-4">
                                    <div class="row amx-n4">
                                        <div class="col-12 col-md-6 apx-4 amb-8">
                                            <label class="col-form-label search-label"><b>{{ __('Packing Type') }}</b></label>
                                            <select id="packing_type" name="packing_type" class="form-control pg-select">

                                                @foreach (App\Models\UserRequest::$packingTypes as $key => $type)
                                                    <option value="{{ $key }}">{{ $type }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('packing_type'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('packing_type') }}
                                                </p>
                                            @endif
                                        </div>

                                        <div class="col-12 col-md-6 apx-4 amb-8">
                                            <label class="col-form-label search-label"><b>{{ __('Prep Type') }}</b></label>
                                            <select id="prep_type" name="prep_type" class="form-control pg-select">
                                                @foreach (App\Models\UserRequest::$prepTypes as $key => $type)
                                                    <option value="{{ $key }}">{{ $type }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('prep_type'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('prep_type') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-10 col-xl-8 apx-4">
                                    <div class="row amx-n4">
                                        <div class="col-12 col-md-6 apx-4 amb-8">
                                            <label class="col-form-label search-label"><b>{{ __('Who label units?') }}</b></label>
                                            <select id="label_by_type" name="label_by_type" class="form-control pg-select">
                                                @foreach (App\Models\UserRequest::$labelByTypes as $key => $type)
                                                    <option value="{{ $key }}">{{ $type }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('label_by_type'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('label_by_type') }}
                                                </p>
                                            @endif
                                        </div>
                                        <div class="col-12 col-md-6 apx-4 amb-8">
                                            <label class="col-form-label search-label"><b>{{ __('Store Type') }}</b></label>
                                            <select id="store_type" name="store_type" class="form-control pg-select">
                                                @foreach (App\Models\UserRequest::$storeTypes as $key => $type)
                                                    <option value="{{ $key }}">{{ $type }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('store_type'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('store_type') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-10 col-xl-8 apx-4">
                                    <div class="row amx-n4">
                                        <div class="col-12 col-md-6 apx-4 amb-8">
                                            <label class="col-form-label search-label"><b>{{ __('Shipment coming') }}</b></label>
                                            <input id="ship_coming" type="text" class="form-control w-100 date-picker" name="ship_coming" value="{{ date('Y-m-d', strtotime(\Carbon\Carbon::now())) }}">
                                            @if ($errors->has('ship_coming'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('ship_coming') }}
                                                </p>
                                            @endif
                                        </div>
                                        <div class="col-12 col-md-6 apx-4 amb-8">
                                            <label class="col-form-label search-label"><b>{{ __('Shipping mode') }}</b></label>
                                            <select id="ship_mode" name="ship_mode" class="form-control pg-select">
                                                @foreach (App\Models\UserRequest::$receivingShipModes as $key => $type)
                                                    <option value="{{ $key }}">{{ $type }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('ship_mode'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('ship_mode') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-10 col-xl-8 apx-4"><hr></div>
                            </div>
                        </div>
                    </div>

                    @php
                        $oldPackage = old('package');
                    @endphp
                    <div id="package">
                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Package Information') }}</b></label>
                            <div class="row amx-n4 amb-8">
                                <div class="col-10 col-xl-8 apx-4">
                                    <div class="row amx-n4">
                                        <div class="col-12 col-md-6 apx-4 amb-8">
                                            <label class="col-form-label search-label"><b>{{ __('Package Width') }}</b></label>
                                            <input type="number" class="form-control" name="package[package_width]"
                                                placeholder="Package Width" step="any" min="0"
                                                value="{{ old('package')['package_width'] ?? '' }}"
                                            />
                                            @if ($errors->has('package.package_width'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('package.package_width') }}
                                                </p>
                                            @endif
                                        </div>

                                        <div class="col-12 col-md-6 apx-4 amb-8">
                                            <label class="col-form-label search-label"><b>{{ __('Package Weight') }}</b></label>
                                            <input type="number" class="form-control" name="package[package_weight]"
                                                placeholder="Package Weight" step="any" min="0"
                                                value="{{ old('package')['package_weight'] ?? '' }}"
                                            />
                                            @if ($errors->has('package.package_weight'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('package.package_weight') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-10 col-xl-8 apx-4">
                                    <div class="row amx-n4">
                                        <div class="col-12 col-md-6 apx-4 amb-8">
                                            <label class="col-form-label search-label"><b>{{ __('Package Height') }}</b></label>
                                            <input type="number" class="form-control" name="package[package_height]"
                                                placeholder="Package Height" step="any" min="0" value="{{ old('package')['package_height'] ?? '' }}" />
                                            @if ($errors->has('package.package_height'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('package.package_height') }}
                                                </p>
                                            @endif
                                        </div>

                                        <div class="col-12 col-md-6 apx-4 amb-8">
                                            <label class="col-form-label search-label"><b>{{ __('Package Length') }}</b></label>
                                            <input type="number" class="form-control" name="package[package_length]"
                                                placeholder="Package Length" step="any" min="0" value="{{ old('package')['package_length'] ?? '' }}" />
                                            @if ($errors->has('package.package_length'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('package.package_length') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-10 col-xl-8 apx-4">
                                    <div class="row amx-n4">
                                        <div class="col-12 col-md-6 apx-4 amb-8">
                                            <label class="col-form-label search-label"><b>{{ __('Number package (*)') }}</b></label>
                                            <input type="number" class="form-control" name="package[package_number]"
                                                placeholder="Number package (*)" step="any" min="0" value="{{ old('package')['package_number'] ?? '' }}"/>
                                            @if ($errors->has('package.package_number'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('package.package_number') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-10 col-xl-8 apx-4"><hr></div>
                            </div>
                        </div>
                    </div>


                    <div id="new_package_option">
                        <div id="add_package_option_content">
                            {{-- Disable old inpit --}}
                            @if (0)

                            {{-- PG --}}
                            @if (old('package_group') != null)
                                @foreach (old('package_group') as $groupId => $group)
                                    <div class="amb-32 apy-8 addition-form" id="{{ 'package_group_'.$groupId }}">
                                        <div id="{{ 'package_group_form_'.$groupId }}">
                                            <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                                                <h3 class="amb-4">{{ __('Package Group') }}</h3>
                                                <button class="btn btn-danger btn-sm apx-16" onclick="deleteGroup(`{{ 'package_group_' . $groupId }}`)">
                                                    <i class="fa fa-trash font-14"></i>
                                                </button>
                                            </div>

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                    <select id="{{ 'package_group_select_' . $groupId }}" name="{{ 'package_group['. $groupId . '][id]' }}" class="form-control pg-select">
                                                        <option selected>{{ __('Select Package Group (*)') }}</option>
                                                        @foreach ($packageGroups as $packageGroup)
                                                            <option value="{{ $packageGroup['id'] }}"
                                                                data-width="{{ $packageGroup['unit_width'] }}" data-weight="{{ $packageGroup['unit_weight'] }}"
                                                                data-height="{{ $packageGroup['unit_height'] }}" data-length="{{ $packageGroup['unit_length'] }}"
                                                                @if (isset($group['id']) && $group['id'] == $packageGroup['id'])
                                                                    selected="selected"
                                                                    @php
                                                                        $dataPackageGroup[$groupId] = $packageGroup;
                                                                    @endphp
                                                                @endif
                                                            >{{ $packageGroup['name'] }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            @if ($errors->has('package_group.' . $groupId . '.id'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('package_group.' . $groupId . '.id') }}
                                                </p>
                                            @endif
                                            @if ($errors->has('package_group.' . $groupId . '.name'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('package_group.' . $groupId . '.name') }}
                                                </p>
                                            @endif

                                            <div id="{{ 'package_group_select_' . $groupId . '_content' }}">
                                                @if (isset($dataPackageGroup[$groupId]))
                                                    <div class="row amb-20 amx-n4">
                                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                            <b>{{ __('Package Group Unit Width') }}</b>
                                                            <div class="form-control">
                                                                {{ $dataPackageGroup[$groupId]['unit_width'] ?? '' }}
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                            <b>{{ __('Package Group Unit Weight') }}</b>
                                                            <div class="form-control">
                                                                {{ $dataPackageGroup[$groupId]['unit_weight'] ?? '' }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row amb-20 amx-n4">
                                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                            <b>{{ __('Package Group Unit Height') }}</b>
                                                            <div class="form-control">
                                                                {{ $dataPackageGroup[$groupId]['unit_height'] ?? '' }}
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                            <b>{{ __('Package Group Unit Length') }}</b>
                                                            <div class="form-control">
                                                                {{ $dataPackageGroup[$groupId]['unit_length'] ?? '' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <input type="file" accept="image/*" hidden id="{{ 'file_unit_upload_pg_' . $groupId }}" name="{{ 'package_group[' . $groupId . '][file_unit][]' }}" multiple
                                                        class="btn-primary form-control" onchange="displayFileName(`{{ 'file_unit_upload_pg_' . $groupId }}`, `{{ 'file_unit_selected_pg_' . $groupId }}`)">
                                                    <div class="btn btn-info w-100" onclick="uploadImage(`{{ 'file_unit_upload_pg_' . $groupId }}`)"> Upload unit image</div>
                                                    <span id="{{ 'file_unit_selected_pg_' . $groupId }}">No file selected</span>
                                                    <div id="{{ 'file_unit_error_pg_' . $groupId }}" class="text-danger mb-0"></div>
                                                    <div id="{{ "file_unit_selected_pg_{$groupId}_content" }}"></div>
                                                </div>
                                            </div>
                                            @if ($errors->has('package_group.' . $groupId . '.file_unit'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('package_group.' . $groupId . '.file_unit') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            {{-- New PG --}}
                            @if (old('new_package_group') != null)
                                @foreach (old('new_package_group') as $groupId => $group)
                                    <div class="amb-32 apy-8 addition-form" id="{{ 'new_package_group_'.$groupId }}">
                                        <div id="{{ 'new_package_group_form_'.$groupId }}">
                                            <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                                                <h3 class="amb-4">{{ __('New Package Group') }}</h3>
                                                <button class="btn btn-danger btn-sm apx-16" onclick="deleteGroup(`{{ 'new_package_group_' . $groupId }}`)">
                                                    <i class="fa fa-trash font-14"></i>
                                                </button>
                                            </div>

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4">
                                                    <input type="text" class="form-control" name="{{ 'new_package_group[' . $groupId . '][name]' }}"
                                                        placeholder="Package Group Name (*)" value="{{ $group['name'] ?? '' }}"
                                                    />
                                                </div>
                                            </div>
                                            @if ($errors->has('new_package_group.' . $groupId . '.name'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('new_package_group.' . $groupId . '.name') }}
                                                </p>
                                            @endif

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <b>{{ __('Package Group Unit Width') }}</b>
                                                    <input type="number" class="form-control" name="{{ 'new_package_group[' . $groupId . '][unit_width]' }}" placeholder="Package Group Unit Width" value="{{ $group['unit_width'] ?? '' }}" step="any" min="0"
                                                    />
                                                    @if ($errors->has('new_package_group.' . $groupId . '.unit_width'))
                                                        <p class="text-danger mb-0">
                                                            {{ $errors->first('new_package_group.' . $groupId . '.unit_width') }}
                                                        </p>
                                                    @endif
                                                </div>
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <b>{{ __('Package Group Unit Weight') }}</b>
                                                    <input type="number" class="form-control" name="{{ 'new_package_group[' . $groupId . '][unit_weight]' }}" placeholder="Package Group Unit Weight" value="{{ $group['unit_weight'] ?? '' }}" step="any" min="0"
                                                    />
                                                    @if ($errors->has('new_package_group.' . $groupId . '.unit_weight'))
                                                        <p class="text-danger mb-0">
                                                            {{ $errors->first('new_package_group.' . $groupId . '.unit_weight') }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <b>{{ __('Package Group Unit Height') }}</b>
                                                    <input type="number" class="form-control" name="{{ 'new_package_group[' . $groupId . '][unit_height]' }}" placeholder="Package Group Unit Height" value="{{ $group['unit_height'] ?? '' }}" step="any" min="0"
                                                    />
                                                    @if ($errors->has('new_package_group.' . $groupId . '.unit_height'))
                                                        <p class="text-danger mb-0">
                                                            {{ $errors->first('new_package_group.' . $groupId . '.unit_height') }}
                                                        </p>
                                                    @endif
                                                </div>
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <b>{{ __('Package Group Unit Length') }}</b>
                                                    <input type="number" class="form-control" name="{{ 'new_package_group[' . $groupId . '][unit_length]' }}" placeholder="Package Group Unit Length" value="{{ $group['unit_length'] ?? '' }}" step="any" min="0"
                                                    />
                                                    @if ($errors->has('new_package_group.' . $groupId . '.unit_length'))
                                                        <p class="text-danger mb-0">
                                                            {{ $errors->first('new_package_group.' . $groupId . '.unit_length') }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="amb-20">
                                                <b>{{ __('Package information') }}</b>
                                                <div id="{{ 'new_package_info_'. $groupId }}">
                                                    @if(isset($group['info']))
                                                        @foreach ($group['info'] as $k => $v)
                                                            <div id="{{ 'new_package_info_' . $groupId . '_' . $k }}" class="row amx-n4 amb-8">
                                                                <div class="col-10 col-xl-8 apx-4">
                                                                    <b>{{ __('Package') }}</b>
                                                                </div>

                                                                <div class="col-10 col-xl-8 apx-4">
                                                                    <div class="row amx-n4">
                                                                        <div class="col-12 col-md-6 apx-4 amb-8">
                                                                            <input type="number" class="form-control"
                                                                                name="{{ 'new_package_group[' . $groupId . '][info][' . $k . '][package_width]' }}"
                                                                                value="{{ $v['package_width'] ?? '' }}" placeholder="Package Width" step="any" min="0" />
                                                                        </div>
                                                                        <div class="col-12 col-md-6 apx-4 amb-8">
                                                                            <input type="number" class="form-control"
                                                                                name="{{ 'new_package_group[' . $groupId . '][info][' . $k . '][package_weight]' }}"
                                                                                value="{{ $v['package_weight'] ?? '' }}" placeholder="Package Weight" step="any" min="0" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-10 col-xl-8 apx-4">
                                                                    @if ($errors->has('new_package_group.' . $groupId . '.info.' . $k . '.package_width'))
                                                                        <p class="text-danger mb-0">
                                                                            {{ $errors->first('new_package_group.' . $groupId . '.info.' . $k . '.package_width') }}
                                                                        </p>
                                                                    @endif
                                                                    @if ($errors->has('new_package_group.' . $groupId . '.info.' . $k . '.package_weight'))
                                                                        <p class="text-danger mb-0">
                                                                            {{ $errors->first('new_package_group.' . $groupId . '.info.' . $k . '.package_weight') }}
                                                                        </p>
                                                                    @endif
                                                                </div>

                                                                <div class="col-10 col-xl-8 apx-4">
                                                                    <div class="row amx-n4">
                                                                        <div class="col-12 col-md-6 apx-4 amb-8">
                                                                            <input type="number" class="form-control"
                                                                                name="{{ 'new_package_group[' . $groupId . '][info][' . $k . '][package_height]' }}"
                                                                                value="{{ $v['package_height'] ?? '' }}" placeholder="Package Height" step="any" min="0" />
                                                                        </div>
                                                                        <div class="col-12 col-md-6 apx-4 amb-8">
                                                                            <input type="number" class="form-control"
                                                                                name="{{ 'new_package_group[' . $groupId . '][info][' . $k . '][package_length]' }}"
                                                                                value="{{ $v['package_length'] ?? '' }}" placeholder="Package Length" step="any" min="0" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-10 col-xl-8 apx-4">
                                                                    @if ($errors->has('new_package_group.' . $groupId . '.info.' . $k . '.package_height'))
                                                                        <p class="text-danger mb-0">
                                                                            {{ $errors->first('new_package_group.' . $groupId . '.info.' . $k . '.package_height') }}
                                                                        </p>
                                                                    @endif
                                                                    @if ($errors->has('new_package_group.' . $groupId . '.info.' . $k . '.package_length'))
                                                                        <p class="text-danger mb-0">
                                                                            {{ $errors->first('new_package_group.' . $groupId . '.info.' . $k . '.package_length') }}
                                                                        </p>
                                                                    @endif
                                                                </div>

                                                                <div class="col-10 col-xl-8 apx-4">
                                                                    <div class="row amx-n4">
                                                                        <div class="col-12 col-md-6 apx-4 amb-8">
                                                                            <input type="number" class="form-control"
                                                                                name="{{ 'new_package_group[' . $groupId . '][info][' . $k . '][unit_number]' }}"
                                                                                value="{{ $v['unit_number'] ?? '' }}" placeholder="Number Unit per Package (*)" step="any" min="0" />
                                                                        </div>
                                                                        <div class="col-12 col-md-6 apx-4 amb-8">
                                                                            <input type="number" class="form-control"
                                                                                name="{{ 'new_package_group[' . $groupId . '][info][' . $k . '][package_number]' }}"
                                                                                value="{{ $v['package_number'] ?? '' }}" placeholder="Number package (*)" step="any" min="0" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-10 col-xl-8 apx-4">
                                                                    @if ($errors->has('new_package_group.' . $groupId . '.info.' . $k . '.unit_number'))
                                                                        <p class="text-danger mb-0">
                                                                            {{ $errors->first('new_package_group.' . $groupId . '.info.' . $k . '.unit_number') }}
                                                                        </p>
                                                                    @endif
                                                                    @if ($errors->has('new_package_group.' . $groupId . '.info.' . $k . '.package_number'))
                                                                        <p class="text-danger mb-0">
                                                                            {{ $errors->first('new_package_group.' . $groupId . '.info.' . $k . '.package_number') }}
                                                                        </p>
                                                                    @endif
                                                                </div>

                                                                <div class="col d-flex align-items-center apx-12 amb-8">
                                                                    <i class="fa fa-close atext-gray-500 font-20 pointer line-height-1" onclick="deletePackageInfo(this)"></i>
                                                                </div>

                                                                <div  class="col-10 col-xl-8 apx-4"><hr></div>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>

                                                @if ($errors->has('new_package_group.' . $groupId . '.info'))
                                                    <p class="text-danger mb-0">
                                                        {{ $errors->first('new_package_group.' . $groupId . '.info') }}
                                                    </p>
                                                @endif
                                                <button type="button" class="btn btn-secondary apx-24 add-btn" onclick="addNewPackageInfo({{ $groupId }})">
                                                    {{ __('Add') }}
                                                </button>
                                            </div>

                                            <div class="amb-20">
                                                <b>{{ __('Tracking urls') }}</b>
                                                <div  id="{{ 'new_package_group_track_' . $groupId}}">
                                                    @if (isset($group['tracking_url']) && $group['tracking_url'] != null)
                                                        @foreach ($group['tracking_url'] as $k => $v)
                                                            <div class="row amb-8 amx-n4">
                                                                <div class="col-10 col-xl-8 apx-4">
                                                                    <input type="text" class="form-control" name="{{ 'new_package_group[' . $groupId . '][tracking_url][]' }}"
                                                                        placeholder="Tracking url" value="{{ $v ?? '' }}"
                                                                    />
                                                                </div>
                                                                <div class="d-flex align-items-center apx-12">
                                                                    <i class="fa fa-close atext-gray-500 font-20 pointer" onclick="deletePackage(this)"></i>
                                                                </div>
                                                            </div>
                                                            @if ($errors->has('new_package_group.' . $groupId . '.tracking_url.' . $k))
                                                                <p class="text-danger mb-0">
                                                                    {{ $errors->first('new_package_group.' . $groupId . '.tracking_url.' . $k) }}
                                                                </p>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </div>

                                                <button type="button" class="btn btn-secondary apx-24 add-btn" onclick="addTrack('new_package_group', {{ $groupId }})">
                                                    {{ __('Add') }}
                                                </button>
                                            </div>

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <input id="{{ 'new_package_group_file_barcode_' . $groupId }}" type="file" hidden
                                                        class="file-select"
                                                        data-id="{{ 'new_package_group_barcode_' . $groupId }}"
                                                        name="{{ 'new_package_group['. $groupId . '][file_barcode]' }}" accept="image/*,application/pdf"
                                                    >
                                                    <div id="{{ 'new_package_group_barcode_' . $groupId . '_img' }}">No file selected</div>
                                                    <div class="btn btn-info w-100" onclick="uploadBarcodeImage(`{{ 'new_package_group_file_barcode_' . $groupId }}`)">
                                                        Upload New Package Group QR code file
                                                    </div>
                                                    <div id="{{ 'new_package_group_barcode_' . $groupId . '_error' }}" class="text-danger mb-0"></div>
                                                </div>
                                            </div>

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <input id="{{ 'new_package_group_barcode_' . $groupId }}" type="text" class="form-control w-100"
                                                        name="{{ 'new_package_group['. $groupId . '][barcode]' }}" placeholder="New Package Group QR code" value="{{ $group['barcode'] ?? '' }}">
                                                    <button type="button"  class="btn scan-btn apy-4 group-start-button" data-id="{{ 'new_package_group_barcode_' . $groupId }}" data-toggle="modal" data-target="#scan-modal">
                                                        <i class="fa fa-qrcode font-20"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @if ($errors->has('new_package_group.' . $groupId . '.barcode'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('new_package_group.' . $groupId . '.barcode') }}
                                                </p>
                                            @endif

                                            <div class="row amb-20 amx-n4">
                                                <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                                                    <input type="file" accept="image/*" hidden id="{{ 'file_unit_upload_' . $groupId }}" name="{{ 'new_package_group[' . $groupId . '][file_unit][]' }}" multiple
                                                        class="btn-primary form-control" onchange="displayFileName(`{{ 'file_unit_upload_' . $groupId }}`, `{{ 'file_unit_selected_' . $groupId }}`)">
                                                    <div class="btn btn-info w-100" onclick="uploadImage(`{{ 'file_unit_upload_' . $groupId }}`)"> Upload unit image</div>
                                                    <span id="{{ 'file_unit_selected_' . $groupId }}">No file selected</span>
                                                    <div id="{{ 'file_unit_error_' . $groupId }}" class="text-danger mb-0"></div>
                                                    <div id="{{ "file_unit_selected_{$groupId}_content" }}"></div>
                                                </div>
                                            </div>
                                            @if ($errors->has('new_package_group.' . $groupId . '.file_unit'))
                                                <p class="text-danger mb-0">
                                                    {{ $errors->first('new_package_group.' . $groupId . '.file_unit') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            @endif
                        </div>

                        <div class="form-group search-form-group">
                            <label class="col-form-label search-label"><b>{{ __('Group Action') }}</b></label>
                            <div class="search-input">
                                <button type="button" class="btn btn-secondary apx-16 amr-8" onclick="addGroup2()">
                                    {{ __('Add Group') }}
                                </button>
                                <button type="button" class="btn btn-success apx-16 amr-8" onclick="addNewGroup2()">
                                    {{ __('Create New Group') }}
                                </button>
                                @if ($errors->has('package_group'))
                                    <p class="text-danger mb-0">
                                        {{ $errors->first('package_group') }}
                                    </p>
                                @endif
                                @if ($errors->has('new_package_group'))
                                    <p class="text-danger mb-0">
                                        {{ $errors->first('new_package_group') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="search-form-group">
                        <div class="search-label d-none d-sm-block"></div>
                        <div class="search-input text-center text-sm-left">
                            <button class="btn btn-primary" type="submit">{{ __('Save and wait confirm Fee') }}</button>
                        </div>
                    </div>
                </form>
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

    <!-- QR code -->
    <img id="imgshow" height="200" hidden>
@endsection

@section('scripts')
<script>
    let id = 0;
    let npgId = 0;
    let pInfo = npInfo = 0;
    let removalId = newRemovalId = 0;
    let returnId = newReturnId = 0;
    let unitId = unitInfoId = 0;
    const maxFileNumber = 3;

    function toggleOption(requestType) {
        if (requestType == 'warehouse labor') {
            $("#other_option").show();
        } else {
            $("#other_option").hide();
        }
    }

    function toggleBarcodeFile(requestType) {
        if (['relabel', 'outbound', 'warehouse labor'].includes(requestType)) {
            $("#required_file").show();
        } else {
            $("#required_file").hide();
        }
    }

    function toggleAddPackage(requestType) {
        if (requestType == 'add package') {
            $("#new_package_option").show();
        } else {
            $("#new_package_option").hide();
        }

        if (['relabel', 'repack', 'outbound', 'warehouse labor'].includes(requestType)) {
            $("#unit_package_option").show();
        } else {
            $("#unit_package_option").hide();
        }
    }

    function toggleContent() {
        $(`#note`).val('');
        $(`#add_package_option_content`).empty();
        $(`#removal_option_content`).empty();
        $(`#return_option_content`).empty();
        $(`#unit_package_option_content`).empty();
    }

    function toggleSizeRadio(requestType) {
        if (['add package', 'removal', 'return'].includes(requestType)) {
            $("#size_radio_option").show();
        } else {
            $("#size_radio_option").hide();
        }
    }

    function addGroup() {
        while ($(`#package_group_${id}`).length) {
            id += 1;
        }

        $('#add_package_option_content').append(`
            <div class="amb-32 apy-8 addition-form" id="package_group_${id}">
                <div id="package_group_form_${id}">
                    <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                        <h3 class="amb-4">{{ __('Package Group') }}</h3>
                        <button class="btn btn-danger btn-sm apx-16" onclick="deleteGroup('package_group_${id}')">
                            <i class="fa fa-trash font-14"></i>
                        </button>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <select id="package_group_select_${id}" name="package_group[${id}][id]" class="form-control pg-select">
                                <option selected>{{ __('Select Package Group (*)') }}</option>
                                @foreach ($packageGroups as $packageGroup)
                                    <option value="{{ $packageGroup['id'] }}"
                                        data-width="{{ $packageGroup['unit_width'] }}" data-weight="{{ $packageGroup['unit_weight'] }}"
                                        data-height="{{ $packageGroup['unit_height'] }}" data-length="{{ $packageGroup['unit_length'] }}"
                                    >
                                        {{ $packageGroup['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="package_group_select_${id}_content"></div>

                    <div class="amb-20">
                        <b>{{ __('Package information') }}</b>
                        <div id="package_info_${id}"></div>

                        <button type="button" class="btn btn-secondary apx-24 add-btn" onclick="addPackageInfo(${id})">
                            {{ __('Add') }}
                        </button>
                    </div>

                    <div class="amb-20">
                        <b>{{ __('Tracking urls') }}</b>
                        <div id="package_group_track_${id}"></div>

                        <button type="button" class="btn btn-secondary apx-24 add-btn" onclick="addTrack('package_group', ${id})">
                            {{ __('Add') }}
                        </button>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <input type="file" accept="image/*" hidden id="file_unit_upload_pg_${id}" name="package_group[${id}][file_unit][]" multiple
                                class="btn-primary form-control" onchange="displayFileName('file_unit_upload_pg_${id}', 'file_unit_selected_pg_${id}')">
                            <div class="btn btn-info w-100" onclick="uploadImage('file_unit_upload_pg_${id}')">Upload unit image</div>
                            <span id="file_unit_selected_pg_${id}">No file selected</span>
                            <div id="file_unit_error_pg_${id}" class="text-danger mb-0"></div>
                            <div id="file_unit_selected_pg_${id}_content"></div>
                        </div>
                    </div>
                </div>
            </div>
        `);
    }

    function addGroup2() {
        while ($(`#package_group_${id}`).length) {
            id += 1;
        }

        $('#add_package_option_content').append(`
            <div class="amb-32 apy-8 addition-form" id="package_group_${id}">
                <div id="package_group_form_${id}">
                    <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                        <h3 class="amb-4">{{ __('Package Group') }}</h3>
                        <button class="btn btn-danger btn-sm apx-16" onclick="deleteGroup('package_group_${id}')">
                            <i class="fa fa-trash font-14"></i>
                        </button>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <select id="package_group_select_${id}" name="package_group[${id}][id]" class="form-control pg-select">
                                <option selected>{{ __('Select Package Group (*)') }}</option>
                                @foreach ($packageGroups as $packageGroup)
                                    <option value="{{ $packageGroup['id'] }}"
                                        data-width="{{ $packageGroup['unit_width'] }}" data-weight="{{ $packageGroup['unit_weight'] }}"
                                        data-height="{{ $packageGroup['unit_height'] }}" data-length="{{ $packageGroup['unit_length'] }}"
                                    >
                                        {{ $packageGroup['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <input type="number" class="form-control" name="package_group[${id}][info][${pInfo}][unit_number]" placeholder="Number Unit per Package (*)" step="any" min="0" />
                        </div>
                    </div>

                    <div id="package_group_select_${id}_content"></div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <input type="file" accept="image/*" hidden id="file_unit_upload_pg_${id}" name="package_group[${id}][file_unit][]" multiple
                                class="btn-primary form-control" onchange="displayFileName('file_unit_upload_pg_${id}', 'file_unit_selected_pg_${id}')">
                            <div class="btn btn-info w-100" onclick="uploadImage('file_unit_upload_pg_${id}')">Upload unit image</div>
                            <span id="file_unit_selected_pg_${id}">No file selected</span>
                            <div id="file_unit_error_pg_${id}" class="text-danger mb-0"></div>
                            <div id="file_unit_selected_pg_${id}_content"></div>
                        </div>
                    </div>
                </div>
            </div>
        `);
    }

    function addPackageInfo(id) {
        while ($(`#package_info_${id}_${pInfo}`).length) {
            pInfo += 1;
        }

        $('#package_info_' + id).append(`
            <div id="package_info_${id}_${pInfo}" class="row amx-n4 amb-8">
                <div class="col-10 col-xl-8 apx-4">
                    <b>{{ __('Package') }}</b>
                </div>

                <div class="col-10 col-xl-8 apx-4">
                    <div class="row amx-n4">
                        <div class="col-12 col-md-6 apx-4 amb-8">
                            <input type="number" class="form-control" name="package_group[${id}][info][${pInfo}][package_width]" placeholder="Package Width" step="any" min="0" />
                        </div>
                        <div class="col-12 col-md-6 apx-4 amb-8">
                            <input type="number" class="form-control" name="package_group[${id}][info][${pInfo}][package_weight]" placeholder="Package Weight" step="any" min="0" />
                        </div>
                    </div>
                </div>

                <div class="col-10 col-xl-8 apx-4">
                    <div class="row amx-n4">
                        <div class="col-12 col-md-6 apx-4 amb-8">
                            <input type="number" class="form-control" name="package_group[${id}][info][${pInfo}][package_height]" placeholder="Package Height" step="any" min="0" />
                        </div>
                        <div class="col-12 col-md-6 apx-4 amb-8">
                            <input type="number" class="form-control" name="package_group[${id}][info][${pInfo}][package_length]" placeholder="Package Length" step="any" min="0" />
                        </div>
                    </div>
                </div>

                <div class="col-10 col-xl-8 apx-4">
                    <div class="row amx-n4">
                        <div class="col-12 col-md-6 apx-4 amb-8">
                            <input type="number" class="form-control" name="package_group[${id}][info][${pInfo}][unit_number]" placeholder="Number Unit per Package (*)" step="any" min="0" />
                        </div>
                        <div class="col-12 col-md-6 apx-4 amb-8">
                            <input type="number" class="form-control" name="package_group[${id}][info][${pInfo}][package_number]" placeholder="Number package (*)" step="any" min="0" />
                        </div>
                    </div>
                </div>

                <div class="col d-flex align-items-center apx-12 amb-8">
                    <i class="fa fa-close atext-gray-500 font-20 pointer line-height-1" onclick="deletePackageInfo(this)"></i>
                </div>

                <div  class="col-10 col-xl-8 apx-4"><hr></div>
            </div>
        `)
    }

    function addNewPackageInfo(id) {
        while ($(`#new_package_info_${id}_${npInfo}`).length) {
            npInfo += 1;
        }

        $('#new_package_info_' + id).append(`
            <div id="new_package_info_${id}_${npInfo}" class="row amx-n4 amb-8">
                <div class="col-10 col-xl-8 apx-4">
                    <b>{{ __('Package') }}</b>
                </div>

                <div class="col-10 col-xl-8 apx-4">
                    <div class="row amx-n4">
                        <div class="col-12 col-md-6 apx-4 amb-8">
                            <input type="number" class="form-control" name="new_package_group[${id}][info][${npInfo}][package_width]" placeholder="Package Width" step="any" min="0" />
                        </div>
                        <div class="col-12 col-md-6 apx-4 amb-8">
                            <input type="number" class="form-control" name="new_package_group[${id}][info][${npInfo}][package_weight]" placeholder="Package Weight" step="any" min="0" />
                        </div>
                    </div>
                </div>

                <div class="col-10 col-xl-8 apx-4">
                    <div class="row amx-n4">
                        <div class="col-12 col-md-6 apx-4 amb-8">
                            <input type="number" class="form-control" name="new_package_group[${id}][info][${npInfo}][package_height]" placeholder="Package Height" step="any" min="0" />
                        </div>
                        <div class="col-12 col-md-6 apx-4 amb-8">
                            <input type="number" class="form-control" name="new_package_group[${id}][info][${npInfo}][package_length]" placeholder="Package Length" step="any" min="0" />
                        </div>
                    </div>
                </div>

                <div class="col-10 col-xl-8 apx-4">
                    <div class="row amx-n4">
                        <div class="col-12 col-md-6 apx-4 amb-8">
                            <input type="number" class="form-control" name="new_package_group[${id}][info][${npInfo}][unit_number]" placeholder="Number Unit per Package (*)" step="any" min="0" />
                        </div>
                        <div class="col-12 col-md-6 apx-4 amb-8">
                            <input type="number" class="form-control" name="new_package_group[${id}][info][${npInfo}][package_number]" placeholder="Number package (*)" step="any" min="0" />
                        </div>
                    </div>
                </div>

                <div class="col d-flex align-items-center apx-12 amb-8">
                    <i class="fa fa-close atext-gray-500 font-20 pointer line-height-1" onclick="deletePackageInfo(this)"></i>
                </div>

                <div  class="col-10 col-xl-8 apx-4"><hr></div>
            </div>
        `)
    }

    function deletePackageInfo(e) {
        $(e).parent().parent().remove()
    }

    function deleteGroup(id) {
       $(`#${id}`).remove();
    }

    function addNewGroup() {
        while ($(`#new_package_group_${npgId}`).length) {
            npgId += 1;
        }

        $('#add_package_option_content').append(`
            <div class="amb-32 apy-8 addition-form" id="new_package_group_${npgId}">
                <div id="new_package_group_form_${npgId}">
                    <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                        <h3 class="amb-4">{{ __('New Package Group') }}</h3>
                        <button class="btn btn-danger btn-sm apx-16" onclick="deleteGroup('new_package_group_${npgId}')">
                            <i class="fa fa-trash font-14"></i>
                        </button>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <input type="text" class="form-control" name="new_package_group[${npgId}][name]"
                                placeholder="Package Group Name (*)"
                            />
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <b>{{ __('Package Group Unit Width') }}</b>
                            <input type="number" class="form-control" name="new_package_group[${npgId}][unit_width]" placeholder="Package Group Unit Width" step="any" min="0"
                            />
                        </div>
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <b>{{ __('Package Group Unit Weight') }}</b>
                            <input type="number" class="form-control" name="new_package_group[${npgId}][unit_weight]" placeholder="Package Group Unit Weight" step="any" min="0"
                            />
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <b>{{ __('Package Group Unit Height') }}</b>
                            <input type="number" class="form-control" name="new_package_group[${npgId}][unit_height]" placeholder="Package Group Unit Height" step="any" min="0"
                            />
                        </div>
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <b>{{ __('Package Group Unit Length') }}</b>
                            <input type="number" class="form-control" name="new_package_group[${npgId}][unit_length]" placeholder="Package Group Unit Length" step="any" min="0"
                            />
                        </div>
                    </div>

                    <div class="amb-20">
                        <b>{{ __('Package information') }}</b>
                        <div id="new_package_info_${npgId}"></div>

                        <button type="button" class="btn btn-secondary apx-24 add-btn" onclick="addNewPackageInfo(${npgId})">
                            {{ __('Add') }}
                        </button>
                    </div>

                    <div class="amb-20">
                        <b>{{ __('Tracking urls') }}</b>
                        <div id="new_package_group_track_${npgId}"></div>

                        <button type="button" class="btn btn-secondary apx-24 add-btn" onclick="addTrack('new_package_group', ${npgId})">
                            {{ __('Add') }}
                        </button>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <input id="new_package_group_file_barcode_${npgId}" type="file" hidden
                                class="file-select"
                                data-id="new_package_group_barcode_${npgId}"
                                name="new_package_group[${npgId}][file_barcode]" accept="image/*,application/pdf"
                            >
                            <div id="{{ 'new_package_group_barcode_${npgId}_img' }}">No file selected</div>
                            <div class="btn btn-info w-100" onclick="uploadBarcodeImage('new_package_group_file_barcode_${npgId}')">
                                Upload New Package Group QR code file
                            </div>
                            <div id="new_package_group_barcode_${npgId}_error" class="text-danger mb-0"></div>
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <input id="new_package_group_barcode_${npgId}" type="text" placeholder="New Package Group QR code"
                                class="form-control w-100" name="new_package_group[${npgId}][barcode]"
                            >
                            <button type="button"  class="btn scan-btn apy-4 group-start-button" data-id="new_package_group_barcode_${npgId}" data-toggle="modal" data-target="#scan-modal">
                                <i class="fa fa-qrcode font-20"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <input type="file" accept="image/*" hidden id="file_unit_upload_${npgId}" name="new_package_group[${npgId}][file_unit][]" multiple
                                class="btn-primary form-control" onchange="displayFileName('file_unit_upload_${npgId}', 'file_unit_selected_${npgId}')">
                            <div class="btn btn-info w-100" onclick="uploadImage('file_unit_upload_${npgId}')"> Upload unit image</div>
                            <span id="file_unit_selected_${npgId}">No file selected</span>
                            <div id="file_unit_error_${npgId}" class="text-danger mb-0"></div>
                            <div id="file_unit_selected_${npgId}_content"></div>
                        </div>
                    </div>
                </div>
            </div>
        `);
    }

    function addNewGroup2() {
        while ($(`#new_package_group_${npgId}`).length) {
            npgId += 1;
        }

        $('#add_package_option_content').append(`
            <div class="amb-32 apy-8 addition-form" id="new_package_group_${npgId}">
                <div id="new_package_group_form_${npgId}">
                    <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                        <h3 class="amb-4">{{ __('New Package Group') }}</h3>
                        <button class="btn btn-danger btn-sm apx-16" onclick="deleteGroup('new_package_group_${npgId}')">
                            <i class="fa fa-trash font-14"></i>
                        </button>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <input type="text" class="form-control" name="new_package_group[${npgId}][name]"
                                placeholder="Package Group Name (*)"
                            />
                        </div>

                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <input type="number" class="form-control" name="new_package_group[${npgId}][info][${pInfo}][unit_number]" placeholder="Number Unit per Package (*)" step="any" min="0" />
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <b>{{ __('Package Group Unit Width') }}</b>
                            <input type="number" class="form-control" name="new_package_group[${npgId}][unit_width]" placeholder="Package Group Unit Width" step="any" min="0"
                            />
                        </div>
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <b>{{ __('Package Group Unit Weight') }}</b>
                            <input type="number" class="form-control" name="new_package_group[${npgId}][unit_weight]" placeholder="Package Group Unit Weight" step="any" min="0"
                            />
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <b>{{ __('Package Group Unit Height') }}</b>
                            <input type="number" class="form-control" name="new_package_group[${npgId}][unit_height]" placeholder="Package Group Unit Height" step="any" min="0"
                            />
                        </div>
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <b>{{ __('Package Group Unit Length') }}</b>
                            <input type="number" class="form-control" name="new_package_group[${npgId}][unit_length]" placeholder="Package Group Unit Length" step="any" min="0"
                            />
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <input id="new_package_group_file_barcode_${npgId}" type="file" hidden
                                class="file-select"
                                data-id="new_package_group_barcode_${npgId}"
                                name="new_package_group[${npgId}][file_barcode]" accept="image/*,application/pdf"
                            >
                            <div id="{{ 'new_package_group_barcode_${npgId}_img' }}">No file selected</div>
                            <div class="btn btn-info w-100" onclick="uploadBarcodeImage('new_package_group_file_barcode_${npgId}')">
                                Upload New Package Group QR code file
                            </div>
                            <div id="new_package_group_barcode_${npgId}_error" class="text-danger mb-0"></div>
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <input id="new_package_group_barcode_${npgId}" type="text" placeholder="New Package Group QR code"
                                class="form-control w-100" name="new_package_group[${npgId}][barcode]"
                            >
                            <button type="button"  class="btn scan-btn apy-4 group-start-button" data-id="new_package_group_barcode_${npgId}" data-toggle="modal" data-target="#scan-modal">
                                <i class="fa fa-qrcode font-20"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <input type="file" accept="image/*" hidden id="file_unit_upload_${npgId}" name="new_package_group[${npgId}][file_unit][]" multiple
                                class="btn-primary form-control" onchange="displayFileName('file_unit_upload_${npgId}', 'file_unit_selected_${npgId}')">
                            <div class="btn btn-info w-100" onclick="uploadImage('file_unit_upload_${npgId}')"> Upload unit image</div>
                            <span id="file_unit_selected_${npgId}">No file selected</span>
                            <div id="file_unit_error_${npgId}" class="text-danger mb-0"></div>
                            <div id="file_unit_selected_${npgId}_content"></div>
                        </div>
                    </div>
                </div>
            </div>
        `);
    }

    function deletePackage(e) {
       $(e).parent().parent().remove();
    }

    function uploadImage(id) {
        $(`#${id}`).click();
    }

    function uploadBarcodeImage(targetId) {
        $(`#${targetId}`).click();
    }

    function loadFile(event, npgId, prefix = '') {
        // let Quagga = window.quagga;

        // Quagga.decodeSingle({
        //     src: URL.createObjectURL(event.target.files[0]),
        //      decoder: {
        //         readers: [
        //             { format: 'code_128_reader', config: {} },
        //             { format: 'ean_reader', config: {} },
        //             { format: 'ean_8_reader', config: {} },
        //             { format: 'codabar_reader', config: {} },
        //             { format: 'i2of5_reader', config: {} },
        //             { format: '2of5_reader', config: {} },
        //             { format: 'code_93_reader', config: {} },
        //             { format: 'code_39_reader', config: {} }
        //         ]
        //     },
        //     inputStream: {
        //         size: 1280
        //     },
        //     locate: true,
        // }, function(result) {
        //     console.log(result);
        //     if(result && result.codeResult) {
        //         $(`#${prefix}barcode_display_${npgId}`).text(result.codeResult.code);
        //         $(`#${prefix}barcode_${npgId}`).val(result.codeResult.code);
        //         $(`#${prefix}span_error_${npgId}`).text("");
        //     } else {
        //         $(`#${prefix}span_error_${npgId}`).text(" (*)Please upload other image");
        //         $(`#${prefix}barcode_display_${npgId}`).text('');
        //         $(`#${prefix}barcode_${npgId}`).val('');
        //     }
        // });
    }

    function addTrack(prefix, id) {
        $(`#${prefix}_track_${id}`).append(`
            <div class="row amb-8 amx-n4">
                <div class="col-10 col-xl-8 apx-4">
                    <input type="text" class="form-control" name="${prefix}[${id}][tracking_url][]"
                        placeholder="Tracking url"
                    />
                </div>
                <div class="d-flex align-items-center apx-12">
                    <i class="fa fa-close atext-gray-500 font-20 pointer" onclick="deletePackage(this)"></i>
                </div>
            </div>
        `);
    }

    function displayFileName(input, target) {
        const fileNumber = $(`#${input}`)[0].files.length;
        const content = `${target}_content`;
        $(`#${content}`).empty();

        if (fileNumber > maxFileNumber) {
            alert(`You can select max ${maxFileNumber} images`);
            // $(`#${input}`).val('');
            $(`#${target}`).text(`You can select max ${maxFileNumber} images`);
        } else {
            // $(`#${target}`).text($(`#${input}`)[0].files[0].name);
            $(`#${target}`).text(`You have selected ${fileNumber} images`);

            for (i = 0; i < fileNumber; i++) {
                let img = $(`
                    <img id="#${content}_${i}"
                        class="img-unit"
                    >
                `);

                img.attr('src', URL.createObjectURL($(`#${input}`)[0].files[i]));
                img.appendTo(`#${content}`);
                img.onload = function() {
                    URL.revokeObjectURL(img.src) // free memory
                }
            }
        }
    }

    function addUnitGroup() {
        while ($(`#unit_group_${unitId}`).length) {
            unitId += 1;
        }

        const requestTypeSelect = $('#m_request_type_id').children("option:selected").text();
        let contentBarcode = '';
        if (['relabel', 'outbound', 'warehouse labor'].includes(requestTypeSelect)) {
            contentBarcode = `
                <div class="row amb-20 amx-n4">
                    <div class="col-12 col-md-5 col-xl-4 apx-4">
                        <input id="unit_group_file_barcode_${unitId}" type="file" hidden
                            class="file-select"
                            data-id="unit_group_barcode_${unitId}"
                            name="unit_group[${unitId}][file_barcode]" accept="image/*,application/pdf"
                        >
                        <div id="{{ 'unit_group_barcode_${unitId}_img' }}">No file selected</div>
                        <div class="btn btn-info w-100" onclick="uploadBarcodeImage('unit_group_file_barcode_${unitId}')">
                            Upload code file
                        </div>
                        <div id="unit_group_barcode_${unitId}_error" class="text-danger mb-0"></div>
                    </div>
                </div>

                <div class="row amb-20 amx-n4">
                    <div class="col-12 col-md-5 col-xl-4 apx-4">
                        <input id="unit_group_barcode_${unitId}" type="text" placeholder="QR code"
                            class="form-control w-100" name="unit_group[${unitId}][barcode]"
                        >
                        <button type="button" class="btn scan-btn apy-4 group-start-button" data-id="unit_group_barcode_${unitId}" data-toggle="modal" data-target="#scan-modal">
                            <i class="fa fa-qrcode font-20"></i>
                        </button>
                    </div>
                </div>
            `
        }

        $('#unit_package_option_content').append(`
            <div class="amb-32 apy-8 addition-form" id="unit_group_${unitId}">
                <div id="unit_group_form_${unitId}">
                    <div class="d-flex justify-content-between align-items-center amb-12 border-bottom apb-4">
                        <h3 class="amb-4">{{ __('Package Group') }}</h3>
                        <button class="btn btn-danger btn-sm apx-16" onclick="deleteGroup('unit_group_${unitId}')">
                            <i class="fa fa-trash font-14"></i>
                        </button>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4">
                            <select id="unit_group_select_${unitId}" name="unit_group[${unitId}][id]" data-id="${unitId}" class="form-control pg-select">
                                <option selected>{{ __('Select Package Group (*)') }}</option>
                                @foreach ($unitPackageGroups as $packageGroup)
                                    <option value="{{ $packageGroup['id'] }}"
                                        data-width="{{ $packageGroup['unit_width'] }}" data-weight="{{ $packageGroup['unit_weight'] }}"
                                        data-height="{{ $packageGroup['unit_height'] }}" data-length="{{ $packageGroup['unit_length'] }}"
                                        data-packages="{{ json_encode($packageGroup['packages']) }}"
                                    >
                                        {{ $packageGroup['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="unit_group_select_${unitId}_content"></div>

                    ${contentBarcode}
                </div>
            </div>
        `);
    }

    let unitData = {};
    function initOld() {
        unitData = {!! json_encode($u) !!};
    }

    function addUnitPackageInfo(id) {
        if (unitData[id] == undefined) {
            return;
        }

        let optionContent = '';
        for(const package of unitData[id]) {
            optionContent += `
                <option value="${package['unit_number']}">Package has ${package['unit_number']} Unit (Max number package = ${package['total']})</option>
            `;
        }

        while ($(`#unit_package_info_${id}_${unitInfoId}`).length) {
            unitInfoId += 1;
        }

        $(`#unit_package_info_${id}`).append(`
            <div id="unit_package_info_${id}_${unitInfoId}">
                <div class="row amb-20 amx-n4">
                    <div class="col-12 col-md-5 col-xl-4 apx-4">
                        <select id="unit_group_select_${id}" name="unit_group[${id}][info][${unitInfoId}][unit_number]" class="form-control">
                            <option value="-1">{{ __('Select Package (*)') }}</option>
                            ${optionContent}
                        </select>
                    </div>

                    <div class="col-12 col-md-5 col-xl-4 apx-4">
                        <input type="number" class="form-control" name="unit_group[${id}][info][${unitInfoId}][package_number]"
                            placeholder="Number Package (*)" step="any" min="1"
                        />
                    </div>

                    <div class="d-flex align-items-center apx-12">
                        <i class="fa fa-close atext-gray-500 font-20 pointer" onclick="deleteUnitPackageInfo(this)"></i>
                    </div>
                </div>
            </div>
        `);
    }

    function deleteUnitPackageInfo(e) {
       $(e).parent().parent().parent().remove();
    }

    let requestTypeSelect;

    $(document).ready(function () {
        initOld();

        $('#packing_type').on('change', function() {
            const id = pInfo = 0;
            const val = $(this).children("option:selected").val();
            $('#add_package_option_content').empty();

            let packageNumberInfo =
            `
                <div class="col-10 col-xl-8 apx-4">
                    <div class="row amx-n4">
                        <div class="col-12 col-md-6 apx-4 amb-8">
                            <input type="number" class="form-control" name="package[package_number]" placeholder="Number package (*)" step="any" min="0" />
                        </div>
                    </div>
                </div>
            `;

            const packageInfo = `
                <div id="package_info_${id}_${pInfo}_a" class="col-10 col-xl-8 apx-4">
                    <div class="row amx-n4">
                        <div class="col-12 col-md-6 apx-4 amb-8">
                            <input type="number" class="form-control" name="package[package_width]" placeholder="Package Width" step="any" min="0" />
                        </div>
                        <div class="col-12 col-md-6 apx-4 amb-8">
                            <input type="number" class="form-control" name="package[package_weight]" placeholder="Package Weight" step="any" min="0" />
                        </div>
                    </div>
                </div>

                <div class="col-10 col-xl-8 apx-4">
                    <div class="row amx-n4">
                        <div class="col-12 col-md-6 apx-4 amb-8">
                            <input type="number" class="form-control" name="package[package_height]" placeholder="Package Height" step="any" min="0" />
                        </div>
                        <div class="col-12 col-md-6 apx-4 amb-8">
                            <input type="number" class="form-control" name="package[package_length]" placeholder="Package Length" step="any" min="0" />
                        </div>
                    </div>
                </div>

                ${packageNumberInfo}
            `;

            let package = `
                <div id="package_info_${id}_${pInfo}" class="row amx-n4 amb-8">
                    <div class="col-10 col-xl-8 apx-4">
                        <b>{{ __('Package') }}</b>
                    </div>

                    ${packageInfo}

                    <div class="col-10 col-xl-8 apx-4"><hr></div>
                </div>
            `;

            // $(`#add_package_option_content`).append(package);
        });

        $('#m_request_type_id').on('change', function() {
            requestTypeSelect = $(this).children("option:selected").text();
            $requestType = $(this).children("option:selected").text();
            // toggleOption($requestType);
            // toggleBarcodeFile($requestType);
            // toggleAddPackage($requestType);

            // toggleSizeRadio($requestType);
            // toggleContent();
        });

        $(document).on('change', '.pg-select', function() {
            const id = $(this).attr('id');
            const data = $(this).children("option:selected").data();

            if (Object.keys(data).length) {
                const unitGroupId = $(this).data('id');
                unitData[unitGroupId] = data['packages'];

                let packageContent = '';
                if (id.startsWith('unit_group_select')) {
                    packageContent = `
                        <div class="amb-20">
                            <b>{{ __('Stored package information') }}</b>
                            <div id="unit_package_info_${unitGroupId}"></div>

                            <button type="button" class="btn btn-secondary apx-24 add-btn" onclick="addUnitPackageInfo('${unitGroupId}')">
                                {{ __('Add') }}
                            </button>
                        </div>
                    `;
                }

                $(`#${id}_content`).html(`
                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <b>{{ __('Package Group Unit Width') }}</b>
                            <div class="form-control">
                                ${data['width'] ? data['width'] : '' }
                            </div>
                        </div>
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <b>{{ __('Package Group Unit Weight') }}</b>
                            <div class="form-control">
                                ${data['weight'] ? data['weight'] : '' }
                            </div>
                        </div>
                    </div>

                    <div class="row amb-20 amx-n4">
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <b>{{ __('Package Group Unit Height') }}</b>
                            <div class="form-control">
                                ${data['height'] ? data['height'] : '' }
                            </div>
                        </div>
                        <div class="col-12 col-md-5 col-xl-4 apx-4 amb-8">
                            <b>{{ __('Package Group Unit Length') }}</b>
                            <div class="form-control">
                                ${data['length'] ? data['length'] : '' }
                            </div>
                        </div>
                    </div>

                    ${packageContent}
                `);
            } else {
                $(`#${id}_content`).empty();
            }
        });

        $('#file_code_btn').click(function() {
            $('#file_code').click()
        });

        $('#file_code').change(function() {
            $('#selected_filename').text($('#file_code')[0].files[0].name);
        });

        window.addEventListener('load', function () {
            try {
                let selectedDeviceId;
                const codeReader = new window.zxing.BrowserMultiFormatReader();
                codeReader.getVideoInputDevices()
                .then((videoInputDevices) => {
                    if (videoInputDevices.length < 1) {
                        console.log('No video devices found');
                        return;
                    }
                    selectedDeviceId = videoInputDevices[0].deviceId;
                    $('#scan-modal').on('hidden.coreui.modal', function (e) {
                        codeReader.reset();
                    })

                    $(document).on('click', '.group-start-button', function() {
                        let targetId = $(this).data('id');
                        codeReader.decodeFromVideoDevice(selectedDeviceId, 'video', (result, err) => {
                            if (result) {
                                $(`#${targetId}`).val(result.text);
                                $('#scan-modal').modal('hide');
                                codeReader.reset();
                            }
                            if (err && !(err instanceof window.zxing.NotFoundException)) {
                                console.log(err);
                                $('#scan-modal').modal('hide');
                                codeReader.reset();
                            }
                        })
                    });
                }).catch((err) => { console.log(err)});

                const qrCodeReader = new window.zxing.BrowserQRCodeReader();
                $(document).on('change', '.file-select', function() {
                    const targetId = $(this).data('id');
                    $(`#${targetId}`).val('');

                    if (this.files && this.files[0]) {
                        $(`#${targetId}_img`).text(this.files[0].name);
                        var reader = new FileReader();
                        const img = document.getElementById('imgshow');

                        reader.onload = function (e) {
                            img.src = e.target.result;
                        }
                        reader.readAsDataURL(this.files[0]);


                        qrCodeReader.decodeFromImage(img).then((result) => {
                            $(`#${targetId}`).val(result.text);
                            console.log(result.text);
                        }).catch((err) => {
                            $(`#${targetId}_error`).val(' (*)Please upload other image');
                            console.error(err)
                        });
                    } else {
                        $(`#${targetId}_img`).text('No file selected');
                    }
                });

            } catch(err){
                console.log(err)
            }
        })
    });
</script>
@endsection
