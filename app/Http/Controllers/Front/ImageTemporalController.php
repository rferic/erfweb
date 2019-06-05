<?php

namespace App\Http\Controllers\Front;

use App\Http\Helpers\ImageHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ImageTemporalController extends Controller
{
    static public $disk = 'public';
    static public $temporalPath = '_tmp';

    public function upload ( Request $request )
    {
        // Validate images
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096'
        ]);

        if ( $validator->fails() ) {
            abort(400);
        }

        $image = ImageHelper::upload($request->file('image'));

        return Response::json( [
            'result' => true,
            'data' => [
                'image' => $image
            ]
        ], 200 );
    }

    public function delete ( Request $request )
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required'
        ]);

        if ( $validator->fails() ) {
            abort(400);
        }

        $image = str_replace('storage/', '', $request->input('image'));

        if ( Storage::disk(self::$disk)->exists($image) ) {
            Storage::disk(self::$disk)->delete($image);
            return Response::json( [ 'result' => true ], 200 );
        } else {
            return Response::json( [ 'result' => false ], 200 );
        }
    }
}
