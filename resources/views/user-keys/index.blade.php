@extends('layouts.menu')

@section('main')

    <div class="mb-3"><h3>{{ __('app.authorized_keys') }}<small class="text-muted"> :: {{ $user->name }}</small></h3></div>

    <div class="table-responsive">
        <table class="table">
            <thead class="thead-light">
                <tr>
                    <th scope="col">{{ __('app.key') }}</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
            @foreach ($keys as $key)
                <tr>
                    <td>{{ $key->data }}</td>
                    <td class="td-btn">
                        <form method="POST" action="{{ route('user-keys.destroy', ['user' => $user->id, 'key' => $key->id]) }}">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                            <button class="btn btn-link" type="submit" onclick="return confirm('{{ __('app.msg_confirm') }}')">
                                <span class="mdi mdi-18px mdi-close" aria-hidden="true"></span>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="form-group form-inline">
        <a href="{{ route('users.index') }}" class="btn btn-secondary" role="button">
            <span class="mdi mdi-arrow-left-bold" aria-hidden="true"></span>{{ __('app.back') }}
        </a>
        <form class="pl-1" method="POST" action="{{ route('user-keys.generate', ['user' => $user->id]) }}">
            {{ csrf_field() }}
            <button class="btn btn-primary" type="submit">
                <span class="mdi mdi-key-plus" aria-hidden="true"></span>{{ __('app.generate') }}
            </button>
        </form>
    </div>

    {{ $keys->links() }}

@endsection
