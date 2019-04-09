<?php

namespace App\Http\Controllers\Admin;

use App\Models\Core\AppLocale;
use App\Models\Core\PageLocale;
use App\Models\Core\Redirection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class SlugController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct ()
    {
        $this->middleware('auth');
    }

    public function getIsFree ( Request $request )
    {
        $validator = Validator::make($request->all(), [ 'slug' => 'required',  'lang' => 'required', 'type' => 'required' ]);

        if ( !$validator->fails() ) {
            $slug = $request->input('slug');
            $lang = $request->input('lang');
            $currentParentId = $request->input('currentParentId');

            switch ( $request->input('type') ) {
                case 'pageLocale':
                    $model = PageLocale::where('slug', $slug);
                    break;
                case 'appLocale':
                    $model = AppLocale::where('slug', $slug);
                    break;
                default:
                    $model = PageLocale::where('slug', $slug);
                    break;
            }

            $isUsedInPageLocale = PageLocale::where('slug', $slug)->where('lang', $lang)->get()->count() > 0;
            $isUsedInAppLocale = AppLocale::where('slug', $slug)->where('lang', $lang)->get()->count() > 0;
            $isUsed = $isUsedInPageLocale || $isUsedInAppLocale;
            $hasRedirection = Redirection::where('slug_origin', $slug)->get()->count() > 0;
            $isMine = false;

            if ( !is_null($currentParentId) && $isUsed ) {
                $isMine = $model->where('lang', $lang)->where('id', $currentParentId)->get()->count() > 0;
            }

            return Response::json([
                'result' => true,
                'isFree' => !$hasRedirection && (!$isUsed || $isMine),
                'isUsed' => $isUsed,
                'isMine' => $isMine,
                'hasRedirection' => $hasRedirection
            ]);
        } else {
            abort(400);
        }
    }

    public function getAllSlugs ()
    {
        $slugsPages = PageLocale::query()->pluck('slug')->all();
        $slugsApps = AppLocale::query()->pluck('slug')->all();

        return Response::json(array_merge($slugsPages, $slugsApps));
    }
}
