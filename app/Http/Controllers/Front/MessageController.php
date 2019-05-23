<?php

namespace App\Http\Controllers\Front;

use App\Http\Helpers\MessageHelper;
use App\Models\Core\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    public function store ( Request $request )
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required',
            'text' => 'required',
            'status' => 'required',
            'tag' => 'required'
        ]);

        if (
            !$validator->fails() &&
            MessageHelper::existsKeyInStatusList($request->input('status')) &&
            MessageHelper::existsKeyInTagsList($request->input('tag'))
        ) {
            $message = Message::create([
                'subject' => $request->input('subject'),
                'text' => $request->input('text'),
                'status' => $request->input('status'),
                'tag' => $request->input('tag'),
                'message_parent_id' => null,
                'author_id' => Auth::id(),
                'receiver_id' => null
            ]);

            return Response::json([
                'result' => true,
                'message' => $message
            ]);
        } else {
            return Response::json([
                'result' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
}
