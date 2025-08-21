@extends('layouts.app', [
    'class' => 'register-page',
    'backgroundImagePath' => 'img/bg/jan-sendereks.jpg',
    'folderActive' => '',
    'elementActive' => ''
])

@section('styles')
    <style>
        .register-page .navbar .navbar-collapse .nav-item .nav-link {
            color: unset !important;
        }
        .navbar.navbar-transparent a:not(.dropdown-item):not(.btn) {
            color: #66615b !important;
        }
    </style>
@endsection

@section('content')
    <div class="content">
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
    </div>
@endsection
