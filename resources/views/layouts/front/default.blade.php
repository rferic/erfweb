<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ERFWeb Admin') }}</title>

        <!-- Scripts -->
        <script src="{{ asset('front/js/app.js') }}" defer></script>

        <!-- Fonts -->
        <link rel="dns-prefetch" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <link href="{{ asset('front/css/app.css') }}" rel="stylesheet">
    </head>
    <body>
        <div id="app">
            <v-app>
                <v-toolbar app>
                    @if ( isset( $menu ))
                        <index-menu :items="{{ json_encode($menu) }}" />
                    @endif
                </v-toolbar>
                <v-content>
                    <v-container fluid>
                        @yield('content')
                    </v-container>
                </v-content>
                <v-footer app></v-footer>
            </v-app>
        </div>
        <script>
            const locale = "{{ App::getLocale() }}"
            const csrfToken = "{{ csrf_token() }}"
            const routesGlobal = {}
        </script>
        @yield('script')
    </body>
</html>
