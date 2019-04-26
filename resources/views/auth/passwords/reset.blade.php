@extends('layouts.auth')

@section('content')
    <auth-layout component="reset-password" />
@endsection

@section('script')
    <script>
        const token = "{{ $token }}"
    </script>
@endsection
