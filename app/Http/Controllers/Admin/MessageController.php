<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Core\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Core\Message;
use App\Http\Helpers\MessageHelper;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
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
        $vieOptions = MessageHelper::getIndexViewOptions(false);
        $title = __('Messages inbox');
        $description = __('Your inbox. Keep in touch with the outside!');
        $component = $vieOptions['component'];
        $data = $vieOptions['data'];
        $routes = $vieOptions['routes'];

        return view('admin/default', compact( 'data', 'title', 'description', 'component', 'routes' ));
    }

    public function detail ( Message $message)
    {
        $message = Message::with('author')->find($message->id);
        $vieOptions = MessageHelper::getIndexViewOptions(false, $message);
        $title = __('Message');
        $description = __('Here is your message. Read and answer!');
        $component = $vieOptions['component'];
        $data = $vieOptions['data'];
        $routes = $vieOptions['routes'];

        return view('admin/default', compact( 'data', 'title', 'description', 'component', 'routes' ));
    }

    public function indexTrash ()
    {
        $vieOptions = MessageHelper::getIndexViewOptions(true);
        $title = __('Messages trash');
        $description = __('Your trahs. Be clean!');
        $component = $vieOptions['component'];
        $data = $vieOptions['data'];
        $routes = $vieOptions['routes'];

        return view('admin/default', compact( 'data', 'title', 'description', 'component', 'routes' ));
    }

    public function get ( Request $request )
    {
        $perPage = $request->input('perPage');
        $filters = $request->input('filters');
        $orderBy = $request->input('orderBy');

        return Response::json($this->getMessages($filters, $perPage, $orderBy));
    }

    public function getState ( Request $request )
    {
        $filters = $request->input('filters');

        return Response::json([
            'statusStructure' => MessageHelper::getStatusList(),
            'tagsStructure' => MessageHelper::getTagsList(),
            'status' => $this->getStateStatus($filters),
            'tags' => $this->getStateTags($filters)
        ]);
    }

    public function getLastPending ( Request $request )
    {
        $count = $request->input('count');
        return Response::json(Message::where('status', 'pending')->orderBy('created_at', 'desc')->take($count)->get());
    }

    public function getAuthor ( Message $message )
    {
        return Response::json(User::where('id', $message->author->id)->with('messages')->first());
    }

    public function create ( Request $request )
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
                'message_parent_id' => $request->input('message_parent_id'),
                'author_id' => Auth::id(),
                'receiver_id' => $request->input('receiver_id')
            ]);

            return Response::json([
                'result' => true,
                'message' => $message
            ]);
        } else {
            abort(400);
        }
    }

    public function update ( Message $message, Request $request )
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
            $message->subject = $request->input('subject');
            $message->text = $request->input('text');
            $message->status = $request->input('status');
            $message->tag = $request->input('tag');
            $message->save();
            return Response::json(['result' => true]);
        } else {
            abort(400);
        }
    }

    public function remove ( $id )
    {
        $message = Message::withTrashed()->find($id);

        if ( !$message->trashed() ) {
            $message->delete();
            return Response::json(['result' => true]);
        } else {
            return Response::json(['result' => false]);
        }
    }

    public function restore ( $id )
    {
        $message = Message::withTrashed()->find($id);

        if ( $message->trashed() ) {
            $message->restore();
            return Response::json(['result' => true]);
        } else {
            return Response::json(['result' => false]);
        }
    }

    public function destroy ( $id )
    {
        $message = Message::withTrashed()->find($id);

        if ( !is_null($message) && $message->trashed() ) {
            $message->forceDelete();
            return Response::json(['result' => true]);
        } else {
            return Response::json(['result' => false]);
        }
    }

    protected function getMessages ( $filters, $perPage, $orderBy )
    {
        $query = Message::query();

        if ( isset($filters['onlyTrashed']) && $filters['onlyTrashed'] ) {
            $query = $query->onlyTrashed();
        }

        if ( isset($filters['status']) ) {
            $status = $filters['status'];
            $query->where(function ($query) use ($status) {
                foreach ($status as $item) {
                    $query->orWhere('status', $item);
                }

                return $query;
            });
        }

        if ( isset($filters['tags']) ) {
            $tags = $filters['tags'];
            $query->where(function ($query) use ($tags) {
                foreach ($tags as $item) {
                    $query->orWhere('tag', $item);
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
                    ->orWhereHas('author', function ($query) use ($text) {
                        return $query
                            ->where('name', 'LIKE', '%' . $text. '%')
                            ->orWhere('email', 'LIKE', '%' . $text. '%');
                    });
            });
        }

        if  ( isset($filters['authors']) || isset($filters['receivers']) || isset($filters['message_parent']) ) {
            $query->where(function ($query) use ($filters) {
                if ( isset($filters['authors']) ) {
                    foreach ($filters['authors'] as $item) {
                        $query->orWhere('author_id', $item);
                    }
                }

                if ( isset($filters['receivers']) ) {
                    foreach ($filters['receivers'] as $item) {
                        if ( $item === 'admin' ) {
                            $query->orWhereNull('receiver_id');
                        } else {
                            $query->orWhere('receiver_id', $item);
                        }
                    }
                }

                if  ( isset($filters['message_parent']) ) {
                    if ( !$filters['message_parent'] ) {
                        $query->orWhereNull('message_parent_id');
                    } else {
                        $query->orWhere('message_parent_id', $filters['message_parent']);
                    }
                }
            });
        }

        $query = $query->with(['author', 'receiver', 'childs', 'parent']);

        if ( $orderBy['way'] === 'ASC' ) {
            $query = $query->orderBy($orderBy['attribute']);
        } else {
            $query = $query->orderByDesc($orderBy['attribute']);
        }

        return $query->paginate($perPage);
    }

    private function getStateStatus ( $filters = [] )
    {
        $query = Message::query();
        $result = new \stdClass();

        if ( isset($filters['authors']) ) {
            $authors = $filters['authors'];
            $query->where(function ($query) use ($authors) {
                foreach ($authors as $item) {
                    $query->orWhere('author_id', $item);
                }

                return $query;
            });
        }

        $status = $query->groupBy('status')->select('status', DB::raw('count(status) AS count'))->get();

        foreach ( MessageHelper::getStatusList() AS $itemStatus ) {
            $key = $itemStatus['key'];
            $count = 0;

            foreach ( $status AS $state ) {
                if ( $itemStatus['key'] === $state->status ) {
                    $count = $state->count;
                }
            }

            $result->$key = strval($count);
        }

        return $result;
    }

    private function getStateTags ( $filters = [] )
    {
        $query = Message::query();
        $result = new \stdClass();

        if ( isset($filters['authors']) ) {
            $authors = $filters['authors'];
            $query->where(function ($query) use ($authors) {
                foreach ($authors as $item) {
                    $query->orWhere('author_id', $item);
                }

                return $query;
            });
        }
        $tags = $query->groupBy('tag')->select('tag', DB::raw('count(tag) AS count'))->get();

        foreach ( MessageHelper::getTagsList() AS $itemTags ) {
            $key = $itemTags['key'];
            $count = 0;

            foreach ( $tags AS $tag ) {
                if ( $key === $tag->tag ) {
                    $count = $tag->count;
                }
            }

            $result->$key = strval($count);
        }

        return $result;
    }
}
