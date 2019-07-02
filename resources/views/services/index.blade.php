@extends('layouts.menu')

@section('main')

    <div class="mb-3"><h3>{{ __('app.services') }}</h3></div>

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
            @foreach ($services as $service)
                <tr>
                    <td><a href="{{ route('services.edit', ['service' => $service->id]) }}">{{ $service->label }}</a></td>
                    <td>{{ $service->name }}</td>
                    <td class="td-btn">
                        <a href="{{ route('services.edit', ['service' => $service->id]) }}" class="btn btn-link">
                            <span class="mdi mdi-18px mdi-square-edit-outline" aria-hidden="true"></span>
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{ $services->links() }}

@endsection
