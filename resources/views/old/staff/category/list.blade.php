@extends('layouts.staff')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('staff.dashboard')
        ],
        [
            'text' => 'Category'
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
            <h2 class="mb-0">Category list</h2>
            <a class="btn btn-success" href="{{ route('staff.category.new') }}">
                {{ __('New Category') }}
            </a>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('staff.category.list') }}" class="form-horizontal" role="form">
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
                    <table class="table table-align-middle table-bordered table-striped table-sm" id="staff-category-list-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>{{ __('Name') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                            <tr>
                                <td>{{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}</td>
                                <td>{{ $category->name }}</td>
                                <td>
                                    <button class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="rename({{ $category->id }}, '{{ $category->name }}')">
                                        {{ __('Update name') }}
                                    </button>
                                </td>
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

<!-- Modal -->
<div id="rename-category-modal" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                {{ __('Rename') }}
            </div>
            <div class="modal-body">
                <form action="{{ route('staff.category.update') }}" method="POST" enctype="multipart/form-data" id="category-rename">
                @csrf
                    <input type="hidden" id="category_id" value="" name="id" />
                    <div class="form-group search-form-group">
                        <label for="type" class="col-form-label search-label"><b>{{ __('Old') }}</b></label>
                        <div class="search-input position-relative">
                            <input type="input" class="form-control w-100" id="old-name" disabled />
                        </div>
                    </div>
                    <div class="form-group search-form-group">
                        <label for="type" class="col-form-label search-label"><b>{{ __('New') }}</b></label>
                        <div class="search-input position-relative">
                            <input type="input" class="form-control w-100" id="new-name" name="name" />
                            <p class="text-danger mb-0" id="error-message">
                                
                            </p>
                        </div>
                    </div>

                    <div class="btn btn-success" onclick="submit()"> {{ __('Update') }} </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let categories = @php echo json_encode($categoryNames) @endphp;

    filterInput(document.getElementById("name-input"), categories, 'dropdown-name');

    function rename(id, oldName) {
        $('#category_id').val(id);
        $('#old-name').val(oldName);
    }

    function submit() {
        const value = $('#new-name').val();
        
        if(!value) {
            $('#new-name').addClass('is-invalid');
            $('#error-message').text('Name required!');

            return;
        }

        const index = categories.indexOf(value);

        if(index > -1) {
            $('#new-name').addClass('is-invalid');
            $('#error-message').text('Name already existed!');

            return;
        }

        const oldValue = $('$old-name').val();

        if(value === oldValue) {
            $('#new-name').addClass('is-invalid');
            $('#error-message').text('Name already existed!');

            return;
        }

        $('#category-rename').submit()
    }
</script>
@endsection
