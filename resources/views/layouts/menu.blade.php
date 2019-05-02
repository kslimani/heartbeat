@extends('layouts.app')

@section('content')
<div class="container">

    @if(session()->has('alert.success'))
        @include('partials.alert', ['type' => 'success', 'iconClass' => 'mdi mdi-comment-check-outline', 'message' => session('alert.success')])
    @elseif(session()->has('alert.danger'))
        @include('partials.alert', ['type' => 'danger', 'iconClass' => 'mdi mdi-comment-remove-outline', 'message' => session('alert.danger')])
    @elseif(session()->has('alert.warning'))
        @include('partials.alert', ['type' => 'warning', 'iconClass' => 'mdi mdi-comment-alert-outline', 'message' => session('alert.warning')])
    @elseif(session()->has('alert.info'))
        @include('partials.alert', ['type' => 'info', 'iconClass' => 'mdi mdi-comment-outline', 'message' => session('alert.info')])
    @endif

    <div class="row justify-content-center">

        <div class="col-md-8">
            @yield('main')
        </div>
    </div>
</div>
@endsection
