@extends('layouts.app',[
'class' => '',
'folderActive' => '',
'elementActive' => 'pickup'
])

@section('styles')
<style>
    .table-responsive {
        overflow: unset;
    }
    .min-w-160 {
        min-width: 160px;
    }
    #staff-package-table a {
        text-decoration-line: none;
        color: rgba(25, 0, 254, .8);
    }
    #staff-package-table a:hover {
        color: rgba(25, 0, 254, 1);
        font-weight: bold;
    }
</style>
@endsection

@section('content')
    <?php
    header('Content-Type: image/png');
    ?>
<div class="content">
    <div class="row justify-content-center">

        <div class="col-md-9">
            <div class="card ">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="card-title">{{ __('Pickup Request') }}</h4>
                </div>

                <div class="card-body ">
                    <form method="get" action="" class="form-horizontal" id="packing_list_search_form">
                        <div class="row">
                            {{--<div class="col-xl-4 col-12">
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Keyword</label>
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <input type="text" id="keyword" name="keyword" class="form-control"
                                                   value="{{ old('keyword') ?? '' }}"
                                                   autocomplete="on" />
                                        </div>
                                    </div>
                                </div>
                            </div>--}}
                            <div class="col-xl-4 col-12">
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">From date</label>
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <input type="text" class="form-control datepicker" id="date_from"
                                                   value="{{ request('date_from') ?? date('Y-m-d', strtotime('-1 month')) }}"
                                                   name="date_from" placeholder="YYYY-MM-DD" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-12">
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">To date</label>
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <input type="text" class="form-control datepicker" id="date_to"
                                                   value="{{ request('date_to') ?? date('Y-m-d') }}"
                                                   name="date_to" placeholder="YYYY-MM-DD" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="row justify-content-center">
                                    <button type="submit" class="btn btn-info btn-round min-w-160">Search</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <div class="fade-in">
        <div class="card">
            <div class="card-footer">
                @if (count($data['pickups']) == 0)
                    <div class="text-center">{{ __('No data.') }}</div>
                @else
                    <div class="table-responsive">
                        <table class="table datatable"
                            id="staff-package-table">
                            <thead class="text-primary">
                                <tr>
                                    <th>{{ __('Request Code') }}</th>
                                     <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Count') }}</th>
                                    <th>{{ __('KG') }}</th>
                                    <th>{{ __('Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data['pickups'] as $pickup)
                                    <tr>
                                        <td>
                                           <a href="{{ route('staff.pickup.show', ['pickup_id' => $pickup->id]) }}"> {{ $pickup->pickup_code }} </a>
                                        </td>
                                        <td>{{ $pickup->created_username }}</td>
                                        <td>{{ App\Models\PickupRequest::$statusName[$pickup->status] }}</td>
                                        <td> {{ count($pickup->orderJourneys) }}
                                        </td>
                                        <td><b>{{ $pickup->totalKG }}</b></td>
                                        <td>{{ $pickup->created_date }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{--<div class="d-flex justify-content-center justify-content-md-end amt-16">
                        {{ $data['pickups']->appends(request()->all())->links() }}
                    </div>--}}
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            /* datepicker */
            // format date, default is MM/DD/YYYY
            demo.date_format = 'YYYY-MM-DD';

            // initialise Datetimepicker and Sliders
            demo.initDateTimePicker();

            // Datatable
            $('.datatable').DataTable({
                "pagingType": "full_numbers",
                "lengthMenu": [
                    // [10, 25, 50, -1],
                    // [10, 25, 50, "All"]
                    [50, 20, 10],
                    [50, 20, 10]
                ],
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search records",
                },
                "aaSorting": [],
                // "ordering": false,
            });
        })
    </script>
@endpush
