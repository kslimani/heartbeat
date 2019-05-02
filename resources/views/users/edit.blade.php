@extends('layouts.menu')

@section('main')

    <div class="mb-3"><h3>{{ __('app.users_edit') }}</h3></div>

    <form method="POST" action="{{ route('users.update', ['user' => $user->id]) }}">
        @method('PUT')
        @csrf

        <div class="form-group row">
            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('app.name') }}</label>

            <div class="col-md-6">
                <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name" autofocus>

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
                <a href="{{ route('users.index') }}" class="btn btn-secondary" role="button">
                    <span class="mdi mdi-arrow-left-bold" aria-hidden="true"></span>{{ __('app.back') }}
                </a>
                <button type="submit" class="btn btn-primary">
                    <span class="mdi mdi-check-bold" aria-hidden="true"></span>{{ __('app.save') }}
                </button>
            </div>
        </div>
    </form>

@endsection
