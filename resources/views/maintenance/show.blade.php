@extends('layouts.menu')

@section('head')
<style>
    .card-body > .container {
        line-height: 3rem;
    }

    .card-footer {
        color: white;
        font-weight: bolder;
    }

    .maintenance {
        margin-right: 20px;
    }
</style>
@endsection

@section('main')

    <div class="mb-3"><h3>{{ __('app.maintenance') }}</h3></div>

    <div class="card text-center">
        <div class="card-body">
            <div class="container">
                <div class="row">
                    <div class="col text-nowrap">
                        <span class="maintenance">{{ __('app.maintenance_is') }}
                            <span class="font-weight-bold {{ $allMuted ? 'text-danger' : 'text-success' }}">
                                {{ __($allMuted ? 'app.enabled' : 'app.disabled') }}
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer {{ $allMuted ? 'bg-danger' : 'bg-success' }}">{{ __($allMuted ? 'app.all_notifications_disabled' : 'app.all_notifications_enabled') }}</div>
    </div>

    <div class="text-center pt-3">
        <a href="javascript:" class="js-Btn btn btn-outline-primary form-control">
            <span class="mdi mdi-18px {{ $allMuted ? 'mdi-power-plug' : 'mdi-power-plug-off' }}"></span>{{ __($allMuted ? 'app.disable' : 'app.enable') }}
        </a>
        <form class="js-Form" method="POST" action="{{ route('maintenance.update') }}">
            @csrf
            <input type="hidden" name="mute_all" value="{{ $allMuted ? '0' : '1' }}">
        </form>
    </div>
@endsection

@section('footer')
<script>
    $(function () {
        // Toggle maintenance button
        $('.js-Btn').on('click', function() {
            if (confirm($(this).text().trim() + ' ?')) {
                $('.js-Form').submit();
            }
        });
    })
</script>
@endsection
