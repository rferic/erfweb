@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-body">
            <h1 class="box-title">{{ $title }}</h1>
            <{{ $component }}
                class="mt-4"
                data="{{ isset($data) ? json_encode($data) : null }}"
            />
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
