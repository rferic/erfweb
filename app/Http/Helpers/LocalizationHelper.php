<?php


namespace App\Http\Helpers;


class LocalizationHelper
{
    static function getSupportedFormatted ()
    {
        $localesSupportedFormatted = [];
        $supportedLocales = config('localization.supported-locales');
        $locales = config('localization.locales');

        foreach ( $supportedLocales AS $supportedLocale ) {
            $localesSupportedFormatted[] = [
                'name' => $locales[$supportedLocale]['name'],
                'code' => $supportedLocale,
                'iso' => $locales[$supportedLocale]['regional'],
                'default' => $supportedLocale === config('app.locale')
            ];
        }

        return $localesSupportedFormatted;
    }

    static function getSupportedRegional ()
    {
        $supported = [];
        $supportedLocales = config('localization.supported-locales');
        $locales = config('localization.locales');

        foreach ( $supportedLocales AS $supportedLocale ) {
            $supported[] = $locales[$supportedLocale]['regional'];
        }

        return $supported;
    }

    static function getSupportedItem ( $key, $value )
    {
        $supportedLocales = self::getSupportedFormatted();

        foreach ( $supportedLocales AS $supportedLocale ) {
            if ( $supportedLocale[$key] === $value ) {
                return $supportedLocale;
            }
        }

        return null;
    }
}
