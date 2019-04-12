<?php

namespace App\Http\Controllers\Admin;

use App\Http\Helpers\ImageHelper;
use App\Models\Core\App;
use App\Models\Core\AppImage;
use App\Models\Core\AppLocale;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class AppController extends Controller
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
        $title = __('Apps');
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

        return Response::json($this->getApps($filters, $perPage, $orderBy));
    }

    public function store ( Request $request )
    {
        $validator = Validator::make($request->all(), [
            'id' => 'nullable|integer',
            'version' => 'required|string',
            'vue_component' => 'required|string',
            'type' => 'required|string',
            'status' => 'required|string',
            'locales' => 'required|array',
            'images' => 'required|array'
        ]);

        if ( !$validator->fails() ) {
            $appLocales = $request->input('locales');
            $appImages = $request->input('images');
            $app_id = $request->input('id');

            $app = is_null($app_id) ? $this->create($request) : $this->update($app_id, $request);
            $this->refreshLocales($app, $appLocales);
            $this->refreshImages($app, $appImages);
        } else {
            abort(400);
        }
    }

    public function destroy ( App $app )
    {
        $app->forceDelete();
        return Response::json(['result' => true]);
    }

    protected function getApps ( $filters, $perPage, $orderBy )
    {
        $query = App::query()->with(['locales', 'users', 'images']);
        // Filter type
        if ( isset($filters['types']) ) {
            $query->where(function ($query) use ($filters) {
                if ( !is_null($filters['types']) ) {
                    foreach ($filters['types'] as $type) {
                        $query->orWhere('type', $type['key']);
                    }

                    return $query;
                }
            });
        }
        // Filter status
        if ( isset($filters['status']) ) {
            $query->where(function ($query) use ($filters) {
                if ( !is_null($filters['status']) ) {
                    foreach ($filters['status'] as $status) {
                        $query->orWhere('status', $status['key']);
                    }

                    return $query;
                }
            });
        }
        // Filter text
        if ( isset($filters['text']) ) {
            $text = $filters['text'];
            $query->where(function ($query) use ($text) {
                return $query
                    ->orWhere('vue_component', 'LIKE', '%' . $text. '%')
                    ->orWhere('version', 'LIKE', '%' . $text. '%')
                    ->orWhereHas('locales', function ($query) use ($text) {
                        return $query->where('title', 'LIKE', '%' . $text. '%');
                    });
            });
        }

        if ( $orderBy['way'] === 'ASC' ) {
            $query = $query->orderBy($orderBy['attribute']);
        } else {
            $query = $query->orderByDesc($orderBy['attribute']);
        }

        return $query->paginate($perPage);
    }

    protected function refreshLocales ( App $app, $appLocales)
    {
        $locales = $app->locales()->get();

        foreach ( $locales AS $locale ) {
            $find = false;

            foreach ( $appLocales AS $appLocale ) {
                if ( $locale->id === $appLocale['id'] ) {
                    $find = true;
                    break;
                }
            }

            if ( !$find ) {
                $locale->forceDelete();
            }
        }

        foreach ( $appLocales AS $data ) {
            if ( is_null($data['id']) ) {
                $this->createLocale($app, $data);
            } else {
                foreach ( $locales AS $locale ) {
                    if ( $locale->id === $data['id'] ) {
                        $this->updateLocale($locale, $data);
                        break;
                    }
                }
            }
        }
    }

    protected function refreshImages ( App $app, $appImages)
    {
        $this->clearImages($app, $appImages);
        $this->updateImages($app, $appImages);
    }

    private function getIndexViewOptions ()
    {
        return [
            'component' => 'index-app',
            'data' => [
                'langsAvailable' => config('global.langsAvailables')
            ],
            'routes' => [
                'getApps' => route('admin.apps.get'),
                'storeApp' => route('admin.pages.store'),
                'getMenus' => route('admin.menus.get'),
                'indexRedirections' => route('admin.redirections'),
                'getRedirections' => route('admin.redirections.get'),
                'createRedirection' => route('admin.redirections.create')
            ]
        ];
    }

    private function create ( $request )
    {
        return App::create([
            'version' => $request->input('version'),
            'vue_component' => $request->input('vue_component'),
            'type' => $request->input('type'),
            'status' => $request->input('status'),
            'user_id' => Auth::id()
        ]);
    }

    private function update ( $app_id, $request )
    {
        $app = App::find($app_id);
        $app->version = $request->input('version');
        $app->vue_component = $request->input('vue_component');
        $app->type = $request->input('type');
        $app->status = $request->input('status');
        $app->save();
        return $app;
    }

    private function createLocale ( App $app, $data)
    {
        return AppLocale::create([
            'app_id' => $app->id,
            'lang' => $data['lang'],
            'title' => $data['title'],
            'description' => $data['description']
        ]);
    }

    private function updateLocale ( AppLocale $appLocale, $data )
    {
        $appLocale->lang = $data['lang'];
        $appLocale->title = $data['title'];
        $appLocale->description = $data['description'];
        $appLocale->save();
        return $appLocale;
    }

    private function updateImages ( App $app, $imagesData )
    {
        $images = $app->images()->get();

        foreach ( $imagesData AS $imageData )
        {
            if ( !is_null($imageData['id']) ) {
                foreach ( $images AS $image ) {
                    if ( intval($imageData['id']) === $image->id ) {
                        $this->updateImage($image, $imageData);
                        break;
                    }
                }
            } else {
                $this->storeImage($app, $imageData);
            }
        }
    }

    private function clearImages ( App $app, $imagesData )
    {
        foreach ( $app->images()->get() AS $image ) {
            $findImage = false;

            foreach ( $imagesData AS $imageData ) {
                if ( $image->id === intval($imageData['id']) ) {
                    $findImage = true;
                    break;
                }
            }

            if ( !$findImage ) {
                ImageHelper::destroyImage( $app->imagePath(), $image->src);
                $image->forceDelete();
            }
        }
    }

    private function updateImage ( AppImage $image, $imageData )
    {
        if ( $image->src !== $imageData['src'] ) {
            $imageInfo = pathinfo($imageData['src']);
            $origin = ImageHelper::$pathTemporal . '/' . $imageInfo['basename'];
            $destination = $image->app->imagePath() . '/' . $imageInfo['basename'];
            ImageHelper::move($origin, $destination);
            ImageHelper::destroyImage($image->app->imagePath(), $image->src);
        }

        $image->title = $imageData['title'];
        $image->langs = $imageData['langs'];
        $image->src = $destination;
        $image->priority = $imageData['priority'];
        $image->save();
    }

    private function storeImage ( App $app, $imageData )
    {
        $imageInfo = pathinfo($imageData['src']);
        $origin = ImageHelper::$pathTemporal . '/' . $imageInfo['basename'];
        $destination = $app->imagePath() . '/' . $imageInfo['basename'];

        $src = ImageHelper::move($origin, $destination);

        return AppImage::create([
            'app_id' => $app->id,
            'src' => $src,
            'title' => $imageData['title'],
            'langs' => $imageData['langs'],
            'priority' => $imageData['priority']
        ])->id;
    }
}
