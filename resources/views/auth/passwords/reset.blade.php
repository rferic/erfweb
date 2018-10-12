@extends('layouts.auth')

@section('content')
    <reset-password></reset-password>
@endsection

@section('script')
    <script>
        const token = "{{ $token }}"
    </script>
@endsection
