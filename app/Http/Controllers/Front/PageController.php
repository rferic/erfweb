<?php

namespace App\Http\Controllers\Front;

use App\Http\Helpers\LocalizationHelper;
use App\Http\Helpers\PageHelper;
use App\Models\Core\MenuItem;
use App\Models\Core\PageLocale;
use App\Models\Core\Redirection;
use Arcanedev\Localization\Facades\Localization;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    // Methods print static pages
    public function home ()
    {
        $pageLocale = PageLocale::where('lang', Localization::getCurrentLocaleRegional())
            ->whereHas('page', function ($query) {
                $query->where('is_home', true);
            })
            ->with(['contents'])
            ->first();

        if ( is_null($pageLocale) ) {
            abort(404);
        }

        return $this->printPage($pageLocale);
    }

    public function whoIAm ()
    {
        $pageLocale = PageLocale::where('lang', Localization::getCurrentLocaleRegional())
            ->where('slug', PageHelper::getSlugTranslate('who-i-am', Localization::getCurrentLocaleRegional()))
            ->with(['contents'])
            ->first();

        if ( is_null($pageLocale) ) {
            abort(404);
        }

        return $this->printPage($pageLocale);
    }
    // Method print dynamic pages
    public function index ( $slug )
    {
        // Check has redirection
        $redirection = Redirection::where('slug_origin', $slug)->first();

        if ( !is_null($redirection) ) {
            return redirect($redirection->slug_destine);
        }
        // Check page locale
        $pageLocale= PageLocale::where('lang', Localization::getCurrentLocaleRegional())
            ->where('slug', $slug)
            ->with(['contents'])
            ->first();

        if ( is_null($pageLocale) ) {
            abort(404);
        }
        // Print page
        return $this->printPage($pageLocale);
    }

    private function printPage ( PageLocale $page ) {
        $menu = $this->getMenu();
        $auth = $this->getAuth();
        $localesSupported = LocalizationHelper::getSupportedFormatted();
        $pageTranslates = $this->getTranslates($page);

        return view('front/' . $page->layout, compact('menu', 'page', 'auth', 'localesSupported', 'pageTranslates'));
    }

    private function getMenu ()
    {
        return MenuItem::whereHas('menu', function ( $query ) {
            $query->where('is_default', true);
        })
        ->where('lang', Localization::getCurrentLocaleRegional())
        ->with('pageLocale')
        ->orderBy('priority')->get()->toArray();
    }

    private function getAuth ()
    {
        $auth = null;
        $user = Auth::user();

        if ( !is_null($user) ) {
            $auth = $user->toArray();
            $auth['roles'] = $user->roles->pluck('name')->toArray();
        }

        return $auth;
    }

    private function getTranslates ( PageLocale $page )
    {
        $localesSupported = LocalizationHelper::getSupportedFormatted();
        $translates = [];

        if ( !$page->page->is_home ) {
            $locale = null;
            $pageTranslates = PageLocale::where('page_id', $page->page_id)->where('lang', '<>', $page->lang)->with(['page'])->get();

            foreach ($localesSupported AS $localeSupported) {
                $find = false;

                foreach ($pageTranslates AS $pageTranslate) {
                    if ( $localeSupported['iso'] === $pageTranslate->lang ) {
                        $find = true;
                        $translates[] = [
                            'locale' => $localeSupported,
                            'url' => Localization::getLocalizedURL($localeSupported['code'], $pageTranslate->slug)
                        ];
                        break;
                    }
                }

                if ( !$find ) {
                    $translates[] = [
                        'locale' => $localeSupported,
                        'url' => Localization::localizeURL(route('home'), $localeSupported['code'])
                    ];
                }
            }
        } else {
            foreach ( $localesSupported AS $localeSupported ) {
                $translates[] = [
                    'locale' => $localeSupported,
                    'url' => Localization::localizeURL(route('home'), $localeSupported['code'])
                ];
            }
        }

        return $translates;
    }
}
