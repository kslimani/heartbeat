@extends('layouts.menu')

@section('main')

    <div class="mb-3"><h3>{{ __('app.roles') }}<small class="text-muted"> :: {{ $user->name }}</small></h3></div>

    <div class="pb-3">
        <form method="POST" action="{{ route('user-roles.add', ['user' => $user->id]) }}">
            {{ csrf_field() }}
            <div class="input-group">
                <select name="role" class="custom-select" aria-label="{{ __('app.roles') }}" required>
                    <option></option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
                </select>
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">
                        <span class="mdi mdi-plus" aria-hidden="true"></span>{{ __('app.add') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead class="thead-light">
                <tr>
                    <th scope="col">{{ __('app.name') }}</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
            @foreach ($userRoles as $userRole)
                <tr>
                    <td>{{ $userRole->name }}</td>
                    <td class="td-btn">
                        <form method="POST" action="{{ route('user-roles.remove', ['user' => $user->id, 'role' => $userRole->id]) }}">
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

    <div class="form-group">
        <a href="{{ route('users.index') }}" class="btn btn-secondary" role="button">
            <span class="mdi mdi-arrow-left-bold" aria-hidden="true"></span>{{ __('app.back') }}
        </a>
    </div>

    {{ $userRoles->links() }}

@endsection
