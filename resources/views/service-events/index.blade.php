@extends('layouts.menu')

@section('main')

    <div class="mb-3"><h3>{{ __('app.services_events') }}</h3></div>

    @include('partials.timeline')

    {{ $events->links() }}

    @include('partials.updated-on')
@endsection
