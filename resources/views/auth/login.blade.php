@extends('layouts.auth')

@section('content')
    <login remember-value="{{ old('remember') ? 0 : 1 }}"></login>
@endsection
