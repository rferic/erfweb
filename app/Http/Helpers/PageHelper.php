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
                'title' => 'default',
                'options' => [
                    'width' => [ 'fullwidth', 'container'],
                    'cols' => [ '1', '2', '3', '4' ],
                    'inject' => [
                        'js',
                        'css'
                    ]
                ]
            ],
            [
                'title' => 'home',
                'options' => [
                    'width' => [ 'fullwidth', 'container'],
                    'cols' => [ '1', '2', '3', '4' ],
                    'inject' => [
                        'js',
                        'css'
                    ]
                ]
            ]
        ];
    }
}
