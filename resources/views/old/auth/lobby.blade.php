@extends('layouts.auth')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="mb-4">
                        {{ __('lobby.lobby-content') }}
                    </div>
                    <div class="d-flex items-items-center justify-content-end">
                        <form method="POST" action="{{ route('logout', ['locale' => app()->getLocale()]) }}">
                            @csrf
                            <button type="submit" class="btn btn-secondary">
                                {{ __('lobby.logout') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
