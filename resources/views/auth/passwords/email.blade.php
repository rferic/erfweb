@extends('layouts.auth')

@section('content')
    <reset-email></reset-email>
@endsection

@section('script')
    <script>
        const statusResetEmail = "{{ isset( Session::all()['status'] ) ? Session::all()['status'] : '' }}"
    </script>
@endsection
