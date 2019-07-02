@extends('layouts.menu')

@section('head')
<style>
    .card-body {
        padding: .375rem .75rem;
        cursor: not-allowed;
    }
</style>
@endsection

@section('main')

    <div class="mb-3"><h3>{{ __('app.devices_edit') }}</h3></div>

    <form method="POST" action="{{ route('devices.update', ['device' => $device->id]) }}">
        @method('PUT')
        @csrf

        <div class="form-group row">
            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('app.name') }}</label>
            <div class="col-md-6">
                <div class="card bg-light" id="name">
                    <div class="card-body">{{ $device->name }}</div>
                </div>
            </div>
        </div>

        <div class="form-group row">
            <label for="label" class="col-md-4 col-form-label text-md-right">{{ __('app.label') }}</label>

            <div class="col-md-6">
                <input id="label" type="label" class="form-control{{ $errors->has('label') ? ' is-invalid' : '' }}" name="label" value="{{ old('label', $device->label) }}" required autocomplete="label">

                @if ($errors->has('label'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('label') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group row mb-0">
            <div class="col-md-6 offset-md-4">
                <a href="{{ route('devices.index') }}" class="btn btn-secondary" role="button">
                    <span class="mdi mdi-arrow-left-bold" aria-hidden="true"></span>{{ __('app.back') }}
                </a>
                <button type="submit" class="btn btn-primary">
                    <span class="mdi mdi-check-bold" aria-hidden="true"></span>{{ __('app.save') }}
                </button>
            </div>
        </div>
    </form>

@endsection
