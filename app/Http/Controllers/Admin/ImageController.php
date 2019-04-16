<?php

namespace App\Http\Controllers\Admin;

use App\Http\Helpers\ImageHelper;
use App\Models\Core\Image;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller
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

    public function index ()
    {
        $title = __('Images');
        $component = 'index-image';
        $data = [
            'images' => Image::all()
        ];
        $routes = [
            'baseRouteImage' => route('admin.images'),
            'createImages' => route('admin.images.create'),
            'uploadImage' => route('admin.imagesTemporal.upload')
        ];

        return view('admin/default', compact( 'data', 'title', 'component', 'routes' ));
    }

    public function create ( Request $request )
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'src' => 'required|string'
        ]);

        if ( !$validator->fails() ) {
            $imageInfo = pathinfo($request->input('src'));
            $origin = ImageHelper::$pathTemporal . '/' . $imageInfo['basename'];
            $destination = Image::$folder . '/' . $imageInfo['basename'];
            $src = ImageHelper::move($origin, $destination);

            $image = Image::create([
                'src' => $src,
                'title' => $request->input('title')
            ]);

            return Response::json([
                'result' => true,
                'image' => $image
            ]);
        } else {
            abort(400);
        }
    }

    public function update ( Image $image, Request $request )
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'src' => 'required|string'
        ]);

        if ( !$validator->fails() ) {
            if ( $image->src !== $request->input('src') ) {
                ImageHelper::destroyImage(Image::$folder, $image->src);
                $imageInfo = pathinfo($request->input('src'));
                $origin = ImageHelper::$pathTemporal . '/' . $imageInfo['basename'];
                $destination = Image::$folder . '/' . $imageInfo['basename'];
                $image->src = ImageHelper::move($origin, $destination);
            }

            $image->title = $request->input('title');
            $image->save();

            return Response::json([
                'result' => true,
                'image' => $image
            ]);
        } else {
            abort(400);
        }
    }

    public function delete ( Image $image )
    {
        $image->delete();
        return Response::json([ 'result' => true ]);
    }
}
