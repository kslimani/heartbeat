@extends('layouts.menu')

@section('head')
<style>
    .td-lbl {
        line-height: 1;
    }
</style>
@endsection

@section('main')

    <div class="mb-3"><h3>{{ __('app.services') }}</h3></div>

    <div class="pb-3">
        <form action="{{ route('service-statuses.index') }}" class="js-Search">
            <div class="input-group">
                <input class="form-control" type="text" name="q" value="{{ $search }}" placeholder="{{ __('app.search') }}...">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">
                        <span class="mdi mdi-magnify"></span> {{ __('app.search') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead class="thead-light">
                <tr>
                    <th scope="col">{{ __('app.device') }}</th>
                    <th scope="col">{{ __('app.service') }}</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
            @foreach ($serviceStatuses as $serviceStatus)
                <tr class="text-nowrap">
                    <td class="td-lbl">
                        <a href="{{ route('devices.edit', ['device' => $serviceStatus->device->id]) }}">{{ $serviceStatus->device->label }}</a><br />
                        <small class="text-muted">{{ $serviceStatus->device->name }}</small>
                    </td>
                    <td class="td-lbl">
                        <a href="{{ route('services.edit', ['service' => $serviceStatus->service->id]) }}">{{ $serviceStatus->service->label }}</a><br />
                        <small class="text-muted">{{ $serviceStatus->service->name }}</small>
                    </td>
                    <td class="td-btn">
                        <form method="POST" action="{{ route('service-statuses.destroy', ['id' => $serviceStatus->id]) }}">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                            <button class="btn btn-link" type="submit" onclick="return confirm('{{ __('app.msg_destroy') }}. {{ __('app.msg_confirm') }}')">
                                <span class="mdi mdi-18px mdi-trash-can-outline" aria-hidden="true"></span>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{ $serviceStatuses->links() }}

@endsection
