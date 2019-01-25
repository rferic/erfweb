<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Core\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Core\Message;
use App\Http\Helpers\MessageHelper;

class MessageController extends Controller
{
    public function index ()
    {
        $vieOptions = MessageHelper::getIndexViewOptions(false);
        $title = __('Messages');
        $component = $vieOptions['component'];
        $data = $vieOptions['data'];
        $routes = $vieOptions['routes'];

        return view('admin/default', compact( 'data', 'title', 'component', 'routes' ));
    }

    public function indexTrash ()
    {
        $vieOptions = MessageHelper::getIndexViewOptions(true);
        $title = __('Messages in trash');
        $component = $vieOptions['component'];
        $data = $vieOptions['data'];
        $routes = $vieOptions['routes'];

        return view('admin/default', compact( 'data', 'title', 'component', 'routes' ));
    }

    public function get ( Request $request )
    {
        $perPage = $request->input('perPage');
        $filters = $request->input('filters');

        return Response::json($this->getMessages($filters, $perPage));
    }

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

    public function getAuthor ( Message $message ) {
        return Response::json(User::where('id', $message->author->id)->with('messages')->first());
    }

    public function remove ( $id ) {
        $message = Message::withTrashed()->find($id);
        if ( !$message->trashed() ) {
            $message->delete();
            return Response::json(['result' => true]);
        } else {
            return Response::json(['result' => false]);
        }
    }

    public function restore ( $id ) {
        $message = Message::withTrashed()->find($id);

        if ( $message->trashed() ) {
            $message->restore();
            return Response::json(['result' => true]);
        } else {
            return Response::json(['result' => false]);
        }
    }

    public function destroy ( $id ) {
        $message = Message::withTrashed()->find($id);

        if ( $message->trashed() ) {
            $message->forceDelete();
            return Response::json(['result' => true]);
        } else {
            return Response::json(['result' => false]);
        }
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

    protected function getMessages ( $filters, $perPage )
    {
        $query = Message::query();

        if ( isset($filters['onlyTrashed']) && $filters['onlyTrashed'] ) {
            $query = $query->onlyTrashed();
        }

        if ( isset($filters['status']) ) {
            $status = $filters['status'];
            $query->where(function ($query) use ($status) {
                foreach ($status as $item) {
                    $query->orWhere('status', '=', $item);
                }

                return $query;
            });
        }

        if ( isset($filters['tags']) ) {
            $tags = $filters['tags'];
            $query->where(function ($query) use ($tags) {
                foreach ($tags as $item) {
                    $query->orWhere('tag', '=', $item);
                }

                return $query;
            });
        }

        if ( isset($filters['text']) ) {
            $text = $filters['text'];
            $query->where(function ($query) use ($text) {
                return $query
                    ->where('subject', 'LIKE', '%' . $text. '%')
                    ->orWhere('text', 'LIKE', '%' . $text. '%')
                    ->orWhereHas('author', function ($query) use ($text){
                        return $query
                            ->where('name', 'LIKE', '%' . $text. '%')
                            ->orWhere('email', 'LIKE', '%' . $text. '%');
                    });
            });
        }

        return $query->with('author')->orderByDesc('created_at')->paginate($perPage);
    }
}
