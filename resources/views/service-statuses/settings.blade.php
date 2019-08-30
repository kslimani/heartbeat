@extends('layouts.menu')

@section('head')
<style>
    .control-switch {
        font-size: 140%;
    }

    .rtd-control {
        cursor: not-allowed;
    }

    /* BS4 temporary workaround */
    .invalid-feedback {
      display: block;
    }
</style>
@endsection

@section('main')

    <div class="mb-3"><h3>{{ __('app.settings') }}<small class="text-muted"> :: {{ $serviceStatus->service->label }} @ {{ $serviceStatus->device->label }}</small></h3></div>

    <form method="POST" action="{{ route('service-statuses.update-settings', ['id' => $serviceStatus->id]) }}">
        @method('PUT')
        @csrf
        <input type="hidden" name="is_mute" value="{{ $serviceStatus->pivot->is_mute ? '1' : '0' }}">

        <div class="form-group row">
            <label for="is_mute" class="col-md-4 col-form-label text-md-right">{{ __('app.notifications') }}</label>

            <div class="col-md-6">
                <input type="checkbox" class="js-IsMute" {{ $serviceStatus->pivot->is_mute ? '' : 'checked' }}>

                @if ($errors->has('is_mute'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('is_mute') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <!-- Read only -->
        <div class="form-group row">
            <label for="rtd" class="col-md-4 col-form-label text-md-right">{{ __('app.rtd') }}</label>

            <div class="col-md-6">
                <div class="input-group mb-3">
                    <div class="form-control rtd-control">{{ $defaultRtd }}</div>

                    <div class="input-group-append">
                        <span class="input-group-text" id="rtd-unit">{{ __('app.seconds') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group row mb-0">
            <div class="col-md-6 offset-md-4">
                <a href="{{ route('service-statuses.show', ['id' => $serviceStatus->id]) }}" class="btn btn-secondary" role="button">
                    <span class="mdi mdi-arrow-left-bold" aria-hidden="true"></span>{{ __('app.back') }}
                </a>
                <button type="submit" class="btn btn-primary">
                    <span class="mdi mdi-check-bold" aria-hidden="true"></span>{{ __('app.save') }}
                </button>
            </div>
        </div>
    </form>
@endsection

@section('footer')
<script>
    $(function () {
        // Notifications toggle button
        $('.js-IsMute')
            .bootstrapToggle({
              on: '{{ __('app.enabled') }}',
              off: '{{ __('app.disabled') }}',
            })
            .change(function() {
                $('input[name=is_mute]').val($(this).prop('checked') ? '0' : '1');
            });
    })
</script>
@endsection
