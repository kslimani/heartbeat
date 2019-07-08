@extends('layouts.menu')

@section('main')

    <div class="mb-3"><h3>{{ __('app.settings') }}</h3></div>

    <form method="POST" action="{{ route('account-settings.update') }}">
        @method('PUT')
        @csrf

        <div class="form-group row">
            <label for="locale" class="col-md-4 col-form-label text-md-right">{{ __('app.language') }}</label>

            <div class="col-md-6">
                <select class="custom-select" name="locale" required>
                    @foreach ($locales as $locale => $label)
                    <option value="{{ $locale }}"{{ isset($settings['locale']) && $settings['locale'] === $locale ? ' selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>

                @if ($errors->has('locale'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('locale') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group row">
            <label for="tz" class="col-md-4 col-form-label text-md-right">{{ __('app.timezone') }}</label>

            <div class="col-md-6">
                <select class="custom-select" name="tz" required>
                    @foreach ($timezones as $tz => $name)
                    <option value="{{ $tz }}"{{ isset($settings['tz']) && $settings['tz'] === $tz ? ' selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>

                @if ($errors->has('tz'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('tz') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group row mb-0">
            <div class="col-md-6 offset-md-4">
                <button type="submit" class="btn btn-primary">
                    <span class="mdi mdi-check-bold" aria-hidden="true"></span>{{ __('app.save') }}
                </button>
            </div>
        </div>
    </form>

@endsection
