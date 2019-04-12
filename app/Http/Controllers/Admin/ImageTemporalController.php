<?php
/**
 * Created by PhpStorm.
 * User: ericrf
 * Date: 28/01/2019
 * Time: 14:34
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ImageHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ImageTemporalController extends Controller
{
    static public $disk = 'public';
    static public $temporalPath = '_tmp';

    public function __construct ()
    {
        $this->middleware('auth');
    }

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

    public function remove ( Request $request )
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
            abort(404);
        }
    }
}
