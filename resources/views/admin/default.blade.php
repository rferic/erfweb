@extends('layouts.admin')

@section('content')
    <admin-layout
        component="{{ $component }}"
        title-page="{{ $title }}"
        @isset($description)
            description-page="{{ $description }}"
        @endisset
        @isset($data)
            data="{{ json_encode($data) }}"
        @endisset
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
