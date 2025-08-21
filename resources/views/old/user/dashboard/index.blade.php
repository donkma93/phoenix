@extends('layouts.user')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard'
        ]
    ]
])
@endsection


@section('styles')
<style>
    #chartdiv {
      width: 100%;
      height: 500px
    }

    #prices {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #prices td, #prices th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #prices tr:nth-child(even){background-color: #f2f2f2;}

    #prices tr:hover {background-color: #ddd;}

    #prices th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #04AA6D;
        color: white;
    }

    #prices tr td:nth-child(3) {
        text-align: right;
    }
</style>
@endsection

@php
$orderStates = [
  [ 'id' => "US-AL", 'value' => 4447100, 'test' => 123 ],
  [ 'id' => "US-AK", 'value' => 626932 ],
];
@endphp

@section('content')
@if(count($remind))
<div class="px-4 px-md-0">
    <h2>{{ __('Reorder remind') }}</h2>
    <hr>

    <div class="card-footer">
        @if (count($remind) == 0)
            <div class="text-center">{{ __('No data.') }}</div>
        @else
            <div class="table-responsive">
                <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-inventory-list-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>{{ __('Product') }}</th>
                            <th>{{ __('Sku') }}</th>
                            <th>{{ __('Incoming') }}</th>
                            <th>{{ __('Available') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($remind as $inventory)
                        <tr>
                            <td>{{ ($remind->currentPage() - 1) * $remind->perPage() + $loop->iteration }}</td>
                            <td>{{ $inventory->product->name }}</td>
                            <td>{{ $inventory->sku }}</td>
                            <td>{{ $inventory->incoming }}</td>
                            <td>{{ $inventory->available }}</td>
                            {{-- <td style="text-align: center">{{ $inventory->incoming }}</td> --}}
                            <td>
                                <a class="btn btn-info" href="{{ route('inventories.show', ['id' => $inventory->id]) }}">Detail</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center justify-content-md-end amt-16">
                {{ $remind->appends(request()->all())->links('components.pagination') }}
            </div>
        @endif
    </div>
</div>
<br><br>
@endif

<div class="px-4 px-md-0">
    <h2>{{ __('Package Summary') }}</h2>
    <hr>

    <h3>{{ __('Total') }}</h3>
    <div class="row justify-content-center justify-content-md-between">
        <div class="col-sm-6 col-md-4 amb-24">
            <div class="card arounded-8 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">
                        <div class="atext-gray-500 text-uppercase">{{ __('Package Inbound') }}</div>
                        <div class="font-32 text-primary">
                            {{ $packageHistoryCount[App\Models\Package::STATUS_INBOUND] ?? 0 }}
                        </div>
                    </div>
                    <div class="card-icon flex-center rounded-circle bg-primary">
                        <i class="fa fa-cube font-24 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 amb-24">
            <div class="card arounded-8 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">
                        <div class="atext-gray-500 text-uppercase">{{ __('Package Stored') }}</div>
                        <div class="font-32 text-danger">
                            {{ $packageStoredCount }}
                        </div>
                    </div>
                    <div class="card-icon flex-center rounded-circle bg-danger">
                        <i class="fa fa-cube font-24 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 amb-24">
            <div class="card arounded-8 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">
                        <div class="atext-gray-500 text-uppercase">{{ __('Package Outbound') }}</div>
                        <div class="font-32 text-primary">
                            {{ $packageHistoryCount[App\Models\Package::STATUS_OUTBOUND] ?? 0 }}
                        </div>
                    </div>
                    <div class="card-icon flex-center rounded-circle bg-primary">
                        <i class="fa fa-cube font-24 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h3>{{ __('This Month') }}</h3>
    <div class="row justify-content-center">
        <div class="col-sm-6 col-md-4 amb-24">
            <div class="card arounded-8 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">
                        <div class="atext-gray-500 text-uppercase">{{ __('Package Inbound') }}</div>
                        <div class="font-32 text-primary">
                            {{ $packageHistoryCurrentMonthCount[App\Models\Package::STATUS_INBOUND] ?? 0 }}
                        </div>
                    </div>
                    <div class="card-icon flex-center rounded-circle bg-primary">
                        <i class="fa fa-cube font-24 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 amb-24">
            <div class="card arounded-8 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">
                        <div class="atext-gray-500 text-uppercase">{{ __('Package Stored') }}</div>
                        <div class="font-32 text-danger">
                            {{ $packageStoredCurrentMonthCount }}
                        </div>
                    </div>
                    <div class="card-icon flex-center rounded-circle bg-danger">
                        <i class="fa fa-cube font-24 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 amb-24">
            <div class="card arounded-8 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">
                        <div class="atext-gray-500 text-uppercase">{{ __('Package Outbound') }}</div>
                        <div class="font-32 text-primary">
                            {{ $packageHistoryCurrentMonthCount[App\Models\Package::STATUS_OUTBOUND] ?? 0 }}
                        </div>
                    </div>
                    <div class="card-icon flex-center rounded-circle bg-primary">
                        <i class="fa fa-cube font-24 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br><br>

<div class="px-4 px-md-0">
    <h2>{{ __('Request Summary') }}</h2>
    <hr>

    <h3>{{ __('Total') }}</h3>
    <div class="row justify-content-center justify-content-md-between">
        <div class="col-sm-6 col-md-4 amb-24">
            <div class="card arounded-8 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">
                        <div class="atext-gray-500 text-uppercase">{{ __('Request New') }}</div>
                        <div class="font-32 text-primary">
                            {{ $requestItems[App\Models\UserRequest::STATUS_NEW] ?? 0 }}
                        </div>
                    </div>
                    <div class="card-icon flex-center rounded-circle bg-primary">
                        <i class="fa fa-plus font-24 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 amb-24">
            <div class="card arounded-8 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">
                        <div class="atext-gray-500 text-uppercase">{{ __('Request Inprogress') }}</div>
                        <div class="font-32 text-danger">
                            {{ $requestItems[App\Models\UserRequest::STATUS_INPROGRESS] ?? 0 }}
                        </div>
                    </div>
                    <div class="card-icon flex-center rounded-circle bg-danger">
                        <i class="fa fa-spinner font-24 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 amb-24">
            <div class="card arounded-8 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">
                        <div class="atext-gray-500 text-uppercase">{{ __('Request Done') }}</div>
                        <div class="font-32 text-success">
                            {{ $requestItems[App\Models\UserRequest::STATUS_DONE] ?? 0 }}
                        </div>
                    </div>
                    <div class="card-icon flex-center rounded-circle bg-success">
                        <i class="fa fa-check font-24 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h3>{{ __('This Month') }}</h3>
    <div class="row justify-content-center justify-content-md-between">
        <div class="col-sm-6 col-md-4 amb-24">
            <div class="card arounded-8 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">
                        <div class="atext-gray-500 text-uppercase">{{ __('Request New') }}</div>
                        <div class="font-32 text-primary">
                            {{ $requestCurrentItems[App\Models\UserRequest::STATUS_NEW] ?? 0 }}
                        </div>
                    </div>
                    <div class="card-icon flex-center rounded-circle bg-primary">
                        <i class="fa fa-plus font-24 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 amb-24">
            <div class="card arounded-8 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">
                        <div class="atext-gray-500 text-uppercase">{{ __('Request Inprogress') }}</div>
                        <div class="font-32 text-danger">
                            {{ $requestCurrentItems[App\Models\UserRequest::STATUS_INPROGRESS] ?? 0 }}
                        </div>
                    </div>
                    <div class="card-icon flex-center rounded-circle bg-danger">
                        <i class="fa fa-spinner font-24 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 amb-24">
            <div class="card arounded-8 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">
                        <div class="atext-gray-500 text-uppercase">{{ __('Request Done') }}</div>
                        <div class="font-32 text-success">
                            {{ $requestCurrentItems[App\Models\UserRequest::STATUS_DONE] ?? 0 }}
                        </div>
                    </div>
                    <div class="card-icon flex-center rounded-circle bg-success">
                        <i class="fa fa-check font-24 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="px-4 px-md-0">
    <h3>{{ __('Orders Shipped') }}</h3>
    <div class="row justify-content-center justify-content-md-between">
        <div class="col-sm-8 col-md-8 amb-24">
            <div id="chartdiv"></div>
        </div>
        <div class="col-sm-4 col-md-2 amb-24">
            @if (count($orders))
                <div class="amb-24">
                    <a href ="{{ route('dashboard.export') }}" class="btn btn-info export" id="export-button"> Export Order CSV </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="prices">
                        <thead>
                            <tr>
                                <th>{{ __('States') }}</th>
                                <th>{{ __('Total Orders') }}</th>
                                <th>{{ __('Percent') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($states as $name => $total)
                                @php
                                    $percent = round($total/count($orders) * 100, 2);
                                @endphp
                                <tr>
                                    <td>{{ $name }}</td>
                                    <td>{{ $total }}</td>
                                    <td>{{ $percent . '%' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>


<div class="fade-in">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('Export Orders') }}</h2>
        </div>

        <form method="GET" action="{{ route('dashboard.exportCondition') }}" class="form-horizontal" role="form">
            <div class="card-body">
                <div class="px-4 px-md-0">
                    <div class="row justify-content-center justify-content-md-between">
                        <div class="col-sm-8 col-md-8 amb-24">
                            @php
                                $fromDateDefault = \Carbon\Carbon::now()->subDay(7);
                                $toDateDefault = \Carbon\Carbon::now();
                            @endphp
                            <div class="form-group search-form-group">
                                <label for="startDate" class="col-form-label search-label"><b>{{ __('From Date') }}</b></label>
                                <div class="search-input">
                                    <input id="startDate" type="text" class="form-control w-100 date-picker @error('startDate') is-invalid @enderror" name="startDate"
                                        value="{{ date('Y-m-d', strtotime($fromDateDefault)) }}">
                                </div>
                            </div>

                            <div class="form-group search-form-group">
                                <label for="toDate" class="col-form-label search-label"><b>{{ __('To Date') }}</b></label>
                                <div class="search-input">
                                    <input id="toDate" type="text" class="form-control w-100 date-picker @error('toDate') is-invalid @enderror" name="toDate"
                                        value="{{ date('Y-m-d', strtotime($toDateDefault)) }}">
                                </div>
                            </div>

                            <div class="form-group search-form-group">
                                <label for="status" class="col-form-label search-label"><b>{{ __('Type') }}</b></label>
                                <div class="search-input">
                                    <select id="status" name="status" class="form-control w-100">
                                        <option selected></option>
                                        @foreach (App\Models\Order::$statusName as $value => $status)
                                            <option value="{{ $value }}">{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group search-form-group">
                                <label for="payment" class="col-form-label search-label"><b>{{ __('Payment Status') }}</b></label>
                                <div class="search-input">
                                    <select id="payment" name="payment" class="form-control w-100">
                                        <option selected></option>
                                        @foreach (App\Models\Order::$paymentName as $value => $status)
                                            <option value="{{ $value }}">{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group search-form-group">
                                <label for="fulfillment" class="col-form-label search-label"><b>{{ __('Fulfillment Status') }}</b></label>
                                <div class="search-input">
                                    <select id="fulfillment" name="fulfillment" class="form-control w-100">
                                        <option selected></option>
                                        @foreach (App\Models\Order::$fulfillName as $value => $status)
                                            <option value="{{ $value }}">{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <div class="search-label d-none d-sm-block"></div>
                    <div class="search-input text-center text-sm-left">
                        <input class="btn btn-success" type="submit" value="{{ __('Export') }}">
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')

<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/map.js"></script>
<script src="https://cdn.amcharts.com/lib/5/geodata/usaLow.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>

<script>

am5.ready(function() {
    // Create root
    var root = am5.Root.new("chartdiv");

    // Set themes
    root.setThemes([
        am5themes_Animated.new(root)
    ]);

    // Create chart
    var chart = root.container.children.push(am5map.MapChart.new(root, {
        panX: "rotateX",
        panY: "none",
        projection: am5map.geoAlbersUsa(),
        layout: root.horizontalLayout
    }));

    // Create polygon series
    var polygonSeries = chart.series.push(am5map.MapPolygonSeries.new(root, {
        geoJSON: am5geodata_usaLow,
        valueField: "value",
        calculateAggregates: true
    }));

    polygonSeries.mapPolygons.template.setAll({
        tooltipText: "{name}: {value}"
    });

    polygonSeries.set("heatRules", [{
        target: polygonSeries.mapPolygons.template,
        dataField: "value",
        min: am5.color(0xff621f),
        max: am5.color(0x661f00),
        key: "fill"
    }]);

    polygonSeries.mapPolygons.template.events.on("pointerover", function(ev) {
        heatLegend.showValue(ev.target.dataItem.get("value"));
    });

    polygonSeries.data.setAll([
        // { 'id': "US-AL", 'value': 4447100 },
        // { 'id': "US-AK", 'value': 626932 },
        // { id: "US-AZ", value: 5130632 },
        // { id: "US-AR", value: 2673400 },
        // { id: "US-CA", value: 33871648 },
        // { id: "US-CO", value: 4301261 },
        // { id: "US-CT", value: 3405565 },
        // { id: "US-DE", value: 783600 },
        // { id: "US-FL", value: 15982378 },
        // { id: "US-GA", value: 8186453 },
        // { id: "US-HI", value: 1211537 },
        // { id: "US-ID", value: 1293953 },
        // { id: "US-IL", value: 12419293 },
        // { id: "US-IN", value: 6080485 },
        // { id: "US-IA", value: 2926324 },
        // { id: "US-KS", value: 2688418 },
        // { id: "US-KY", value: 4041769 },
        // { id: "US-LA", value: 4468976 },
        // { id: "US-ME", value: 1274923 },
        // { id: "US-MD", value: 5296486 },
        // { id: "US-MA", value: 6349097 },
        // { id: "US-MI", value: 9938444 },
        // { id: "US-MN", value: 4919479 },
        // { id: "US-MS", value: 2844658 },
        // { id: "US-MO", value: 5595211 },
        // { id: "US-MT", value: 902195 },
        // { id: "US-NE", value: 1711263 },
        // { id: "US-NV", value: 1998257 },
        // { id: "US-NH", value: 1235786 },
        // { id: "US-NJ", value: 8414350 },
        // { id: "US-NM", value: 1819046 },
        // { id: "US-NY", value: 18976457 },
        // { id: "US-NC", value: 8049313 },
        // { id: "US-ND", value: 642200 },
        // { id: "US-OH", value: 11353140 },
        // { id: "US-OK", value: 3450654 },
        // { id: "US-OR", value: 3421399 },
        // { id: "US-PA", value: 12281054 },
        // { id: "US-RI", value: 1048319 },
        // { id: "US-SC", value: 4012012 },
        // { id: "US-SD", value: 754844 },
        // { id: "US-TN", value: 5689283 },
        // { id: "US-TX", value: 0 },
        // { id: "US-UT", value: 2233169 },
        // { id: "US-VT", value: 608827 },
        // { id: "US-VA", value: 7078515 },
        // { id: "US-WA", value: 5894121 },
        // { id: "US-WV", value: 1808344 },
        // { id: "US-WI", value: 5363675 },
        // { id: "US-WY", value: 493782 }
    ]);

    let rawStates = <?php echo json_encode($states); ?>;
    let states = Object.keys(rawStates).map(stateName => {
        return {
            id: `US-${stateName}`,
            value: rawStates[stateName],
        }
    });

    polygonSeries.data.setAll(states);

    var heatLegend = chart.children.push(am5.HeatLegend.new(root, {
        orientation: "vertical",
        startColor: am5.color(0xff621f),
        endColor: am5.color(0x661f00),
        startText: "Lowest",
        endText: "Highest",
        stepCount: 5
    }));

    heatLegend.startLabel.setAll({
        fontSize: 12,
        fill: heatLegend.get("startColor")
    });

    heatLegend.endLabel.setAll({
        fontSize: 12,
        fill: heatLegend.get("endColor")
    });

    // change this to template when possible
    polygonSeries.events.on("datavalidated", function () {
        heatLegend.set("startValue", polygonSeries.getPrivate("valueLow"));
        heatLegend.set("endValue", polygonSeries.getPrivate("valueHigh"));
    });

}); // end am5.ready()
</script>
@endsection
