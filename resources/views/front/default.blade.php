@extends('layouts.front.default')

@section('content')
    @foreach ($page->contents as $content)
        <div id="{{ $content->id_html }}" class="{{ $content->class_html }}">
            {!! $content->text !!}
        </div>
    @endforeach
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

    @if (isset(json_decode($page->options)->css))
        <script>
            {!! json_decode($page->options)->js !!}
        </script>
    @endif

    @foreach ($page->contents as $content)
        {!! $content->footer_inject !!}
    @endforeach
@endsection
