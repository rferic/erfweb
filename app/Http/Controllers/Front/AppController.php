<?php

namespace App\Http\Controllers\Front;

use App\Http\Helpers\AppHelper;
use App\Models\Core\App;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AppController extends Controller
{
    public function get ( Request $request )
    {
        $validator = Validator::make($request->all(), [
            'format' => [
                'required',
                Rule::in(['collection', 'pagination'])
            ],
            'randomOrder' => 'required|boolean',
            'maxItems' => 'required_if:format,collection|integer',
            'perPage' => 'required_if:format,pagination',
            'status' => [
                'array',
                function ( $attribute, $items, $fail ) {
                    foreach ( $items AS $item ) {
                        if ( !AppHelper::existsKeyInStatus($item) ) {
                            $fail($attribute.' is invalid.');
                        }
                    }
                }
            ],
            'tag' => [
                'array',
                function ( $attribute, $items, $fail ) {
                    foreach ( $items AS $item ) {
                        if ( !AppHelper::existsKeyInTypes($item) ) {
                            $fail($attribute.' is invalid.');
                        }
                    }
                }
            ]
        ]);

        if ( !$validator->fails() ) {
            return Response::json($this->getApps($request));
        } else {
            abort(400);
        }
    }

    protected function getApps ( Request $request )
    {
        $query = App::query()->with(['locales', 'users', 'images']);
        // Filter status
        if ( !is_null($request->input('status')) && COUNT($request->input('status')) > 0 ) {
            $status = $request->input('status');
            $query->where(function ($query) use ($status) {
                foreach ($status as $item) {
                    $query->orWhere('status', $item);
                }

                return $query;
            });
        }
        // Filter types
        if ( !is_null($request->input('types')) && COUNT($request->input('types')) > 0 ) {
            $types = $request->input('types');
            $query->where(function ($query) use ($types) {
                foreach ($types as $item) {
                    $query->orWhere('type', $item);
                }

                return $query;
            });
        }

        if ( $request->input('randomOrder') ) {
            $query->inRandomOrder();
        }

        if ( $request->input('format') === 'collection' ) {
            return $query->limit($request->input('maxItems'))->get();
        } else if ( $request->input('format') === 'pagination' ) {
            return $query->paginate($request->input('perPage'));
        }
    }
}
