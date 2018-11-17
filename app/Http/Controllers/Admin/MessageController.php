<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Core\Message;
use App\Http\Helpers\MessageHelper;

class MessageController extends Controller
{
    public function getState ()
    {
        return Response::json([
            'statusStructure' => MessageHelper::getStatusList(),
            'tagsStructure' => MessageHelper::getTagsList(),
            'status' => $this->getStateStatus(),
            'tags' => $this->getStateTags()
        ]);
    }

    public function getLastPending ( Request $request )
    {
        $count = $request->input('count');
        return Response::json(Message::where('status', 'pending')->orderBy('created_at', 'desc')->take($count)->get());
    }

    private function getStateStatus ()
    {
        $result = new \stdClass();
        $status = Message::groupBy('status')->select('status', DB::raw('count(status) AS count'))->get();

        foreach ( MessageHelper::getStatusList() AS $itemStatus ) {
            $key = $itemStatus['key'];
            $count = 0;

            foreach ( $status AS $state ) {
                if ( $itemStatus['key'] === $state->status ) {
                    $count = $state->count;
                }
            }

            $result->$key = $count;
        }

        return $result;
    }

    private function getStateTags ()
    {
        $result = new \stdClass();
        $tags = Message::groupBy('tag')->select('tag', DB::raw('count(tag) AS count'))->get();

        foreach ( MessageHelper::getTagsList() AS $itemTags ) {
            $key = $itemTags['key'];
            $count = 0;

            foreach ( $tags AS $tag ) {
                if ( $key === $tag->tag ) {
                    $count = $tag->count;
                }
            }

            $result->$key = $count;
        }

        return $result;
    }
}
