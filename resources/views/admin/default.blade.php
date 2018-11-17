@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-header">{{ $title }}</div>

        <div class="card-body">
            <{{ $component }} data="{{ isset($data) ? json_encode($data) : null }}" />
        </div>
    </div>
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
