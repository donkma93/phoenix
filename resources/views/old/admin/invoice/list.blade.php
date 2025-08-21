@extends('layouts.admin')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('admin.dashboard')
        ],
        [
            'text' => 'Invoice'
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
<?php
header("Content-Type: image/png");
?>
<div class="fade-in">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('Invoice list') }}</h2>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.invoice.send') }}" class="form-horizontal" role="form">
                @csrf
                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Invoice Target Year') }}</b></label>
                    <div class="search-input">
                        <select name="target_year" class="form-control w-100">
                            @php
                                $currentYear = date("Y");
                            @endphp
                                <option selected></option>
                            @foreach(range(1990, $currentYear) as $year)
                                <option value="{{ $year }}"
                                @if (old('target_year') == $year)
                                    selected
                                @endif
                                >{{ $year }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('target_year'))
                            <p class="text-danger mb-0">
                                {{ $errors->first('target_year') }}
                            </p>
                        @endif
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Invoice Target Month') }}</b></label>
                    <div class="search-input">
                        <select name="target_month" class="form-control w-100">
                            <option selected></option>
                            @foreach(range(1,12) as $month)
                                <option value="{{ $month }}"
                                @if (old('target_month') == $month)
                                    selected
                                @endif
                                >{{ $month }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('target_month'))
                            <p class="text-danger mb-0">
                                {{ $errors->first('target_month') }}
                            </p>
                        @endif
                    </div>
                </div>

                <div class="search-form-group">
                    <div class="search-label d-none d-sm-block"></div>
                    <div class="search-input text-center text-sm-left">
                        <input class="btn btn-primary" type="submit" value="{{ __('Send Invoice') }}">
                    </div>
                </div>
            </form>
        </div>

        <hr>

        <div class="card-body">
            <form method="GET" action="{{ route('admin.invoice.list') }}" class="form-horizontal" role="form">
                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Search By Email') }}</b></label>
                    <div class="search-input position-relative">
                        <input type="input" class="form-control w-100" id="email-input" list="dropdown-email" name="email" value="@if (isset($oldInput['email'])){{$oldInput['email']}}@endif" autocomplete="off" />
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Search By Year') }}</b></label>
                    <div class="search-input">
                        <select name="year" class="form-control w-100">
                            @php
                                $currentYear = date("Y");
                            @endphp
                                <option selected></option>
                            @foreach(range(1990, $currentYear) as $year)
                                <option value="{{ $year }}"
                                @if (isset($oldInput['year']) && $oldInput['year'] == $year)
                                            selected
                                        @endif
                                >{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group search-form-group">
                    <label class="col-form-label search-label"><b>{{ __('Search By Month') }}</b></label>
                    <div class="search-input">
                        <select name="month" class="form-control w-100">
                            <option selected></option>
                            @foreach(range(1,12) as $month)
                                <option value="{{ $month }}"
                                @if (isset($oldInput['month']) && $oldInput['month'] == $month)
                                            selected
                                        @endif
                                >{{ $month }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="search-form-group">
                    <div class="search-label d-none d-sm-block"></div>
                    <div class="search-input text-center text-sm-left">
                        <input class="btn btn-primary" type="submit" value="{{ __('Search') }}">
                    </div>
                </div>
            </form>
        </div>

        <div class="card-footer">
            @if (count($invoices) == 0)
                <div class="text-center">{{ __('No data.') }}</div>
            @else
                <div class="table-responsive">
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-invoice-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Month') }}</th>
                                <th>{{ __('Year') }}</th>
                                <th>{{ __('Balance') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoices as $invoice)
                                <tr>
                                    <td>{{ ($invoices->currentPage() - 1) * $invoices->perPage() + $loop->iteration }}</td>
                                    <td>{{ $invoice->user->email }}</td>
                                    <td>{{ $invoice->month }}</td>
                                    <td>{{ $invoice->year }}</td>
                                    <td>{{ round($invoice->balance, 4) }}</td>
                                    <td>{{ $invoice->created_at }}</td>
                                    <td>
                                        <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="previewPDF(`{{ asset($invoice->file) }}`)">
                                            {{ __('Preview') }}
                                        </button>
                                    </td>
                                    <td>
                                        <a class="btn btn-info btn-block" href="{{ route('admin.invoice.detail', ['id' => $invoice->id]) }}">
                                            {{ __('Detail') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center justify-content-md-end amt-16">
                    {{ $invoices->appends(request()->all())->links('components.pagination') }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="preview-pdf">
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
    let users = @php echo json_encode($users) @endphp;

    filterInput(document.getElementById("email-input"), users, 'dropdown-email');

    function previewPDF(file) {
        const { jsPDF } = window.jspdf;
        const splitFile = file.split('.');
        const fileType = splitFile[splitFile.length - 1];
        const validImageTypes = ['gif', 'jpeg', 'png', 'tiff', 'jpg', 'heif'];

        let imgSrc;
        if (validImageTypes.includes(fileType)) {
            let doc = new jsPDF("p", "mm", "a4");

            let width = doc.internal.pageSize.getWidth();
            let height = doc.internal.pageSize.getHeight();
            doc.addImage(file, 'JPEG',  0, 0, width, height);
            imgSrc = doc.output('bloburl');

        } else {
            imgSrc = file
        }

        $("#preview-pdf").find("embed").remove();
            let embed = "<embed src="+ imgSrc +" frameborder='0' width='100%' height='500px' type='application/pdf' class='preview-pdf'>"
        $("#preview-pdf").append(embed)
    }
</script>
@endsection
