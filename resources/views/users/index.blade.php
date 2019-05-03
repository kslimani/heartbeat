@extends('layouts.menu')

@section('main')

    <div class="mb-3"><h3>{{ __('app.users') }}</h3></div>

    <div class="table-responsive">
        <table class="table">
            <thead class="thead-light">
                <tr>
                    <th scope="col">{{ __('app.name') }}</th>
                    <th scope="col">{{ __('app.email') }}</th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
            @foreach ($users as $user)
                <tr>
                    <td><a href="{{ route('users.edit', ['user' => $user->id]) }}">{{ $user->name }}</a></td>
                    <td>{{ $user->email }}</td>
                    <td class="td-btn">
                        <a href="{{ route('users.edit', ['user' => $user->id]) }}" class="btn btn-link">
                            <span class="mdi mdi-18px mdi-square-edit-outline" aria-hidden="true"></span>
                        </a>
                    </td>
                    <td class="td-btn">
                        <a href="{{ route('user-keys.index', ['user' => $user->id]) }}" class="btn btn-link">
                            <span class="mdi mdi-18px mdi-account-key" aria-hidden="true"></span>
                        </a>
                    </td>
                    <td class="td-btn">
                        <a href="{{ route('user-roles.index', ['user' => $user->id]) }}" class="btn btn-link">
                            <span class="mdi mdi-18px mdi-account-multiple" aria-hidden="true"></span>
                        </a>
                    </td>
                    <td class="td-btn">
                        <form method="POST" action="{{ route('users.destroy', ['user' => $user->id]) }}">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                            <button class="btn btn-link" type="submit" onclick="return confirm('{{ __('app.msg_confirm') }}')">
                                <span class="mdi mdi-18px mdi-trash-can-outline" aria-hidden="true"></span>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="form-group">
        <a class="btn btn-primary" href="{{ route('users.create') }}" role="button">
            <span class="mdi mdi-account-plus" aria-hidden="true"></span>{{ __('app.create') }}
        </a>
    </div>

    {{ $users->links() }}

@endsection
