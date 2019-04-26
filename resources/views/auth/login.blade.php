@extends('layouts.auth')

@section('content')
    <auth-layout
        component="login"
        component-data="{{ json_encode([
            'sessionErrors' => json_encode($errors->all()),
            'remember' => old('remember') ? 0 : 1
        ]) }}"
        dusk="login"
    />
@endsection
