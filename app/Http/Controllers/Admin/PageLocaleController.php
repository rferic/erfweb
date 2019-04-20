<?php

namespace App\Http\Controllers\Admin;

use App\Models\Core\PageLocale;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class PageLocaleController extends Controller
{
    public function get ( Request $request )
    {
        $validator = Validator::make($request->all(), [ 'filters' => 'nullable|array' ]);

        if ( !$validator->fails() ) {
            return Response::json($this->getPageLocales($request->input('filters')));
        } else {
            abort(400);
        }
    }

    private function getPageLocales ( $filters )
    {
        $query = PageLocale::query();

        if ( isset($filters['langs']) ) {
            $langs = $filters['langs'];

            $query->where(function ($query) use ($langs) {
                foreach ($langs as $lang) {
                    $query->orWhere('lang', $lang);
                }

                return $query;
            });
        }

        if ( isset($filters['text']) ) {
            $text = $filters['text'];
            $query->where(function ($query) use ($text) {
                return $query
                    ->where('slug', 'LIKE', '%' . $text. '%')
                    ->orWhere('title', 'LIKE', '%' . $text. '%');
            });
        }

        return $query->get();
    }
}
