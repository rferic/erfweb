@extends('layouts.auth')

@section('content')
    <login
        session-errors-json="{{ json_encode($errors->all()) }}"
        dusk="login"
        remember-value="{{ old('remember') ? 0 : 1 }}" />
@endsection
