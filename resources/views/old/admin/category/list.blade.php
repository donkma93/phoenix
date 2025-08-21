@extends('layouts.admin')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('admin.dashboard')
        ],
        [
            'text' => 'Category'
        ]
    ]
])
@endsection

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Category list</h2>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('admin.category.list') }}" class="form-horizontal" role="form">
                <div class="form-group search-form-group">
                    <label for="email" class="col-form-label search-label"><b>{{ __('Name') }}</b></label>
                    <div class="search-input">
                        <input type="text" class="form-control" name="name" id="name-input" list="dropdown-name"  value="@if (isset($oldInput['name'])){{$oldInput['name']}}@endif" autocomplete="off" />
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
            @if (count($categories) == 0)
                <div class="text-center">{{ __('No data.') }}</div>
            @else
                <div class="table-responsive">
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="admin-category-list-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>{{ __('Name') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                            <tr>
                                <td>{{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}</td>
                                <td>{{ $category->name }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center justify-content-md-end amt-16">
                    {{ $categories->appends(request()->all())->links('components.pagination') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let categories = @php echo json_encode($categoryNames) @endphp;

    filterInput(document.getElementById("name-input"), categories, 'dropdown-name');
</script>
@endsection
