@extends('layouts.app')

@section('head')
        <style>
            .content {
                text-align: center;
            }

            .title {
                font-size: 150%;
                color: #636b6f;
                margin-bottom: 30px;
                margin-left: 30px;
                margin-right: 30px;
            }

            .btn-login {
                margin-top: 1rem;
            }
        </style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="content">
            <div class="title">
                Heartbeat is a simple back-end application to keep track of service statuses.
            </div>
        </div>
    </div>
    @guest
    <div class="row justify-content-center">
        <a class="btn btn-primary btn-lg btn-login" href="{{ route('login') }}">{{ __('Login') }}</a>
    </div>
    @endguest
</div>
@endsection

@section('footer')
<footer class="footer">
    <div class="container">
        <div class="text-center text-muted">Project source code is available at <a href="https://github.com/kslimani/heartbeat">Github</a>.</div>
    </div>
</footer>
@endsection
