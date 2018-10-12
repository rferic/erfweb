@extends('layouts.auth')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-success">
                    {{ __('Your password has been reseted.') }}
                </div>
                <a href="/" class="btn btn-twitter pull-right">{{ __('Return to home') }}</a>
            </div>
        </div>
    </div>
@endsection
