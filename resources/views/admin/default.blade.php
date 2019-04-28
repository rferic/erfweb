@extends('layouts.admin')

@section('content')
    <admin-layout
        component="{{ $component }}"
        title-page="{{ $title }}"
        data="{{ isset($data) ? json_encode($data) : null }}"
    />
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
