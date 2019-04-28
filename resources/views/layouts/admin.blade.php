<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ERFWeb Admin') }}</title>

        <!-- Scripts -->
        <script src="{{ asset('admin/js/app.js') }}" defer></script>

        <!-- Fonts -->
        <link rel="dns-prefetch" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <link href="{{ asset('admin/css/app.css') }}" rel="stylesheet">
        <link href="{{ asset('admin/css/argon.css') }}" rel="stylesheet">
    </head>
    <body class="fix-header fix-sidebar mini-sidebar">
        <div id="app">
            @yield('content')
        </div>
        <script>
            const locale = "{{ App::getLocale() }}"
            const csrfToken = "{{ csrf_token() }}"
            const routesGlobal = {
                current: "{{ Request::url() }}",
                logout: "{{ route('logout') }}",
                dashboard: "{{ route('admin.dashboard') }}",
                adminMenu: "{{ route('admin.adminMenu') }}",
                web: "{{ route('home') }}",
                profile: {
                    index: "{{ route('admin.profile') }}",
                    getData: "{{ route('admin.profile.getData') }}"
                },
                messages: {
                    index: "{{ route('admin.messages') }}",
                    getState: "{{ route('admin.messages.getState') }}",
                    getLastPendingMessages: "{{ route('admin.messages.getLastPending') }}"
                }
            }
        </script>
        @yield('script')
    </body>
</html>
