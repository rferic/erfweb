<?php

namespace App\Http\Controllers\Admin;

use App\Models\Core\Menu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class MenuController extends Controller
{
    public function get ()
    {
        return Response::json(Menu::with('items')->get());
    }
}
