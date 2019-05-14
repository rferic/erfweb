@extends('layouts.front.default')

@section('content')
    <home-layout />
@endsection

@section('script')
    @if ( isset( $routes ))
        <script>
            const routes = {
                @foreach ( $routes as $key => $route )
                '{{ $key }}': '{{ $route  }}',
                @endforeach
            }
        </script>
    @endif
@endsection
