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
                'key' => 'dashboard',
                'name' => __('Dashboard'),
                'icon' => 'ni ni-tv-2 text-default',
                'path' => route('admin.dashboard')
            ],
            [
                'key' => 'messages',
                'name' => __('Messages'),
                'icon' => 'ni ni-email-83 text-orange',
                'path' => null,
                'childrens' => [
                    [
                        'key' => 'messages-inbox',
                        'name' => __('Inbox'),
                        'icon' => null,
                        'path' => route('admin.messages')
                    ],
                    [
                        'key' => 'messages-trash',
                        'name' => __('Trash'),
                        'icon' => null,
                        'path' => route('admin.messages.trash')
                    ]
                ]
            ],
            [
                'key' => 'Web',
                'name' => __('Web'),
                'icon' => 'ni ni-world-2 text-green',
                'path' => null,
                'childrens' => [
                    [
                        'key' => 'pages',
                        'name' => __('Pages'),
                        'icon' => null,
                        'path' => route('admin.pages')
                    ],
                    [
                        'key' => 'apps',
                        'name' => __('Apps'),
                        'icon' => null,
                        'path' => route('admin.apps')
                    ],
                    [
                        'key' => 'menus',
                        'name' => __('Menus'),
                        'icon' => null,
                        'path' => route('admin.menus')
                    ],
                    [
                        'key' => 'redirections',
                        'name' => __('Redirections'),
                        'icon' => null,
                        'path' => route('admin.redirections')
                    ]
                ]
            ],
            [
                'key' => 'Images',
                'name' => __('Images'),
                'icon' => 'ni ni-image text-info',
                'path' => route('admin.images'),
                'childrens' => []
            ],
            [
                'key' => 'Users',
                'name' => __('Users'),
                'icon' => 'ni ni-circle-08 text-blue',
                'path' => route('admin.users'),
                'childrens' => []
            ],
            [
                'key' => 'Admins',
                'name' => __('Admins'),
                'icon' => 'ni ni-key-25 text-yellow',
                'path' => route('admin.admins'),
                'childrens' => []
            ]
        ];
    }
}
