<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ERFWeb Admin') }}</title>

        <!-- Scripts -->
        <script src="{{ asset('auth/js/app.js') }}" defer></script>

        <!-- Fonts -->
        <link rel="dns-prefetch" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <link href="{{ asset('admin/css/app.css') }}" rel="stylesheet">
        <link href="{{ asset('admin/css/argon.css') }}" rel="stylesheet">
    </head>
    <body class="bg-default">
        <div id="app">
            <main>
                @yield('content')
            </main>
        </div>
        <script>
            const locale = "{{ App::getLocale() }}"
            const routesGlobal = {
                login: "{{ route('login') }}",
                forgottenPassword: "{{ route('password.request')  }}",
                register: "{{ route('register') }}",
                resetEmail: "{{ route('password.email') }}",
                resetPassword: "{{ route('password.update') }}"
            }
            const csrfToken = "{{ csrf_token() }}"
            const links = @json(config('global.links'))
        </script>

        @yield('script')
    </body>
</html>
