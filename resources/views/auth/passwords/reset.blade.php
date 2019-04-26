@extends('layouts.auth')

@section('content')
    <auth-layout
        component="reset-password"
        component-data="{{ json_encode([ 'sessionErrors' => $errors->all() ]) }}"
    />
@endsection

@section('script')
    <script>
        const token = "{{ $token }}"
    </script>
@endsection
