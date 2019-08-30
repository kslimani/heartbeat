@extends('layouts.menu')

@section('head')
<style>
    /* BS4 temporary workaround */
    .invalid-feedback {
      display: block;
    }
</style>
@endsection

@section('main')

    <div class="mb-3"><h3>{{ __('app.services') }}<small class="text-muted"> :: {{ $serviceStatus->service->label }} @ {{ $serviceStatus->device->label }}</small></h3></div>

    <form method="POST" action="{{ route('service-statuses.update', ['id' => $serviceStatus->id]) }}">
        @method('PUT')
        @csrf

        <div class="form-group row">
            <label for="rtd" class="col-md-4 col-form-label text-md-right">{{ __('app.rtd') }}</label>

            <div class="col-md-6">
                <div class="input-group mb-3">
                    <input id="rtd" class="form-control{{ $errors->has('rtd') ? ' is-invalid' : '' }}" type="number" step="60" min="60" name="rtd"
                        value="{{ old('rtd', $serviceStatus->rtd) }}" placeholder="{{ __('app.rtd_default', ['rtd' => $defaultRtd]) }}">
                    <div class="input-group-append">
                        <span class="input-group-text">{{ __('app.seconds') }}</span>
                    </div>
                </div>

                @if ($errors->has('rtd'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('rtd') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group row mb-0">
            <div class="col-md-6 offset-md-4">
                <a href="{{ route('service-statuses.index') }}" class="btn btn-secondary" role="button">
                    <span class="mdi mdi-arrow-left-bold" aria-hidden="true"></span>{{ __('app.back') }}
                </a>
                <button type="submit" class="btn btn-primary">
                    <span class="mdi mdi-check-bold" aria-hidden="true"></span>{{ __('app.save') }}
                </button>
            </div>
        </div>
    </form>
@endsection
