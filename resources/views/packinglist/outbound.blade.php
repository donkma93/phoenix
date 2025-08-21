@extends('layouts.app',[
'class' => '',
'folderActive' => '',
'elementActive' => 'packing'
])

@section('styles')
<style>
    .table-responsive {
        overflow: unset;
    }
    .min-w-160 {
        min-width: 160px;
    }
</style>
@endsection

@section('content')
    <?php
    header('Content-Type: image/png');
    ?>
<div class="content">
    @if (session('success'))
        <div class="row justify-content-end">
            <div class="col-md-4">
                <div class="alert alert-success alert-dismissible fade show">
                    <button type="button" aria-hidden="true" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="nc-icon nc-simple-remove"></i>
                    </button>
                    <span>
                            <b> Success - </b>
                            {{ session('success') }}
                        </span>
                </div>
            </div>
        </div>
    @endif
    @if (session('error'))
        <div class="row justify-content-end">
            <div class="col-md-4">
                <div class="alert alert-danger alert-dismissible fade show">
                    <button type="button" aria-hidden="true" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="nc-icon nc-simple-remove"></i>
                    </button>
                    <span>
                            <b> Error - </b>
                            {{ session('error') }}
                        </span>
                </div>
            </div>
        </div>
    @endif
    <div class="row justify-content-center">

        <div class="col-md-9">
            <div class="card ">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="card-title">{{__('Packing List Outbound')}}</h4>
                </div>

                <div class="card-body ">
                    <form method="get" action="" class="form-horizontal" id="packing_list_search_form">
                        <div class="row">
                            <div class="col-xl-4 col-12">
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
                            </div>
                            <div class="col-xl-4 col-12">
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">From date</label>
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <input type="text" class="form-control datepicker" id="date_from"
                                                   value="{{ old('date_from') ?? date('Y-m-d', strtotime('-1 week')) }}"
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
                                                   value="{{ old('date_to') ?? date('Y-m-d') }}"
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
                @if (count($data['packing_list']) == 0)
                    <div class="text-center">{{ __('No data.') }}</div>
                @else
                    <div class="table-responsive">
                        <table class="table datatable"
                            id="staff-package-table">
                            <thead class="text-primary">
                                <tr>
                                    <th class="disabled-sorting">{{ __('Master Bill') }}</th>
                                    <th class="disabled-sorting">{{ __('Packing List') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th class="disabled-sorting">{{ __('Status') }}</th>
                                    <th>{{ __('Created Date') }}</th>
                                    <th>{{ __('Updated Date') }}</th>
                                    <th>{{ __('Received Date') }}</th>
                                    <th class="disabled-sorting"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data['packing_list'] as $packing)
                                    <tr>
                                        <td>
                                            {{ $packing->master_bill }}
                                        </td>
                                        <td><p title="PACKINGLIST" >{{ $packing->packing_list_code }}</p></td>
                                        <td> {{ $packing->quantity }}
                                        </td>
                                        <td>{{ App\Models\PackingList::$statusName[$packing->status] }}</td>
                                        <td>{{ $packing->created_date }}</td>
                                        <td>{{ $packing->updated_at }}</td>
                                        <td>{{ $packing->received_at ?? '' }}</td>
                                        <td>
                                            @if(!isset($packing->master_bill))
                                                <a class="btn btn-primary btn-round btn-block"
                                                    href="{{ route('staff.packing.show', ['packing_id' => $packing->id]) }}">
                                                    {{ __('Finish') }}
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{--<div class="d-flex justify-content-center justify-content-md-end amt-16">
                        {{ $data['packing_list']->appends(request()->all())->links() }}
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
