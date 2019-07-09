@extends('layouts.menu')

@section('head')
<meta http-equiv="refresh" content="{{ config('app.status_change_interval') * 60 }}">
<style>
    .card-header-service {
        font-size: 140%;
        font-weight: bolder;
    }

    .card-body > .container {
        line-height: 3rem;
    }

    .card-footer {
        color: white;
        font-weight: bolder;
    }

    .notifications {
        margin-right: 20px;
    }
</style>
@endsection

@section('main')

    <div class="mb-3"><h3>{{ __('app.service_status') }}</h3></div>

    <div class="card text-center">
        <div class="card-header">
            <div class="card-header-service">{{ $serviceStatus->service->label }}</div>
            <div class="text-muted">@ {{ $serviceStatus->device->label }}</div>
        </div>
        <div class="card-body">
            <div class="container">
                <div class="row">
                    <div class="col text-nowrap">
                        <span class="notifications">{{ __('app.notifications') }}
                            <span class="font-weight-bold {{ $isMute ? 'text-danger' : 'text-success' }}">
                                {{ __($isMute ? 'app.disabled' : 'app.enabled') }}
                            </span>
                        </span>
                        <a href="javascript:" class="js-Btn btn btn-primary">
                            <span class="mdi mdi-18px {{ $isMute ? 'mdi-bell-outline' : 'mdi-bell-off-outline' }}"></span>{{ __($isMute ? 'app.enable' : 'app.disable') }}</a>
                        <form class="js-Form" method="POST" action="{{ route('service-statuses.update', ['id' => $serviceStatus->id]) }}">
                            @method('PUT')
                            @csrf
                            <input type="hidden" name="is_mute" value="{{ $isMute ? '0' : '1' }}">
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer {{ $serviceStatus->status->name === 'UP' ? 'bg-success' : 'bg-danger' }}">{{ $serviceStatus->label_status }}</div>
    </div>

    <div class="mb-3 section-break"><h3>{{ __('app.services_events') }}</h3></div>

    @include('partials.timeline', ['withoutDeviceLine' => true])

    {{ $events->links() }}

    @include('partials.updated-on')
@endsection

@section('footer')
<script>
    $(function () {
        // Toggle notifications button
        $('.js-Btn').on('click', function() {
            if (confirm($(this).text().trim() + ' ?')) {
                $('.js-Form').submit();
            }
        });
    })
</script>
@endsection
