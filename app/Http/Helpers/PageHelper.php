<?php
/**
 * Created by PhpStorm.
 * User: ericrf
 * Date: 01/12/2018
 * Time: 15:52
 */

namespace App\Http\Helpers;


class PageHelper
{
    static function getLayouts ()
    {
        return [
            [
                'key' => 'default',
                'title' => 'Default',
                'options' => [
                    'width' => [ 'fullwidth', 'container'],
                    'menu' => true,
                    'footer' => true,
                    'inject' => [
                        'js',
                        'css'
                    ]
                ]
            ],
            [
                'key' => 'home',
                'title' => 'Home',
                'options' => [
                    'width' => [ 'fullwidth', 'container'],
                    'menu' => true,
                    'footer' => true,
                    'inject' => [
                        'js',
                        'css'
                    ]
                ]
            ],
            [
                'key' => 'landing',
                'title' => 'Landing',
                'options' => [
                    'width' => [ 'fullwidth' ],
                    'menu' => false,
                    'footer' => true,
                    'inject' => [
                        'js',
                        'css'
                    ]
                ]
            ]
        ];
    }
}
