@extends('layouts.menu')

@section('head')
<meta http-equiv="refresh" content="{{ config('app.page_refresh_interval', 60) }}">
@endsection

@section('main')

    <div class="mb-3"><h3>{{ __('app.services_events') }}</h3></div>

    @include('partials.timeline')

    {{ $events->links() }}

    @include('partials.updated-on')
@endsection
