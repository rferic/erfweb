@extends('layouts.auth')

@section('content')
    <auth-layout
        component="register"
        component-data="{{ json_encode([ 'sessionErrors' => $errors->all() ]) }}"
    />
@endsection
