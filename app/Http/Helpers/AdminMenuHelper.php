<?php
/**
 * Created by PhpStorm.
 * User: ericrf
 * Date: 01/12/2018
 * Time: 15:52
 */

namespace App\Http\Helpers;


class AdminMenuHelper
{
    static function getMenu ()
    {
        return [
            [
                'key' => 'messages',
                'label' => __('Messages'),
                'icon' => 'fa-envelope',
                'url' => null,
                'childrens' => [
                    [
                        'key' => 'messages-inbox',
                        'label' => __('Inbox'),
                        'icon' => null,
                        'url' => route('admin.messages')
                    ],
                    [
                        'key' => 'messages-trash',
                        'label' => __('Trash'),
                        'icon' => null,
                        'url' => route('admin.messages.trash')
                    ]
                ]
            ],
            [
                'key' => 'Web',
                'label' => __('Web'),
                'icon' => 'fa-mixcloud',
                'url' => null,
                'childrens' => [
                    [
                        'key' => 'pages',
                        'label' => __('Pages'),
                        'icon' => null,
                        'url' => route('admin.pages')
                    ],
                    [
                        'key' => 'apps',
                        'label' => __('Apps'),
                        'icon' => null,
                        'url' => route('admin.apps')
                    ]
                ]
            ],
            [
                'key' => 'Images',
                'label' => __('Images'),
                'icon' => 'fa-image',
                'url' => route('admin.images'),
                'childrens' => []
            ]
        ];
    }
}
