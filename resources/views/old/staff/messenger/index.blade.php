@extends('layouts.staff')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/messenger.css') }}">
@endsection

@section('content')
<div id="app"></div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.10/dist/vue.js"></script>
<script src="{{ asset('js/messenger.js') }}"></script>
@endsection
