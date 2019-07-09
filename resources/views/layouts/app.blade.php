<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Heartbeat') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @yield('head')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
            <div class="container">
                <a class="navbar-logo" href="{{ Auth::check() ? route('home') : url('/') }}">
                    <!-- Logo -->
                </a>
                <a class="navbar-brand" href="{{ Auth::check() ? route('home') : url('/') }}">
                    {{ config('app.name', 'Heartbeat') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('app.nav_toggle') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('app.login') }}</a>
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('account-settings') }}">
                                        <span class="mdi mdi-settings" aria-hidden="true"></span>{{ __('app.settings') }}
                                    </a>

                                    @if (Auth::user()->isAdmin())
                                        @if (Route::has('register'))
                                        <a class="dropdown-item" href="{{ route('register') }}">
                                            <span class="mdi mdi-account-plus" aria-hidden="true"></span>{{ __('app.register') }}
                                        </a>
                                        @endif
                                        <a class="dropdown-item" href="{{ route('users.index') }}">
                                            <span class="mdi mdi-account-group" aria-hidden="true"></span>{{ __('app.users') }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('devices.index') }}">
                                            <span class="mdi mdi-server-network" aria-hidden="true"></span>{{ __('app.devices') }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('services.index') }}">
                                            <span class="mdi mdi-network-outline" aria-hidden="true"></span>{{ __('app.services') }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('maintenance.show') }}">
                                            <span class="mdi mdi-power-plug" aria-hidden="true"></span>{{ __('app.maintenance') }}
                                        </a>
                                    @endif

                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <span class="mdi mdi-logout" aria-hidden="true"></span>{{ __('app.logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
@yield('footer')
</body>
</html>
