@extends('layouts.menu')

@section('head')
<meta http-equiv="refresh" content="{{ config('app.status_change_interval') * 60 }}">
<style>
    .status {
        padding-bottom: 5px;
    }

    .status-count {
        text-align: center;
        font-size: 50px;
        line-height: 1;
        margin: 0 0 10px;
    }

    .status-text {
        text-align: center;
        margin: 0 0 10px;
    }

    .tooltip-inner {
        max-width: none;
    }
</style>
@endsection

@section('main')
    <div class="mb-3"><h3>{{ __('app.services_statuses') }}</h3></div>

    <div class="card">
      <div class="card-body status">
        <div class="row">
            <div class="col-sm">
                <p class="status-count">{{ $statuses['INACTIVE'] }}</p>
                <p class="status-text">INACTIVE</p>
            </div>
            <div class="col-sm">
                <p class="status-count">{{ $statuses['DOWN'] }}</p>
                <p class="status-text">DOWN</p>
            </div>
            <div class="col-sm">
                <p class="status-count">{{ $statuses['UP'] }}</p>
                <p class="status-text">UP</p>
            </div>
        </div>
      </div>
    </div>

    <div class="card section-break">
      <ul class="list-group list-group-flush">
        @foreach ($byDevices as $serviceStatuses)
        <li class="list-group-item">
            <div>{{ $serviceStatuses->first()->device->label }}</div>
            <div>
                @foreach ($serviceStatuses as $serviceStatus)
                <a href="{{ route('service-statuses.show', ['id' => $serviceStatus->id]) }}" class="badge {{ $serviceStatus->status->name === 'UP' ? 'badge-success' : 'badge-danger' }}"
                    data-toggle="tooltip" data-placement="top" title="{{ $serviceStatus->label_tooltip }}">{{ $serviceStatus->service->label }}</a>
                @endforeach
            </div>
        </li>
        @endforeach
      </ul>
    </div>

    <div class="mb-3 section-break"><h3>{{ __('app.past_events') }}</h3></div>

    @include('partials.timeline')

    <a href="{{ route('service-events.index') }}"><div class="mdi mdi-chevron-left">{{ __('app.services_events') }}</div></a>

    @include('partials.updated-on')
@endsection

@section('footer')
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    })
</script>
@endsection
