<?php

namespace App\Http\Helpers;

use Illuminate\Support\Facades\Auth;

class AuthHelper
{
    static function getAuth ()
    {
        $auth = null;
        $user = Auth::user();

        if ( !is_null($user) ) {
            $auth = $user->toArray();
            $auth['roles'] = $user->roles->pluck('name')->toArray();
        }

        return $auth;
    }
}
