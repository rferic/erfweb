<?php
/**
 * Created by PhpStorm.
 * User: ericrf
 * Date: 12/11/2018
 * Time: 20:08
 */

namespace App\Http\Helpers;


class RoleHelper
{
    static function getRoles ()
    {
        return Array(
            'public',
            'admin'
        );
    }
}
