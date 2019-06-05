<?php

namespace App\Http\Middleware;

use App\Http\Helpers\LocalizationHelper;
use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class RefreshLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ( Auth::check() ) {
            $localesSupported = LocalizationHelper::getSupportedFormatted();

            foreach ( $localesSupported AS $localeSupported ) {
                if ( Auth::user()->lang === $localeSupported['iso'] ) {
                    App::setLocale($localeSupported['code']);
                }
            }
        }

        return $next($request);
    }
}
