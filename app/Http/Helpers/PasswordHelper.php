<?php
namespace App\Http\Helpers;

class PasswordHelper
{
    static function validate () {
        return 'required|string|min:6|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/|confirmed';
    }
}
