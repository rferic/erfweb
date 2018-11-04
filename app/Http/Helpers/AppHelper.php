<?php

namespace App\Http\Helpers;

class AppHelper
{
    static function getTypes ()
    {
        return Array(
            Array(
                'key' => 'public',
                'color' => '#00a65a'
            ),
            Array(
                'key' => 'protected',
                'color' => '#f39c12'
            ),
            Array(
                'key' => 'private',
                'color' => '#dd4b39'
            )
        );
    }
    static function getStatus ()
    {
        return Array(
            Array(
                'key' => 'success',
                'color' => '#00a65a'
            ),
            Array(
                'key' => 'working',
                'color' => '#f39c12'
            ),
            Array(
                'key' => 'error',
                'color' => '#dd4b39'
            )
        );
    }
}
