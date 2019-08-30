@extends('layouts.menu')

@section('head')
<meta http-equiv="refresh" content="{{ config('app.page_refresh_interval', 60) }}">
<style>
    .card-header-service {
        font-size: 140%;
        font-weight: bolder;
    }

    .card-footer {
        color: white;
        font-weight: bolder;
    }
</style>
@endsection

@section('main')
    <div class="mb-3"><h3>{{ __('app.service_status') }}</h3></div>

    <div class="card text-center">
        <div class="card-body">
            <div class="card-header-service">{{ $serviceStatus->service->label }}</div>
            <div class="text-muted">@ {{ $serviceStatus->device->label }}</div>
        </div>
        <div class="card-footer {{ $serviceStatus->status->name === 'UP' ? 'bg-success' : 'bg-danger' }}">{{ $serviceStatus->label_status }}</div>
    </div>

    <div class="text-center pt-3">
        <a href="{{ route('service-statuses.edit-settings', ['id' => $serviceStatus->id]) }}" class="btn btn-outline-primary form-control">
            <span class="mdi mdi-settings"></span>{{ __('app.settings') }}
        </a>
    </div>

    <div class="mb-3 section-break"><h3>{{ __('app.services_events') }}</h3></div>

    @include('partials.timeline', ['withoutDeviceLine' => true])

    {{ $events->links() }}

    @include('partials.updated-on')
@endsection
