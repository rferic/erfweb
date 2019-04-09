<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\Core\Content;

class ContentController extends Controller
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

    public function get ( Request $request )
    {
        $validator = Validator::make($request->all(), [ 'page_locale_id' => 'required|exists:page_locales,id' ]);

        if ( !$validator->fails() ) {
            return Response::json(Content::where('page_locale_id', $request->input('page_locale_id'))->get()->toArray());
        } else {
            abort(400);
        }
    }

    public function create ( Request $request )
    {
        $validator = Validator::make($request->all(), [
            'page_locale_id' => 'required|exists:page_locales,id',
            'key' => 'required',
            'text' => 'required',
            'priority' => 'required|integer'
        ]);

        if ( !$validator->fails() ) {
            $content = Content::create([
                'page_locale_id' => $request->input('page_locale_id'),
                'key' => $request->input('key'),
                'id_html' => $request->input('id_html'),
                'class_html' => $request->input('class_html'),
                'text' => $request->input('text'),
                'header_inject' => $request->input('header_inject'),
                'footer_inject' => $request->input('footer_inject'),
                'priority' => $request->input('priority')
            ]);

            return Response::json([
                'result' => true,
                'content' => $content
            ]);
        } else {
            abort(400);
        }
    }

    public function update ( Content $content, Request $request )
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'text' => 'required',
            'priority' => 'required|integer'
        ]);

        if ( !$validator->fails() ) {
            $content->key = $request->input('key');
            $content->id_html = $request->input('id_html');
            $content->class_html = $request->input('class_html');
            $content->text = $request->input('text');
            $content->header_inject = $request->input('header_inject');
            $content->footer_inject = $request->input('footer_inject');
            $content->priority = $request->input('priority');
            $content->save();
            return Response::json(['result' => true]);
        } else {
            abort(400);
        }
    }

    public function restore ( $id )
    {
        $content = Content::withTrashed()->find($id);

        if ( $content->trashed() ) {
            $content->restore();
            return Response::json(['result' => true]);
        } else {
            return Response::json(['result' => false]);
        }
    }

    public function remove ( $id )
    {
        $content = Content::withTrashed()->find($id);

        if ( !$content->trashed() ) {
            $content->delete();
            return Response::json(['result' => true]);
        } else {
            return Response::json(['result' => false]);
        }
    }

    public function destroy ( $id )
    {
        $content = Content::withTrashed()->find($id);

        if ( !is_null($content) && $content->trashed() ) {
            $content->forceDelete();
            return Response::json(['result' => true]);
        } else {
            return Response::json(['result' => false]);
        }
    }
}
