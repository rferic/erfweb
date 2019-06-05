<?php
/**
 * Created by PhpStorm.
 * User: ericrf
 * Date: 01/12/2018
 * Time: 15:52
 */

namespace App\Http\Helpers;


use Arcanedev\Localization\Facades\Localization;

class PageHelper
{
    static function getTypes ()
    {
        return Array(
            'html',
            'apps'
        );
    }

    static function getLayouts ()
    {
        return [
            [
                'key' => 'default',
                'title' => 'Default',
                'options' => [
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

    static function getSlugTranslate ( $url, $lang = null ) {
        if ( is_null($lang) ) {
            $lang = Localization::getCurrentLocale();
        }

        $slug = str_replace(
            Localization::localizeURL(route('home'), $lang),
            '',
            Localization::localizeURL(route($url), $lang)
        );
        return str_replace('/', '', $slug);
    }
}
