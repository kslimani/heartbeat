@extends('layouts.menu')

@section('main')

    <div class="mb-3"><h3>{{ __('app.devices') }}</h3></div>

    <div class="table-responsive">
        <table class="table">
            <thead class="thead-light">
                <tr>
                    <th scope="col">{{ __('app.label') }}</th>
                    <th scope="col">{{ __('app.name') }}</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
            @foreach ($devices as $device)
                <tr>
                    <td><a href="{{ route('devices.edit', ['device' => $device->id]) }}">{{ $device->label }}</a></td>
                    <td>{{ $device->name }}</td>
                    <td class="td-btn">
                        <a href="{{ route('devices.edit', ['device' => $device->id]) }}" class="btn btn-link">
                            <span class="mdi mdi-18px mdi-square-edit-outline" aria-hidden="true"></span>
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{ $devices->links() }}

@endsection
