<?php

namespace App\Http\Controllers\Front;

use App\Models\Core\MenuItem;
use App\Models\Core\PageLocale;
use App\Models\Core\Redirection;
use Arcanedev\Localization\Facades\Localization;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PageController extends Controller
{
    public function home ()
    {
        return $this->printPage('home');
    }

    public function index ( $slug )
    {
        // Check has redirection
        $redirection = Redirection::where('slug_origin', $slug)->first();

        if ( !is_null($redirection) ) {
            return redirect($redirection->slug_destine);
        }
        // Check page locale
        $pageLocale = PageLocale::where('lang', Localization::getCurrentLocaleRegional())
            ->where('slug', $slug)
            ->with(['contents'])
            ->first();

        if ( is_null($pageLocale) ) {
            abort(404);
        }
        // Print page
        return $this->printPage('default');
    }

    private function printPage ( $layout ) {
        $menu = $this->getMenu();
        return view('front/' . $layout, compact('menu'));
    }

    private function getMenu ()
    {
        return MenuItem::whereHas('menu', function ( $query ) {
            $query->where('is_default', true);
        })->with([ 'pageLocale' ])->orderBy('priority')->get()->toArray();
    }
}
