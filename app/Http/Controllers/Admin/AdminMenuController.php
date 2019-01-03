<?php
/**
 * Created by PhpStorm.
 * User: ericrf
 * Date: 01/12/2018
 * Time: 15:46
 */

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Helpers\AdminMenuHelper;
use Illuminate\Support\Facades\Response;


class AdminMenuController extends Controller
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

    public function get ()
    {
        return Response::json([ 'result' => AdminMenuHelper::getMenu() ], 200);
    }
}
