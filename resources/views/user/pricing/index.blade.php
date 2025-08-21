@extends('layouts.user')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard',
            'url' => route('dashboard')
        ],
        [
            'text' => 'Pricing request'
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
        <div class="card-header">
            <h2 class="mb-0">{{ __('Pricing request') }}</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('pricing.create') }}" class="form-horizontal" role="form">
            @csrf
                @if(isset($lastRequest))
                    <div class="form-group search-form-group">
                        <label for="address" class="search-label col-form-label"><b>{{ __('Last request') }}</b></label>
                        <div class="search-input col-form-label">
                            {{ date('Y-m-d H:i:s', strtotime($lastRequest['created_at'])) }}
                        </div>  
                    </div>
                @endif
                <div class="form-group search-form-group">
                    <label for="address" class="search-label col-form-label"><b>{{ __('Note') }}</b></label>
                    <div class="search-input">
                        <textarea class="form-control" name="note" rows="3" ></textarea>
                    </div>
                </div>
                
                @php 
                    $canRequest = true;
                    if(isset($lastRequest)) {
                        $ldate = new DateTime(date("Y-m-d H:i:s"));
                        $diff = $ldate->diff($lastRequest['created_at']);
                        $day = $diff->format("%a");

                        if($day == 0 || $day == "0") {
                            $canRequest = false;
                        }
                    }
                @endphp

                @if($canRequest) 
                    <div class="search-form-group">
                        <div class="search-label d-none d-sm-block"></div>
                        <div class="search-input text-center text-sm-left">
                            <input class="btn btn-primary" type="submit" value="{{ __('Request') }}">
                        </div>
                    </div>
                @else 
                    <div class="form-group search-form-group">
                        <div class="search-input col-form-label">
                            You can't send request now.
                        </div>  
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection
