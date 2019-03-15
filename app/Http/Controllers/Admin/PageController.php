<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\PageHelper;
use App\Models\Core\Page;
use App\Models\Core\PageLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class PageController extends Controller
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
        $vieOptions = $this->getIndexViewOptions();
        $title = __('Pages');
        $component = $vieOptions['component'];
        $data = $vieOptions['data'];
        $routes = $vieOptions['routes'];

        return view('admin/default', compact( 'data', 'title', 'component', 'routes' ));
    }

    public function get ( Request $request )
    {
        $perPage = $request->input('perPage');
        $filters = $request->input('filters');
        $orderBy = $request->input('orderBy');

        return Response::json($this->getPages($filters, $perPage, $orderBy));
    }

    public function getAllSlugsPage ()
    {
        return Response::json(PageLocale::query()->pluck('slug')->all());
    }

    public function remove ( $id )
    {
        $page = Page::withTrashed()->find($id);

        if ( !$page->trashed() ) {
            $page->delete();
            return Response::json(['result' => true]);
        } else {
            return Response::json(['result' => false]);
        }
    }

    public function restore ( $id )
    {
        $page = Page::withTrashed()->find($id);

        if ( $page->trashed() ) {
            $page->restore();
            return Response::json(['result' => true]);
        } else {
            return Response::json(['result' => false]);
        }
    }

    public function destroy ( $id )
    {
        $page = Page::withTrashed()->find($id);

        if ( !is_null($page) && $page->trashed() ) {
            $page->forceDelete();
            return Response::json(['result' => true]);
        } else {
            return Response::json(['result' => false]);
        }
    }

    protected function getPages ( $filters, $perPage, $orderBy )
    {
        $query = Page::query()->with(['locales', 'author']);
        $onlyTrashed = isset($filters['enables']) && !$filters['enables'] && isset($filters['disables']) && $filters['disables'];
        $withTrashed = isset($filters['enables']) && $filters['enables'] && isset($filters['disables']) && $filters['disables'];

        if ( $onlyTrashed ) {
            $query = $query->onlyTrashed();
        } else if ( $withTrashed ) {
            $query = $query->withTrashed();
        }
        // Filter text
        if ( isset($filters['text']) ) {
            $text = $filters['text'];
            $query->where(function ($query) use ($text) {
                return $query
                    ->orWhereHas('author', function ($query) use ($text) {
                        return $query
                            ->where('name', 'LIKE', '%' . $text. '%')
                            ->orWhere('email', 'LIKE', '%' . $text. '%');
                    })
                    ->orWhereHas('locales', function ($query) use ($text) {
                        return $query
                            ->where('slug', 'LIKE', '%' . $text . '%')
                            ->orWhere('title', 'LIKE', '%' . $text. '%')
                            ->orWhere('description', 'LIKE', '%' . $text. '%');
                    })
                    ->orWhereHas('contents', function ($query) use ($text) {
                        return $query
                            ->where('key', '=', $text)
                            ->orWhere('text', 'LIKE', '%' . $text. '%');
                    });
            });
        }
        // Filter author
        if  ( isset($filters['authors']) ) {
            $query->where(function ($query) use ($filters) {
                foreach ($filters['authors'] as $item) {
                    $query->orWhere('author_id', $item);
                }
            });
        }
        // Filter is in menus
        if ( isset($filters['menus']) ) {
            $query->where(function ($query) use ($filters) {
                if ( !is_null($filters['menus']) ) {
                    foreach ($filters['menus'] as $menu) {
                        return $query->orWhereHas('menuItems', function ($query) use ($menu) {
                            return $query->where('menu_id', $menu)->withTrashed();
                        });
                    }
                }
            });
        }
        // Filters locales
        if ( isset($filters['langs']) ) {
            $query->where(function ($query) use ($filters) {
                if ( !is_null($filters['langs']) ) {
                    foreach ($filters['langs'] as $lang) {
                        return $query->orWhereHas('locales', function ($query) use ($lang) {
                            return $query->where('lang', $lang)->withTrashed();
                        });
                    }
                }
            });
        }

        if ( $orderBy['way'] === 'ASC' ) {
            $query = $query->orderBy($orderBy['attribute']);
        } else {
            $query = $query->orderByDesc($orderBy['attribute']);
        }

        return $query->paginate($perPage);
    }

    private function getIndexViewOptions ()
    {
        return [
            'component' => 'index-page',
            'data' => [
                'langsAvailable' => config('global.langsAvailables'),
                'layouts' => PageHelper::getLayouts()
            ],
            'routes' => [
                'getPages' => route('admin.pages.get'),
                'getAllSlugsPage' => route('admin.pages.getAllSlugsPage'),
                'getContents' => route('admin.contents.get'),
                'getMenus' => route('admin.menus.get'),
                'indexRedirections' => route('admin.redirections'),
                'getRedirections' => route('admin.redirections.get'),
                'createRedirection' => route('admin.redirections.create')
            ]
        ];
    }
}
