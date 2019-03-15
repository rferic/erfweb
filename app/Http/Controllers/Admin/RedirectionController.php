<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Core\Redirection;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class RedirectionController extends Controller
{
    public function __construct ()
    {
        $this->middleware('auth');
    }

    public function index ()
    {}

    public function get ( Request $request )
    {
        $perPage = $request->input('perPage');
        $filters = $request->input('filters');
        $orderBy = $request->input('orderBy');

        return Response::json($this->getRedirections($filters, $perPage, $orderBy));
    }

    public function create ( Request $request )
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'slug_origin' => 'required',
            'slug_destine' => 'required'
        ]);

        if ( !$validator->fails() ) {
            $redirectionsExists = Redirection::where('slug_origin', $request->input('slug_origin'))->get();
            $redirectionFind = false;

            if ( !is_null($redirectionsExists) ) {
                foreach ( $redirectionsExists as $redirectionExists ) {
                    if ( $redirectionExists->slug_destiner !== $request->input('slug_destine') ) {
                        $redirectionExists->delete();
                    } else {
                        $redirectionFind = true;
                        $redirection = $redirectionExists;
                    }
                }
            }

            if ( !$redirectionFind ) {
                $redirection = Redirection::create([
                    'code' => $request->input('code'),
                    'slug_origin' => $request->input('slug_origin'),
                    'slug_destine' => $request->input('slug_destine')
                ]);
            }

            return Response::json([
                'result' => true,
                'redirection' => $redirection
            ]);
        } else {
            abort(400);
        }
    }

    public function destroy ( Redirection $redirection )
    {
        $redirection->delete();
        return Response::json(['result' => true]);
    }

    protected function getRedirections ( $filters, $perPage, $orderBy )
    {
        $query = Redirection::query();

        if ( isset($filters['code']) ) {
            $query->where('code', $filters['code']);
        }

        if ( isset($filters['slugs_origin']) && COUNT($filters['slugs_origin']) > 0 ) {
            $slugs_origin = $filters['slugs_origin'];

            $query->where(function ($query) use ($slugs_origin) {
                foreach ( $slugs_origin as $slug ) {
                    $query->orWhere('slug_origin', $slug);
                }
            });
        }

        if ( isset($filters['slugs_destine']) && COUNT($filters['slugs_destine']) > 0 ) {
            $slugs_destine = $filters['slugs_destine'];

            $query->where(function ($query) use ($slugs_destine) {
                foreach ( $slugs_destine as $slug ) {
                    $query->orWhere('slug_destine', $slug);
                }
            });
        }

        if ( isset($orderBy) ) {
            if ( $orderBy['way'] === 'ASC' ) {
                $query = $query->orderBy($orderBy['attribute']);
            } else {
                $query = $query->orderByDesc($orderBy['attribute']);
            }
        }

        return is_null($perPage) ? $query->get() : $query->paginate($perPage);
    }
}
