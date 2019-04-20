<?php

namespace App\Http\Controllers\Admin;

use App\Models\Core\Menu;
use App\Models\Core\MenuItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
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
        $title = __('Menus');
        $component = 'index-menu';
        $data = [
            'menus' => Menu::with('items')->get()->all(),
            'langsAvailable' => config('global.langsAvailables')
        ];
        $routes = [
            'getMenus' => route('admin.menus.get'),
            'storeMenu' => route('admin.menus.store'),
            'getPageLocales' => route('admin.pageLocales.get')
        ];

        return view('admin/default', compact( 'data', 'title', 'component', 'routes' ));
    }

    public function get ()
    {
        return Response::json(Menu::with('items')->get());
    }

    public function store ( Request $request )
    {
        $validator = Validator::make($request->all(), [
            'id' => 'nullable|integer',
            'name' => 'required',
            'description' => 'required',
            'items' => 'nullable|array'
        ]);

        if ( !$validator->fails() ) {
            $items = $request->input('items');
            $menu_id = $request->input('id');
            $name = $request->input('name');
            $description = $request->input('description');

            if ( is_null($menu_id) ) {
                $menu = Menu::create([
                    'user_id' => Auth::id(),
                    'name' => $name,
                    'description' => $description
                ]);
            } else {
                $menu = Menu::find($menu_id);
                $menu->name = $name;
                $menu->description = $description;
                $menu->save();
            }

            $this->clearItems($menu, $items);

            if ( !is_null($items) && COUNT($items) > 0 ) {
                foreach ( $items AS $item ) {
                    $this->storeItem($menu, $item);
                }
            }

            return Response::json(['result' => true, 'menu' => Menu::with('items')->find($menu->id)]);
        } else {
            abort(400);
        }
    }

    public function destroy ( Menu $menu )
    {
        $menu->forceDelete();
        return Response::json(['result' => true]);
    }

    protected function storeItem ( Menu $menu, $params )
    {
        if ( is_null($params['id']) ) {
            MenuItem::create([
                'user_id' => Auth::id(),
                'menu_id' => $menu->id,
                'lang' => $params['lang'],
                'label' => $params['label'],
                'type' => $params['type'],
                'page_locale_id' => $params['page_locale_id'],
                'url_external' => $params['url_external'],
                'priority' => $params['priority']
            ]);
        } else {
            $item = MenuItem::find($params['id']);
            $item->lang = $params['lang'];
            $item->label = $params['label'];
            $item->type = $params['type'];
            $item->page_locale_id = $params['page_locale_id'];
            $item->url_external = $params['url_external'];
            $item->priority = $params['priority'];
            $item->save();
        }
    }

    protected function clearItems ( Menu $menu, $items )
    {
        $itemsOrigin = $menu->items()->get();

        foreach ( $itemsOrigin AS $itemOrigin ) {
            $find = false;

            foreach ( $items AS $item ) {
                if ( $itemOrigin->id === intval($item['id']) ) {
                    $find = true;
                    break;
                }
            }

            if ( !$find ) {
                $itemOrigin->forceDelete();
            }
        }
    }
}
