<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Invoice</title>

    <style>
        html, body {
            padding: 0;
            margin: 0;
        }
        * {
            box-sizing: border-box
        }
        .invoice-wrapper {
            margin: auto;
            width: 624px;
        }
        .invoice-divider {
            display: block;
            height: 22px;
            background: black;
        }
        .invoice-info-table {
            border-collapse: collapse;
            width: 100%;
        }
        .invoice-info-table tr.odd {
            background-color: #d9d9d9;
        }
        .invoice-info-table tr th,
        .invoice-info-table tr td {
            vertical-align: top;
            border: 1px solid black;
            padding: 0 6px;
            text-align: center;
        }
        .invoice-info-table tr th {
            height: 22px;
            line-height: 22px;
            font-weight: 500;
            color: white;
            background-color: black;
        }
        .text-left {
            text-align: left !important;
        }
        .abg-white {
            background: white;
        }
        .abg-black {
            background: black;
        }
        .atext-black {
            color: black;
        }
        .atext-white {
            color: white;
        }
        .font-14 {
            font-size: 14px;
            line-height: 22px;
        }
        .font-30 {
            font-size: 30px;
        }
        .font-48 {
            font-size: 48px;
            line-height: 110px;
        }
        .flex-center {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .justify-content-between {
            justify-content: space-between;
        }
        .text-uppercase {
            text-transform: uppercase;
        }
        .text-center {
            text-align: center
        }
        .fw-medium {
            font-weight: 700;
        }
        .apx-8 {
            padding-right: 8px;
            padding-left: 8px;
        }
        .apx-24 {
            padding-right: 24px;
            padding-left: 24px;
        }
        .apy-32 {
            padding-top: 32px;
            padding-bottom: 32px;
        }
        .amy-32 {
            margin-top: 32px;
            margin-bottom: 32px;
        }
        .amb-12 {
            margin-bottom: 12px;
        }
        .amb-32 {
            margin-bottom: 32px;
        }
        .amb-48 {
            margin-bottom: 48px;
        }
        .amt-32 {
            margin-top: 32px;
        }
        .m-0 {
            margin: 0;
        }
        .col-7 {
            width: 60%;
            float: left;
        }
        .col-5 {
            width: 40%;
            float: left;
        }
        .col-4 {
            max-width: 40%;
            float: right;
        }
        .border {
            border: 1px solid
        }
        .border-black {
            border-color: black;
        }
        .w-100 {
            width: 100%
        }
        .logo {
            float: right;
        }
        .d-inline-block {
            display: inline-block;
        }
    </style>
</head>

@php
    $columns = [__('User'), __('Type'), __('Status'), __('Group Name'), __('Total'), __('Price'), __('Total Price')];

    $storeColumns = [__('User'), __('Type'), __('Name'), __('Total Package'), __('Cuft'), __('Price'), __('Total Cuft')];
@endphp

<body class="abg-white atext-black">
    <div class="invoice-wrapper font-14">
        <h1 class="font-48 text-uppercase apx-8 d-inline-block m-0">invoice {{ $targetYear }}-{{ $targetMonth }}</h1>
        <img src="" width="177" height="110" alt="" class="logo">
        <div class="amy-32 abg-black atext-white text-uppercase apx-8 fw-medium">
            <div class="col-7">
                invoice no:
            </div>
            <div class="col-5">
                invoice date: {{ $invoiceDate }}
            </div>
            <div style="clear: both;"></div>
        </div>
        <div class="apx-24">
            <h2 class="font-14 text-uppercase amb-12 fw-medium">leuleu company</h2>
            <p class="amb-12">2248 Us Highway 9, Howell, NJ 07731</p>
            <p class="amb-12">Website</p>
            <p class="amb-12">Phone number:</p>
        </div>
        <div class="invoice-divider"></div>
        <div class="amt-32 apx-24">
            <h2 class="font-14 text-uppercase amb-12 fw-medium">Company: {{ $user->profile->company_name ?? '' }}</h2>
            <p class="amb-12">Address: {{ $user->addresses[0]->building ?? '' }}</p>
            <p class="amb-12">Phone number: {{ $user->profile->phone ?? '' }}</p>
        </div>

        @if (count($inboundResult))
            <table class="invoice-info-table table table-bordered table-striped amb-32">
                <thead>
                    <tr>
                        @foreach ($columns as $column)
                            <th>{{ $column }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inboundResult as $index => $data)
                        <tr>
                            <td>{{ $index ? '' : $user->email }}</td>
                            <td>{{ $index ? '' : 'Inbound' }} </td>
                            <td>{{ $index ? '' : 'Done' }} </td>
                            <td>{{ $data['group_name'] }}</td>
                            <td>{{ $data['total'] }}</td>
                            <td>${{ $data['price'] }}</td>
                            <td class="justify-content-between">
                                <span>$</span>
                                <span>{{ $data['total_price'] }}</span>
                            </td>
                        </tr>
                    @endforeach

                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ $numberInbound }}</td>
                        <td></td>
                        <td class="justify-content-between">
                            <span>$</span>
                            <span>{{ $totalPriceInbound }}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
            <br>
        @endif

        @if (count($outboundResult))
            <table class="invoice-info-table table table-bordered table-striped amb-32">
                <thead>
                    <tr>
                        @foreach ($columns as $column)
                            <th>{{ $column }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($outboundResult as $data)
                        <tr>
                            <td>{{ $loop->first ? $user->email : '' }}</td>
                            <td>{{ $loop->first ? 'Outbound' : '' }}</td>
                            <td>{{ $loop->first ? 'Done' : '' }} </td>
                            <td>{{ $data['group_name'] }}</td>
                            <td>{{ $data['total'] }}</td>
                            <td>${{ $data['price'] }}</td>
                            <td class="justify-content-between">
                                <span>$</span>
                                <span>{{ $data['total_price'] }}</span>
                            </td>
                        </tr>
                    @endforeach

                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ $numberOutbound }}</td>
                        <td></td>
                        <td class="justify-content-between">
                            <span>$</span>
                            <span>{{ $totalPriceOutboundOrigin }}</span>
                        </td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>{{ 'Minimum Fee Outbound $40/month' }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="justify-content-between">
                            <span>$</span>
                            <span>{{ $totalPriceOutboundBuffer }}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
            <br>
        @endif

        @if ($numberLaborHour + $numberLaborUnit)
            <table class="invoice-info-table table table-bordered table-striped amb-32">
                <thead>
                    <tr>
                        @foreach ($columns as $column)
                            <th>{{ $column }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @if ($numberLaborHour)
                        <tr>
                            <td>{{ $user->email }} </td>
                            <td>{{ 'Warehouse Labor - Hour' }} </td>
                            <td>{{ 'Done' }} </td>
                            <td></td>
                            <td>{{ $numberLaborHour }}</td>
                            <td>${{ $priceLaborHour }}</td>
                            <td class="justify-content-between">
                                <span>$</span>
                                <span>{{ $totalPriceLaborHour }}</span>
                            </td>
                        </tr>
                    @endif

                    @if ($numberLaborUnit)
                        @foreach ($laborResult as $data)
                            <tr>
                                <td>{{ $loop->first ? $user->email : '' }} </td>
                                <td>{{ $loop->first ? 'Warehouse Labor - Unit' : '' }} </td>
                                <td>{{ $loop->first ? 'Done' : '' }} </td>
                                <td></td>
                                <td>{{ $data['total'] }}</td>
                                <td></td>
                                <td class="justify-content-between"></td>
                            </tr>
                        @endforeach

                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{ $numberLaborUnit }}</td>
                            <td>${{ $priceLaborUnit ?? 0 }}</td>
                            <td class="justify-content-between">
                                <span>$</span>
                                <span>{{ $totalPriceLaborUnit }}</span>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
            <br>
        @endif

        @if ($numberRelabel)
            <table class="invoice-info-table table table-bordered table-striped amb-32">
                <thead>
                    <tr>
                        @foreach ($columns as $column)
                            <th>{{ $column }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @if (count($relabelMaxResult))
                        @foreach ($relabelMaxResult as $data)
                            <tr>
                                <td>{{ $loop->first ? $user->email : '' }} </td>
                                <td>{{ $loop->first ? 'Relabel Oversize' : '' }} </td>
                                <td>{{ $loop->first ? 'Done' : '' }} </td>
                                <td>{{ $data['group_name'] }}</td>
                                <td>{{ $data['total'] }}</td>
                                <td></td>
                                <td class="justify-content-between"></td>
                            </tr>
                        @endforeach

                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{ $numberRelabelMaxSize }}</td>
                            <td>${{ $priceRelabelMaxSize }}</td>
                            <td class="justify-content-between">
                                <span>$</span>
                                <span>{{ $totalPriceRelabelMaxSize }}</span>
                            </td>
                        </tr>
                    @endif

                    @if (count($relabelMinResult))
                        @foreach ($relabelMinResult as $data)
                            <tr>
                                <td>{{ $loop->first ? $user->email : '' }} </td>
                                <td>{{ $loop->first ? 'Relabel' : '' }} </td>
                                <td>{{ $loop->first ? 'Done' : '' }} </td>
                                <td>{{ $data['group_name'] }}</td>
                                <td>{{ $data['total'] }}</td>
                                <td></td>
                                <td class="justify-content-between"></td>
                            </tr>
                        @endforeach

                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{ $numberRelabelMinSize }}</td>
                            <td>${{ $priceRelabelMinSize }}</td>
                            <td class="justify-content-between">
                                <span>$</span>
                                <span>{{ $totalPriceRelabelMinSize }}</span>
                            </td>
                        </tr>
                    @endif

                    @if ($numberRelabelBuffer)
                        <tr>
                            <td></td>
                            <td>{{ __('Minimum Relabel 100 Units/month') }}</td>
                            <td></td>
                            <td></td>
                            <td>{{ $numberRelabelBuffer }}</td>
                            <td>${{ $priceRelabelMinSize }}</td>
                            <td class="justify-content-between">
                                <span>$</span>
                                <span>{{ $totalPriceRelabelBuffer }}</span>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
            <br>
        @endif

        @if ($numberReturn)
            <table class="invoice-info-table table table-bordered table-striped amb-32">
                <thead>
                    <tr>
                        @foreach ($columns as $column)
                            <th>{{ $column }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($returnResult as $data)
                        <tr>
                            <td>{{ $loop->first ? $user->email : '' }} </td>
                            <td>{{ $loop->first ? 'Return' : '' }} </td>
                            <td>{{ $loop->first ? 'Done' : '' }} </td>
                            <td>{{ $data['group_name'] }}</td>
                            <td>{{ $data['total'] }}</td>
                            <td></td>
                            <td class="justify-content-between"></td>
                        </tr>
                    @endforeach

                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ $numberReturn }}</td>
                        <td>${{ $priceReturn }}</td>
                        <td class="justify-content-between">
                            <span>$</span>
                            <span>{{ $totalPriceReturn }}</span>
                        </td>
                    </tr>

                    @if ($numberReturnBuffer)
                        <tr>
                            <td></td>
                            <td>{{ __('Minimum Return 50 Units/month') }}</td>
                            <td></td>
                            <td></td>
                            <td>{{ $numberReturnBuffer }}</td>
                            <td>${{ $priceReturn }}</td>
                            <td class="justify-content-between">
                                <span>$</span>
                                <span>{{ $totalPriceReturnBuffer }}</span>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
            <br>
        @endif

        @if ($numberRepack)
            <table class="invoice-info-table table table-bordered table-striped amb-32">
                <thead>
                    <tr>
                        @foreach ($columns as $column)
                            <th>{{ $column }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($repackResult as $data)
                        <tr>
                            <td>{{ $loop->first ? $user->email : '' }} </td>
                            <td>{{ $loop->first ? 'Repack' : '' }} </td>
                            <td>{{ $loop->first ? 'Done' : '' }} </td>
                            <td>{{ $data['group_name'] }}</td>
                            <td>{{ $data['total'] }}</td>
                            <td></td>
                            <td class="justify-content-between"></td>
                        </tr>
                    @endforeach

                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ $numberRepack }}</td>
                        <td>${{ $priceRepack }}</td>
                        <td class="justify-content-between">
                            <span>$</span>
                            <span>{{ $totalPriceRepack }}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
            <br>
        @endif

        @if ($numberRemoval)
            <table class="invoice-info-table table table-bordered table-striped amb-32">
                <thead>
                    <tr>
                        @foreach ($columns as $column)
                            <th>{{ $column }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @if (count($removalMaxResult))
                        @foreach ($removalMaxResult as $data)
                            <tr>
                                <td>{{ $loop->first ? $user->email : '' }} </td>
                                <td>{{ $loop->first ? 'Removal Oversize' : '' }} </td>
                                <td>{{ $loop->first ? 'Done' : '' }} </td>
                                <td>{{ $data['group_name'] }}</td>
                                <td>{{ $data['total'] }}</td>
                                <td></td>
                                <td class="justify-content-between"></td>
                            </tr>
                        @endforeach

                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{ $numberRemovalMaxSize }}</td>
                            <td>${{ $priceRemovalMaxSize }}</td>
                            <td class="justify-content-between">
                                <span>$</span>
                                <span>{{ $totalPriceRemovalMaxSize }}</span>
                            </td>
                        </tr>
                    @endif

                    @if (count($removalMinResult))
                        @foreach ($removalMinResult as $data)
                            <tr>
                                <td>{{ $loop->first ? $user->email : '' }} </td>
                                <td>{{ $loop->first ? 'Removal' : '' }} </td>
                                <td>{{ $loop->first ? 'Done' : '' }} </td>
                                <td>{{ $data['group_name'] }}</td>
                                <td>{{ $data['total'] }}</td>
                                <td></td>
                                <td class="justify-content-between"></td>
                            </tr>
                        @endforeach

                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{ $numberRemovalMinSize }}</td>
                            <td>${{ $priceRemovalMinSize }}</td>
                            <td class="justify-content-between">
                                <span>$</span>
                                <span>{{ $totalPriceRemovalMinSize }}</span>
                            </td>
                        </tr>
                    @endif

                    @if ($numberRemovalBuffer)
                        <tr>
                            <td></td>
                            <td>{{ __('Minimum Removal 100 Units/month') }}</td>
                            <td></td>
                            <td></td>
                            <td>{{ $numberRemovalBuffer }}</td>
                            <td>${{ $priceRemovalMinSize }}</td>
                            <td class="justify-content-between">
                                <span>$</span>
                                <span>{{ $totalPriceRemovalBuffer }}</span>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
            <br>
        @endif

        @if ($numberStored)
            <table class="invoice-info-table table table-bordered table-striped amb-32">
                <thead>
                    <tr>
                        @foreach ($storeColumns as $column)
                            <th>{{ $column }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($storedResult as $month => $dataByMonth)
                        @if(count($dataByMonth))
                            @foreach ($dataByMonth as $dataByCuft)
                                @php
                                    $isDataMonthFirst = $loop->first;
                                @endphp
                                @foreach ($dataByCuft as $data)
                                    <tr>
                                        <td>{{ $isDataMonthFirst && $loop->first ? $user->email : '' }} </td>
                                        <td>{{ $isDataMonthFirst && $loop->first ? 'Stored >= ' . $month . ' month' : '' }} </td>
                                        <td>{{ $data['name'] }} </td>
                                        <td>{{ $data['total'] }} </td>
                                        <td>{{ $data['cuft'] }} </td>
                                        <td>{{ $data['price'] }}</td>
                                        <td class="justify-content-between">
                                            <span>$</span>
                                            <span>{{ $data['total_price'] }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach

                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>{{ $totalStoredSummary[$month] }}</td>
                                <td></td>
                                <td></td>
                                <td class="justify-content-between">
                                    <span>$</span>
                                    <span>{{ $priceStoredSummary[$month] }}</span>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
            <br>
        @endif

        {{-- stored --}}

        {{-- <table class="invoice-info-table table table-bordered table-striped amb-32">
            <colgroup>
                <col width="25%">
                <col width="25%">
                <col width="25%">
                <col width="25%">
            </colgroup>
            <thead>
                <tr>
                    <th>SERVICE TYPE</th>
                    <th>QUANTITY</th>
                    <th>UNIT PRICE</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($priceStoredSummary as $month => $info)
                    <tr>
                        <td class="text-left">Storage >= {{ $month }} Months</td>
                        <td>{{ $info['cuft'] }} Cuft</td>
                        <td>{{ $info['price'] }}/Cuft</td>
                        <td>${{ $info['total'] }}</td>
                    </tr>
                @endforeach

                <tr>
                    <td class="text-left">Inbound</td>
                    <td>{{ $numberInbound }} Package</td>
                    <td>${{ $priceInbound }}/Package</td>
                    <td>${{ $totalPriceInbound}}</td>
                </tr>

                <tr>
                    <td class="text-left">Outbound</td>
                    <td>{{ $numberOutbound }} Package</td>
                    <td>${{ $priceOutbound }}/Package</td>
                    <td>${{ $totalPriceOutbound }}</td>
                </tr>

                <tr>
                    <td class="text-left">Warehouse Labor</td>
                    <td>{{ $numberLabor }} Hour</td>
                    <td>${{ $priceLabor }}/Hour</td>
                    <td>${{ $totalPriceLabor }}</td>
                </tr>

                <tr>
                    <td class="text-left">Relabel</td>
                    <td>{{ $numberRelabelMinSize }} Unit</td>
                    <td>${{ $priceRelabelMinSize }}/Unit</td>
                    <td>${{ $totalPriceRelabelMinSize }}</td>
                </tr>

                <tr>
                    <td class="text-left">Relabel Oversize</td>
                    <td>{{ $numberRelabelMaxSize }} Unit</td>
                    <td>${{ $priceRelabelMaxSize }}/Unit</td>
                    <td>${{ $totalPriceRelabelMaxSize }}</td>
                </tr>

                <tr>
                    <td class="text-left">Return</td>
                    <td>{{ $numberReturn }} Unit</td>
                    <td>${{ $priceReturn }}/Unit</td>
                    <td>${{ $totalPriceReturn }}</td>
                </tr>

                <tr>
                    <td class="text-left">Repacking</td>
                    <td>{{ $numberRepack }} Unit</td>
                    <td>${{ $priceRepack }}/Unit</td>
                    <td>${{ $totalPriceRepack }}</td>
                </tr>

                <tr>
                    <td class="text-left">Removal</td>
                    <td>{{ $numberRemovalMinSize }} Unit</td>
                    <td>${{ $priceRemovalMinSize }}/Unit</td>
                    <td>${{ $totalPriceRemovalMinSize }}</td>
                </tr>

                <tr>
                    <td class="text-left">Removal Oversize</td>
                    <td>{{ $numberRemovalMaxSize }} Unit</td>
                    <td>${{ $priceRemovalMaxSize }}/Unit</td>
                    <td>${{ $totalPriceRemovalMaxSize }}</td>
                </tr>
            </tbody>
        </table> --}}
        <div class="amb-48">
            <div class="col-5">
                <div class="invoice-divider"></div>
                <div class="border border-black apy-32"></div>
            </div>
            <div class="col-4">
                <table class="table-borderless text-uppercase w-100">
                    <tbody>
                        <tr>
                            <td>subtotal</td>
                            <td class="text-center">${{ $subTotal }}</td>
                        </tr>
                        <tr>
                            <td>tax (...%)</td>
                            <td class="text-center">{{ $tax }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="text-center">___________</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">balance</td>
                            <td class="text-center">${{ $balance }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div style="clear: both"></div>
        </div>
        <div class="text-center font-30 amb-12">Thank you for your business.</div>
    </div>
</body>
</html>
