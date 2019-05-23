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

        @if (isset(json_decode($page->options)->css))
            {!! json_decode($page->options)->css !!}
        @endif

        @foreach ($page->contents as $content)
            {!! $content->header_inject !!}
        @endforeach
    </head>
    <body>
        <div id="app">
            <v-app v-cloak light>
                <v-navigation-drawer
                    fixed
                    v-model="drawer"
                    left
                    app
                    :disable-resize-watcher="true"
                >
                    @if ( isset( $menu ))
                        <mobile-menu :items="{{ json_encode($menu) }}" />
                    @endif
                </v-navigation-drawer>
                <v-toolbar color="grey darken-4" dark fixed app class="toolbar">
                    <v-toolbar-side-icon class="hidden-md-and-up" @click.stop="drawer = !drawer"></v-toolbar-side-icon>
                    <a href="{{ localization()->localizeURL('/') }}" class="icon">
                        <span class="white--text">< <strong>ERF</strong>Web /></span>
                    </a>
                    @if ( isset( $menu ))
                        <v-spacer></v-spacer>
                        <toolbar-menu :items="{{ json_encode($menu) }}" auth-json="{{ json_encode($auth) }}" />
                    @endif
                </v-toolbar>
                <v-content>
                    <v-container fluid fill-height class="pa-0">
                            <v-layout>
                            @yield('content')
                        </v-layout>
                    </v-container>
                </v-content>
                <auth-index />
                <index-footer />
            </v-app>
        </div>
        <script>
            const locale = "{{ App::getLocale() }}"
            const csrfToken = "{{ csrf_token() }}"
            const routesGlobal = {
                account: "{{ localization()->localizeURL(route('account')) }}",
                technologies: "{{ localization()->localizeURL(route('technologies')) }}",
                logout: "{{ route('logout') }}",
                login: "{{ route('login') }}",
                loginAjax: "{{ route('login-ajax') }}",
                register: "{{ route('register') }}",
                registerAjax: "{{ route('register-ajax') }}",
                emailIsFree: "{{ route('email-is-free') }}",
                sendMessage: "{{ route('send-message') }}",
                translates: @json($pageTranslates)
            }
            const localesSupported = @json($localesSupported);
        </script>
        @yield('script')
    </body>
</html>
