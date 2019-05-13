<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Helpers\AppHelper;
use App\Http\Helpers\LocalizationHelper;
use App\Http\Helpers\MessageHelper;
use App\Http\Helpers\RoleHelper;
use App\Models\Core\App;
use App\Models\Core\Message;
use App\Models\Core\Page;
use App\Models\Core\PageLocale;
use App\Models\Core\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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
        $data = [
            'langsAvailable' => LocalizationHelper::getSupportedFormatted()
        ];

        return view('admin/default', compact('title', 'description', 'component', 'routes', 'data'));
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

    public function getStatisticsMessages ()
    {
        $statistics = [
            'status' => [],
            'tags' => [],
            'total' => Message::all()->count(),
            'lastThreeMonths' => Message::whereNull('receiver_id')
                ->where('created_at', '>', Carbon::now()->subMonths(6))
                ->get()
                ->toArray()
        ];
        $statusList = MessageHelper::getStatusList();
        $tagsList = MessageHelper::getTagsList();
        $status = Message::groupBy('status')->select('status', DB::raw('count(status) AS count'))->get();
        $tags = Message::groupBy('tag')->select('tag', DB::raw('count(tag) AS count'))->get();

        foreach ( $statusList AS $item ) {
            $item['count'] = 0;

            foreach ( $status AS $item2 ) {
                if ( $item['key'] === $item2->status ) {
                    $item['count'] = $item2->count;
                    break;
                }
            }

            $statistics['status'][$item['key']] = $item;
        }

        foreach ( $tagsList AS $item ) {
            $item['count'] = 0;

            foreach ( $tags AS $item2 ) {
                if ( $item['key'] === $item2->status ) {
                    $item['count'] = $item2->count;
                    break;
                }
            }

            $statistics['tags'][$item['key']] = $item;
        }

        return $statistics;
    }

    public function getStatisticsUsers ()
    {
        $statistics = [];
        $roles = RoleHelper::getRoles();

        foreach ( $roles AS $role ) {
            $statistics[$role] = [
                'total' => User::whereRoleIs($role)->withTrashed()->get()->count(),
                'enable' => User::whereRoleIs($role)->get()->count(),
                'disable' => User::onlyTrashed()->whereRoleIs($role)->get()->count(),
                'lastThreeMonths' => User::where('created_at', '>', Carbon::now()->subMonths(6))->get()->toArray()
            ];
        }

        return $statistics;
    }

    public function getStatisticsApps ()
    {
        $statistics = [
            'total' => App::all()->count(),
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
                'class' => $type['class'],
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
                'class' => $statusItem['class'],
                'count' => 0
            ];

            foreach ( $statusResults AS $result ) {
                if ( $statusItem['key'] === $result->status ) {
                    $item['count'] = $result->count;
                    break;
                }
            }

            $statistics['status'][$statusItem['key']] = $item;
        }

        return $statistics;
    }

    public function getStatisticsPages ()
    {
        return [
            'total' => Page::all()->count(),
            'langs' => PageLocale::groupBy('lang')->select('lang', DB::raw('count(lang) AS count'))->get()->toArray(),
            'layouts' => PageLocale::groupBy('layout')->select('layout', DB::raw('count(layout) AS count'))->get()->toArray(),
        ];
    }
}
