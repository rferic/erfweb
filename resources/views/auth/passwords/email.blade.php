@extends('layouts.auth')

@section('content')
    <auth-layout
        component="reset-email"
        component-data="{{ json_encode([ 'sessionErrors' => $errors->all() ]) }}"
    />
@endsection

@section('script')
    <script>
        const statusResetEmail = "{{ isset( Session::all()['status'] ) ? Session::all()['status'] : '' }}"
    </script>
@endsection
