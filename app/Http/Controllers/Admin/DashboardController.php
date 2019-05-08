<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Helpers\AppHelper;
use App\Http\Helpers\MessageHelper;
use App\Http\Helpers\RoleHelper;
use App\Models\Core\App;
use App\Models\Core\Message;
use Illuminate\Support\Facades\Response;

class DashboardController extends Controller
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
        $title = __('Dashboard');
        $description = __('See how the web is going. We hope everything goes perfectly!');
        $component = 'index-dashboard';
        $routes = [
            'getStatistics' => route('admin.dashboard.getStatistics')
        ];

        return view('admin/default', compact('title', 'description', 'component', 'routes'));
    }

    public function getStatistics ()
    {
        return Response::json([
            'messages' => $this->getStatisticsMessages(),
            'users' => $this->getStatisticsUsers(),
            'apps' => $this->getStatisticsApps(),
            'pages' => $this->getStatisticsPages()
        ]);
    }

    private function getStatisticsMessages ()
    {
        $statistics = [
            'status' => [],
            'tags' => []
        ];
        $statusList = MessageHelper::getStatusList();
        $tagsList = MessageHelper::getTagsList();
        $status = Message::groupBy('status')->select('status', DB::raw('count(status) AS count'))->get();
        $tags = Message::groupBy('tag')->select('tag', DB::raw('count(tag) AS count'))->get();

        foreach ( $statusList AS $item ) {
            $item['count'] = 0;

            foreach ( $status AS $item2 ) {
                if ( $item['key'] === $item2->status ) {
                    $item['count'] = $item->count;
                    break;
                }
            }

            $statistics['status'][$item['key']] = $item;
        }

        foreach ( $tagsList AS $item ) {
            $item['count'] = 0;

            foreach ( $tags AS $item2 ) {
                if ( $item['key'] === $item2->status ) {
                    $item['count'] = $item->count;
                    break;
                }
            }

            $statistics['tags'][$item['key']] = $item;
        }

        return $statistics;
    }

    private function getStatisticsUsers ()
    {
        $statistics = [];
        $roles = RoleHelper::getRoles();

        foreach ( $roles AS $role ) {
            $statistics[$role] = [
                'enable' => User::role($role)->get()->count(),
                'disable' => User::onlyTrashed()->role($role)->get()->count()
            ];
        }

        return $statistics;
    }

    private function getStatisticsApps ()
    {
        $statistics = [
            'types' => [],
            'status' => []
        ];
        $types = AppHelper::getTypes();
        $status = AppHelper::getStatus();
        $typeResults = App::groupBy('type')->select('type', DB::raw('count(type) AS count'))->get();
        $statusResults = App::groupBy('status')->select('status', DB::raw('count(status) AS count'))->get();

        foreach ( $types AS $type ) {
            $item = [
                'key' => $type['key'],
                'color' => $type['color'],
                'count' => 0
            ];

            foreach ( $typeResults AS $result ) {
                if ( $type['key'] === $result->type ) {
                    $item['count'] = $result->count;
                    break;
                }
            }

            $statistics['types'][$type['key']] = $item;
        }

        foreach ( $status AS $statusItem ) {
            $item = [
                'key' => $statusItem['key'],
                'color' => $statusItem['color'],
                'count' => 0
            ];

            foreach ( $statusResults AS $result ) {
                if ( $statusItem['key'] === $result->type ) {
                    $item['count'] = $result->count;
                    break;
                }
            }

            $statistics['types'][$statusItem['key']] = $item;
        }

        return $statistics;
    }

    private function getStatisticsPages ()
    {
        return Page::groupBy('layout')->select('layout', DB::raw('count(layout) AS count'))->get()->toArray;
    }
}
