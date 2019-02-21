<?php

namespace App\Http\Helpers;

class MessageHelper
{
    static function getStatusList ()
    {
        return Array (
            Array (
                'key' => 'pending',
                'color' => '#f39c12',
                'class' => 'warning'
            ),
            Array (
                'key' => 'readed',
                'color' => '#007bff',
                'class' => 'primary'
            ),
            Array (
                'key' => 'answered',
                'color' => '#00a65a',
                'class' => 'success'
            )
        );
    }

    static function getTagsList ()
    {
        return Array (
            Array (
                'key' => 'important',
                'color' => '#00a65a',
                'icon' => 'fa-flash',
                'class' => 'warning'
            ),
            Array (
                'key' => 'error',
                'color' => '#f39c12',
                'icon' => 'fa-exclamation',
                'class' => 'danger'
            ),
            Array (
                'key' => 'contact',
                'color' => '#dd4b39',
                'icon' => 'fa-envelope',
                'class' => 'primary'
            )
        );
    }

    static function getIndexViewOptions ( $onlyTrashed = false )
    {
        return [
            'component' => 'index-message',
            'data' => [
                'statusList' => MessageHelper::getStatusList(),
                'tagsList' => MessageHelper::getTagsList(),
                'onlyTrashed' => $onlyTrashed
            ],
            'routes' => [
                'getMessages' => route('admin.messages.get'),
                'createMessage' => route('admin.messages.create')
            ]
        ];
    }

    static function existsKeyInStatusList ( $key )
    {
        $exists = false;

        foreach ( MessageHelper::getStatusList() AS $item ) {
            if ( $item['key'] === $key ) {
                $exists = true;
            }
        }

        return $exists;
    }

    static function existsKeyInTagsList ( $key )
    {
        $exists = false;

        foreach ( MessageHelper::getTagsList() AS $item ) {
            if ( $item['key'] === $key ) {
                $exists = true;
            }
        }

        return $exists;
    }
}
