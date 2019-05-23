<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\LocalizationHelper;
use App\Http\Helpers\PageHelper;
use App\Models\Core\Content;
use App\Models\Core\Page;
use App\Models\Core\PageLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

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
        $title = __('Pages management');
        $description = __('CRUD for pages. Be creative!');
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

        return Response::json($this->getPages($filters, $perPage, $orderBy));
    }

    public function getAllParents ()
    {
        return Response::json(Page::whereNull('page_id')->with('locales')->get()->toArray());
    }

    public function store ( Request $request )
    {
        $validator = Validator::make($request->all(), [
            'id' => 'nullable|integer',
            'page_id' => 'nullable|integer',
            'locales' => 'nullable|array'
        ]);

        if ( !$validator->fails() ) {
            $pageLocales = $request->input('locales');
            $id = $request->input('id');
            $page_id = $request->input('page_id');
            $is_home = $request->input('is_home');

            if ( $is_home ) {
                $pageHome = Page::where('is_home', true)->whereNotIn('id', [ $id ])->first();

                if ( !is_null($pageHome) ) {
                    $pageHome->is_home = false;
                    $pageHome->save();
                }
            }

            if ( is_null($id) ) {
                $id = Page::create([
                    'page_id' => $page_id,
                    'user_id' => Auth::id(),
                    'is_home' => $is_home
                ])->id;
            } else {
                $page = Page::find($id);
                $page->is_home = $is_home;
                $page->save();
            }

            foreach ( $pageLocales AS $pageLocale ) {
                if (is_null($pageLocale['id']) && is_null($pageLocale['deleted_at'])) {
                    $page_locale_id = $this->createPageLocale($id, $pageLocale)->id;
                } else {
                    $page_locale_id = $pageLocale['id'];
                    if (!is_null($pageLocale['id']) && is_null($pageLocale['deleted_at'])) {
                        $this->updatePageLocale($pageLocale);
                    }
                }

                if (!is_null($page_locale_id)) {
                    $pageLocaleInst = PageLocale::where('id', $page_locale_id)->first();

                    if (!is_null($pageLocale['deleted_at'])) {
                        $pageLocaleInst->delete();
                    } else {
                        $pageLocaleInst->restore();
                        $this->destroyContents($pageLocaleInst, $pageLocale['contents']);

                        foreach ($pageLocale['contents'] AS $contentData) {
                            if (is_null($contentData['id'])) {
                                $content_id = $this->createContent($page_locale_id, $contentData)->id;
                            } else {
                                $this->updateContent($contentData);
                                $content_id = $contentData['id'];
                            }

                            $content = Content::withTrashed()->where('id', $content_id)->first();

                            if (!is_null($content)) {
                                if (!is_null($contentData['deleted_at'])) {
                                    $content->delete();
                                } else if (is_null($contentData['deleted_at'])) {
                                    $content->restore();
                                }
                            }
                        }
                    }
                }
            }

            return Response::json(['result' => true]);
        } else {
            abort(400);
        }
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
        $query = Page::query()->with(['locales', 'author', 'contents']);
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
        // Filter parent
        if ( isset($filters['parent']) ) {
            $query->where('parent_id', $filters['parent']);
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
                'langsAvailable' => LocalizationHelper::getSupportedFormatted(),
                'layouts' => PageHelper::getLayouts()
            ],
            'routes' => [
                'getPages' => route('admin.pages.get'),
                'getAllPagesParent' => route('admin.pages.getAllParents'),
                'storePage' => route('admin.pages.store'),
                'getContents' => route('admin.contents.get'),
                'getMenus' => route('admin.menus.get'),
                'indexRedirections' => route('admin.redirections'),
                'getRedirections' => route('admin.redirections.get'),
                'createRedirection' => route('admin.redirections.create'),
                'getAllSlugs' => route('admin.slugs.getAllSlugs'),
                'getSlugIsFree' => route('admin.slugs.getIsFree')
            ]
        ];
    }

    private function createPageLocale ( $page_id, $params )
    {
        return PageLocale::create([
            'user_id' => Auth::id(),
            'page_id' => $page_id,
            'lang' => $params['lang'],
            'slug' => $params['slug'],
            'title' => $params['title'],
            'description' => $params['description'],
            'layout' => $params['layout'],
            'options' => $params['options'],
            'seo_title' => $params['seo_title'],
            'seo_description' => $params['seo_description'],
            'seo_keywords' => $params['seo_keywords']
        ]);
    }

    private function updatePageLocale ( $params )
    {
        $pageLocale = PageLocale::find($params['id']);

        $pageLocale->slug = $params['slug'];
        $pageLocale->title = $params['title'];
        $pageLocale->description = $params['description'];
        $pageLocale->layout = $params['layout'];
        $pageLocale->options = $params['options'];
        $pageLocale->seo_title = $params['seo_title'];
        $pageLocale->seo_description = $params['seo_description'];
        $pageLocale->seo_keywords = $params['seo_keywords'];
        $pageLocale->save();
    }

    private function createContent ( $page_locale_id, $params )
    {
        return Content::create([
            'page_locale_id' => $page_locale_id,
            'key' => $params['key'],
            'id_html' => $params['id_html'],
            'class_html' => $params['class_html'],
            'text' => $params['text'],
            'header_inject' => $params['header_inject'],
            'footer_inject' => $params['footer_inject'],
            'priority' => $params['priority']
        ]);
    }

    private function updateContent ( $params )
    {
        $content = Content::find($params['id']);

        foreach ( $params AS $attribute => $param ) {
            if ( isset($content[$attribute]) ) {
                $content[$attribute] = $param;
                $content->save();
            }
        }
    }

    private function destroyContents ( $pageLocale, $currentContents ) {
        $contents = $pageLocale->contents;

        if ( $contents->count() > 0 ) {
            foreach ( $contents AS $content ) {
                $find = false;

                foreach ( $currentContents AS $currentContent ) {
                    if ( intval($currentContent['id']) === $content->id ) {
                        $find = true;
                    }
                }

                if ( !$find ) {
                    $content->forceDelete();
                }
            }
        }
    }
}
