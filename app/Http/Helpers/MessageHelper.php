<?php

namespace App\Http\Helpers;

class MessageHelper
{
    static function getStatusList ()
    {
        return Array (
            Array (
                'key' => 'pending',
                'color' => '#00a65a'
            ),
            Array (
                'key' => 'readed',
                'color' => '#f39c12'
            ),
            Array (
                'key' => 'answered',
                'color' => '#dd4b39'
            )
        );
    }

    static function getTagsList ()
    {
        return Array (
            Array (
                'key' => 'important',
                'color' => '#00a65a',
                'icon' => 'fa-start',
                'class' => 'btn-warning'
            ),
            Array (
                'key' => 'error',
                'color' => '#f39c12',
                'icon' => 'fa-exclamation',
                'class' => 'btn-danger'
            ),
            Array (
                'key' => 'contact',
                'color' => '#dd4b39',
                'icon' => 'fa-envelope',
                'class' => 'btn-primary'
            )
        );
    }
}
