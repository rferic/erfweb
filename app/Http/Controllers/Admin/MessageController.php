<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Core\Message;
use App\Http\Helpers\MessageHelper;

class MessageController extends Controller
{
    public function getStatus ()
    {
        return Response::json([
            'status' => [
                'result' => Message::groupBy('status')->select('status', DB::raw('count(status) AS count'))->get(),
                'options' => MessageHelper::getStatusList()
            ],
            'tags' => [
                'result' => Message::groupBy('tag')->select('tag', DB::raw('count(tag) AS count'))->get(),
                'options' => MessageHelper::getTagsList()
            ]
        ]);
    }
}
