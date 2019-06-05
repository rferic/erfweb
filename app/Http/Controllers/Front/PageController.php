<?php

namespace App\Http\Controllers\Front;

use App\Http\Helpers\AuthHelper;
use App\Http\Helpers\ImageHelper;
use App\Http\Helpers\LocalizationHelper;
use App\Http\Helpers\PageHelper;
use App\Http\Helpers\UserHelper;
use App\Models\Core\MenuItem;
use App\Models\Core\PageLocale;
use App\Models\Core\Redirection;
use Arcanedev\Localization\Facades\Localization;
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
        $slug = 'who-i-am';
        // Check has redirection
        $redirection = Redirection::where('slug_origin', $slug)->first();

        if ( !is_null($redirection) ) {
            return redirect($redirection->slug_destine);
        }

        $pageLocale = PageLocale::where('lang', Localization::getCurrentLocaleRegional())
            ->where('slug', PageHelper::getSlugTranslate($slug))
            ->with(['contents'])
            ->first();

        if ( is_null($pageLocale) ) {
            abort(404);
        }

        return $this->printPage($pageLocale);
    }

    public function account ()
    {
        $slug = 'account';
        // Check has redirection
        $redirection = Redirection::where('slug_origin', $slug)->first();

        if ( !is_null($redirection) ) {
            return redirect($redirection->slug_destine);
        }

        $pageLocale = PageLocale::where('lang', Localization::getCurrentLocaleRegional())
            ->where('slug', PageHelper::getSlugTranslate($slug))
            ->with(['contents'])
            ->first();

        $params = [
            'routes' => [
                'updateBaseUser' => route('update-base-user'),
                'updatePasswordUser' => route('update-password-user'),
                'uploadImageTemporal' => route('upload-image-temporal'),
                'deleteImageTemporal' => route('delete-image-temporal')
            ],
            'requireLogged' => true,
            'pageData' => [
                'avatars' => UserHelper::getAvatars(),
                'imageMetadata' => ImageHelper::getMetadata(Auth::user()->avatar)
            ]
        ];

        if ( is_null($pageLocale) ) {
            abort(404);
        }

        return $this->printPage($pageLocale, $params);
    }

    public function apps ()
    {
        $slug = 'apps';
        // Check has redirection
        $redirection = Redirection::where('slug_origin', $slug)->first();

        if ( !is_null($redirection) ) {
            return redirect($redirection->slug_destine);
        }

        $pageLocale = PageLocale::where('lang', Localization::getCurrentLocaleRegional())
            ->where('slug', PageHelper::getSlugTranslate($slug))
            ->with(['contents'])
            ->first();

        $params = [
            'routes' => [],
            'requireLogged' => false,
            'pageData' => []
        ];

        if ( is_null($pageLocale) ) {
            abort(404);
        }

        return $this->printPage($pageLocale, $params);
    }

    public function app ( $slug )
    {
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
            ->whereHas('page', function ( $query ) {
                $query->where('type', 'html');
            })
            ->where('slug', $slug)
            ->with(['contents'])
            ->first();

        if ( is_null($pageLocale) ) {
            abort(404);
        }
        // Print page
        return $this->printPage($pageLocale);
    }

    private function printPage ( PageLocale $page, $params = [] ) {
        $menu = $this->getMenu();
        $auth = AuthHelper::getAuth();
        $homeRoute = Localization::localizeURL(route('home'));
        $pageTranslates = $this->getTranslates($page);
        $routes = isset($params['routes']) ? $params['routes'] : [];
        $requireLogged = isset($params['requireLogged']) ? $params['requireLogged'] : false;
        $pageData = isset($params['pageData']) ? $params['pageData'] : [];
        $isAdmin = Auth::check() ? Auth::user()->isAdmin() : false;
        $myApps = Auth::check() ? Auth::user()->apps : [];

        return view(
            'front/' . $page->layout,
            compact(
                'menu',
                'page',
                'auth',
                'pageTranslates',
                'routes',
                'requireLogged',
                'pageData',
                'homeRoute',
                'isAdmin',
                'myApps'
            )
        );
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
