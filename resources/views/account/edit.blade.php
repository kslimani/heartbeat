@extends('layouts.menu')

@section('main')

    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link {{ old('tab', $tab) === 'settings' ? 'active' : ''}}" id="settings-tab" data-toggle="tab" href="#settings" role="tab" aria-controls="settings" aria-selected="true">{{ __('app.settings') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ old('tab', $tab) === 'profile' ? 'active' : ''}}" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">{{ __('app.profile') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ old('tab', $tab) === 'security' ? 'active' : ''}}" id="security-tab" data-toggle="tab" href="#security" role="tab" aria-controls="security" aria-selected="false">{{ __('app.security') }}</a>
        </li>
    </ul>
    <div class="tab-content mt-4" id="myTabContent">
        <div class="tab-pane fade {{ old('tab', $tab) === 'settings' ? 'show active' : ''}}" id="settings" role="tabpanel" aria-labelledby="settings-tab">

            <!-- SETTINGS -->

            <form method="POST" action="{{ route('account-settings.update') }}">
                @method('PUT')
                @csrf
                <input type="hidden" name="tab" value="settings">

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

        </div>
        <div class="tab-pane fade {{ old('tab', $tab) === 'profile' ? 'show active' : ''}}" id="profile" role="tabpanel" aria-labelledby="profile-tab">

            <!-- PROFILE -->

            <form method="POST" action="{{ route('account-profile.update') }}">
                @method('PUT')
                @csrf
                <input type="hidden" name="tab" value="profile">

                <div class="form-group row">
                    <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('app.name') }}</label>

                    <div class="col-md-6">
                        <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name">

                        @if ($errors->has('name'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('app.email') }}</label>

                    <div class="col-md-6">
                        <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email">

                        @if ($errors->has('email'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('email') }}</strong>
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

        </div>
        <div class="tab-pane fade {{ old('tab', $tab) === 'security' ? 'show active' : ''}}" id="security" role="tabpanel" aria-labelledby="security-tab">

            <!-- SECURITY -->

            <form method="POST" action="{{ route('account-password.update') }}">
                @method('PUT')
                @csrf
                <input type="hidden" name="tab" value="security">

                <div class="form-group row">
                    <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('app.password') }}</label>

                    <div class="col-md-6">
                        <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required autocomplete="new-password">

                        @if ($errors->has('password'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('app.password_confirm') }}</label>

                    <div class="col-md-6">
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
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

        </div>
    </div>

@endsection
