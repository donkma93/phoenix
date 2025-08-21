@extends('layouts.app', [
'class' => '',
'folderActive' => '',
'elementActive' => 'epacket',
])

@section('styles')
<style>
    .table-responsive {
        overflow: unset;
    }
    .min-w-160 {
        min-width: 160px;
    }
    .card .card-header {
        border-bottom: 1px solid #d8dbe0;
    }
</style>
@endsection

@section('content')
    <?php
    header('Content-Type: image/png');
    ?>
<div class="content">
    <div class="fade-in">
      <form target="_blank" method="get" action="{{ route('pickup.detail.print') }}">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="mb-0">{{ __('Pickup Request Detail') }}</h2>

                  <div class="search-form-group d-flex justify-content-center align-items-center"
                  style="flex-direction: column!important"
                  >
                                <div class="search-input text-center text-sm-left">
                                    <input class="btn btn-primary mb-3" type="submit" value="{{ __('Print') }}">
                                </div>
                    </div>
            </div>
            <div class="card-footer">
                @if (count($orderJourneys) == 0)
                    <div class="text-center">{{ __('No data.') }}</div>
                @else

                    <div class="table-responsive">
                        <table class="table table-striped table-sm datatable"
                            id="staff-package-table">
                            <thead>
                                <tr class="text-primary">
                                    <th class="disabled-sorting">
                                        <label for="select_all_order"
                                               class="d-flex mb-0 justify-content-center align-items-center"
                                               style="padding: 4px 0px;">
                                            <input id="select_all_order" class="" type="checkbox"
                                                   name="" value="">
                                        </label>
                                    </th>
                                    <th class="disabled-sorting">#</th>
                                    <th>{{ __('Order Code') }}</th>
                                    <th>{{ __('Partner') }}</th>
                                    <th>{{ __('Info') }}</th>
                                    <th>{{ __('KG') }}</th>
                                    <th>{{ __('Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = 0;
                                @endphp
                                @foreach ($orderJourneys as $orderJourney)
                                    @php
                                        $i++;
                                    @endphp
                                    <tr>
                                        <td style="text-align: center;">
                                            <input id="{{ $orderJourney->orders->order_code }}" class="form-group select_item_order" type="checkbox"
                                                name="label_list[]" value="{{$orderJourney->orders->order_code}}">
                                        </td>
                                        {{--<td>{{ ($orderJourneys->currentPage() - 1) * $orderJourneys->perPage() + $loop->iteration }}
                                        </td>--}}
                                        <td>{{ $i }}</td>
                                        <td>
                                            {{ $orderJourney->orders->order_code }}
                                        </td>
                                        <td
                                         style="text-align:center"
                                        >{{ $orderJourney->orders->partner_code }}</td>
                                        <td>
                                            <div><b>Name: </b>{{ $orderJourney->orders->addressTo->name ?? $orderJourney->orders->shipping_name ?? '' }}</div>
                                            <div><b>Street: </b>{{ $orderJourney->orders->addressTo->street1 ?? $orderJourney->orders->shipping_street ?? '' }}</div>
                                            <div><b>Province: </b>{{ $orderJourney->orders->addressTo->state ?? $orderJourney->orders->shipping_province ?? '' }}</div>
                                            <div><b>WxHxD: </b>{{ $orderJourney->orders->orderPackage->width.'x'.$orderJourney->orders->orderPackage->height .'x'.$orderJourney->orders->orderPackage->length}}</div>
                                        </td>
                                        <td
                                            style="text-align:center;font-size:18px;"
                                        ><b>{{ $orderJourney->orders->orderPackage->weight }}</b>
                                        </td>
                                        <td>{{ $orderJourney->created_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                     </div>

                    {{--<div class="d-flex justify-content-center justify-content-md-end amt-16">
                        {{ $orderJourneys->appends(request()->all())->links() }}
                    </div>--}}
                @endif
            </div>
        </div>
    </form>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Datatable
            $('.datatable').DataTable({
                "pagingType": "full_numbers",
                "lengthMenu": [
                    // [10, 25, 50, -1],
                    // [10, 25, 50, "All"]
                    [200, 100, 50, 20, 10],
                    [200, 100, 50, 20, 10]
                ],
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search records",
                },
                "aaSorting": [],
                // "ordering": false,
            });
        });
    </script>
@endpush
