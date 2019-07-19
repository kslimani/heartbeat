@extends('layouts.menu')

@section('head')
<style>
    .td-lbl {
        line-height: 1;
    }

    label {
        margin-right: .5rem;
        margin-bottom: 0rem;
    }
</style>
@endsection

@section('main')

    <div class="mb-3"><h3>{{ __('app.services_statuses') }}<small class="text-muted"> :: {{ $user->name }}</small></h3></div>

    <div class="pb-3">
        <!-- Attach service form -->
        <form class="js-Attach" method="POST" action="{{ route('user-service-statuses.attach', ['user' => $user->id]) }}">
            {{ csrf_field() }}
            <input class="js-Attach-Id" type="hidden" name="service_status_id" value="">
            <input class="js-Attach-IsUpdatable" type="hidden" name="is_updatable" value="">
            <input class="js-Attach-IsMute" type="hidden" name="is_mute" value="">
            <div class="input-group">
                <input class="js-Attach-Search form-control" type="text" autocomplete="off" required>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <label class="mdi mdi-check-network-outline" for="updatableCb"></label>
                        <input class="js-Attach-Cb-Updatable" type="checkbox" id="updatableCb">
                    </div>
                    <div class="input-group-text">
                        <label class="mdi mdi-bell-outline" for="muteCb"></label>
                        <input class="js-Attach-Cb-Mute" type="checkbox" id="muteCb" checked>
                    </div>
                    <button class="btn btn-primary" type="submit">
                        {{ __('app.add') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead class="thead-light">
                <tr>
                    <th scope="col">{{ __('app.device') }}</th>
                    <th scope="col">{{ __('app.service') }}</th>
                    <th scope="col"><span class="mdi mdi-18px mdi-check-network-outline" data-toggle="tooltip" data-placement="top" title="{{ __('app.can_update') }}"></span></th>
                    <th scope="col"><span class="mdi mdi-18px mdi-bell-outline" data-toggle="tooltip" data-placement="top" title="{{ __('app.notifications') }}"></th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
            @foreach ($serviceStatuses as $serviceStatus)
                <tr class="text-nowrap">
                    <td class="td-lbl">{{ $serviceStatus->device->label }}<br /><small class="text-muted">{{ $serviceStatus->device->name }}</small></td>
                    <td class="td-lbl">{{ $serviceStatus->service->label }}<br /><small class="text-muted">{{ $serviceStatus->service->name }}</small></td></td>
                    <td class="td-btn">
                        <form method="POST" action="{{ route('user-service-statuses.update', ['user' => $user->id, 'id' => $serviceStatus->id]) }}">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}
                            <input type="hidden" name="is_updatable" value="{{ $serviceStatus->pivot->is_updatable ? 0 : 1 }}">
                            <button class="btn btn-link" type="submit" onclick="return confirm('{{ __('app.msg_confirm') }}')">
                                <span class="mdi mdi-18px {{ $serviceStatus->pivot->is_updatable ? 'mdi-check-bold text-success' : 'mdi-block-helper text-danger' }}" aria-hidden="true"></span>
                            </button>
                        </form>
                    </td>
                    <td class="td-btn">
                        <form method="POST" action="{{ route('user-service-statuses.update', ['user' => $user->id, 'id' => $serviceStatus->id]) }}">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}
                            <input type="hidden" name="is_mute" value="{{ $serviceStatus->pivot->is_mute ? 0 : 1 }}">
                            <button class="btn btn-link" type="submit" onclick="return confirm('{{ __('app.msg_confirm') }}')">
                                <span class="mdi mdi-18px {{ $serviceStatus->pivot->is_mute ? 'mdi-block-helper text-danger' : 'mdi-check-bold text-success' }}" aria-hidden="true"></span>
                            </button>
                        </form>
                    </td>
                    <td class="td-btn">
                        <form method="POST" action="{{ route('user-service-statuses.detach', ['user' => $user->id, 'id' => $serviceStatus->id]) }}">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                            <button class="btn btn-link" type="submit" onclick="return confirm('{{ __('app.detach_service_confirm') }}')">
                                <span class="mdi mdi-18px mdi-close" aria-hidden="true"></span>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="text-right">
        <!-- Synchronize with user form -->
        <form class="js-Sync" method="POST" action="{{ route('user-service-statuses.sync', ['user' => $user->id]) }}">
            {{ csrf_field() }}
            <input class="js-Sync-UserId" type="hidden" name="with_user" value="">
            <div class="input-group">
                <input class="js-Sync-Search form-control form-control-sm" type="text" autocomplete="off" required>
                <div class="input-group-append">
                    <button class="btn btn-sm btn-outline-primary" type="submit">
                        {{ __('app.sync_with_user') }}
                    </button>
                </div>
            </div>
        </form>

        <!-- Attach all services form -->
        <form class="mt-1" method="POST" action="{{ route('user-service-statuses.attachall', ['user' => $user->id]) }}">
            {{ csrf_field() }}
            <button type="submit" class="btn btn-sm btn-outline-primary" onclick="return confirm('{{ __('app.msg_confirm') }}')">{{ __('app.add_all_services') }}</button>
        </form>
    </div>

    <div class="form-group">
        <a href="{{ route('users.index') }}" class="btn btn-secondary" role="button">
            <span class="mdi mdi-arrow-left-bold" aria-hidden="true"></span>{{ __('app.back') }}
        </a>
    </div>

    {{ $serviceStatuses->links() }}

@endsection

@section('footer')
<script>
    $(function () {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Attach form input values handler
        var updateAttach = function(evt) {
            var id = $('.js-Attach-Id').val();
            $('.js-Attach-IsUpdatable').val($('.js-Attach-Cb-Updatable').prop('checked') ? '1' : '0');
            $('.js-Attach-IsMute').val($('.js-Attach-Cb-Mute').prop('checked') ? '0' : '1');

            if (id && id.length > 0) {
                return true;
            }

            evt && $('.js-Attach-Search').focus();
            return false;
        };

        // Initialize autocomplete text input
        var searchUrl = '{{ route('service-statuses.search') }}';
        $('.js-Attach-Search').typeahead({
            delay: 800,
            minLength: 3,
            items: 'all',
            source:  function (term, process) {
                return $.get(searchUrl, {term: term}, function (data) {
                    // FIXME: no suggestion if single result
                    return process(data);
                });
            },
            afterSelect: function(item) {
                $('.js-Attach-Id').val(item.id);
            },
        });

        // Sync form input values handler
        var updateSync = function(evt) {
            var id = $('.js-Sync-UserId').val();

            if (id && id.length > 0) {
                return true;
            }

            evt && $('.js-Sync-Search').focus();
            return false;
        };

        // Initialize autocomplete text input (user)
        var userSearchUrl = '{{ route('users.search') }}';
        $('.js-Sync-Search').typeahead({
            delay: 800,
            minLength: 3,
            items: 'all',
            source:  function (term, process) {
                return $.get(userSearchUrl, {term: term}, function (data) {
                    // FIXME: no suggestion if single result
                    return process(data);
                });
            },
            afterSelect: function(item) {
                $('.js-Sync-UserId').val(item.id);
            },
        });

        // Handle form submit events
        $('.js-Attach').submit(updateAttach);
        $('.js-Sync').submit(updateSync);

        // Initialize forms
        updateAttach();
        updateSync();
    });
</script>
@endsection
