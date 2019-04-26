@extends('layouts.auth')

@section('content')
    <auth-layout
        component="login"
        component-data="{{ json_encode([
            'sessionErrors' => $errors->all(),
            'remember' => old('remember') ? 0 : 1
        ]) }}"
        dusk="login"
    />
@endsection
