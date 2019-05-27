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

    static function existsKeyInStatus ( $key )
    {
        $exists = false;

        foreach ( AppHelper::getStatus() AS $item ) {
            if ( $item['key'] === $key ) {
                $exists = true;
                break;
            }
        }

        return $exists;
    }

    static function existsKeyInTypes ( $key )
    {
        $exists = false;

        foreach ( AppHelper::getTypes() AS $item ) {
            if ( $item['key'] === $key ) {
                $exists = true;
            }
        }

        return $exists;
    }
}
