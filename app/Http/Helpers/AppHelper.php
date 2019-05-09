<?php

namespace App\Http\Helpers;

class AppHelper
{
    static function getTypes ()
    {
        return Array(
            Array(
                'key' => 'public',
                'color' => '#00a65a',
                'class' => 'success'
            ),
            Array(
                'key' => 'protected',
                'color' => '#f39c12',
                'class' => 'warning'
            ),
            Array(
                'key' => 'private',
                'color' => '#dd4b39',
                'class' => 'danger'
            )
        );
    }
    static function getStatus ()
    {
        return Array(
            Array(
                'key' => 'success',
                'color' => '#00a65a',
                'class' => 'success'
            ),
            Array(
                'key' => 'working',
                'color' => '#f39c12',
                'class' => 'waring'
            ),
            Array(
                'key' => 'error',
                'color' => '#dd4b39',
                'class' => 'danger'
            )
        );
    }
}
