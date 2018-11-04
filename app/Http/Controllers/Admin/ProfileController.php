<?php
/**
 * Created by PhpStorm.
 * User: ericrf
 * Date: 02/11/2018
 * Time: 22:36
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Core\User;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
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
        $user = Auth::user();
        return view('admin/profile', compact($user));
    }

    public function getData ()
    {
        $id = Auth::id();
        return Response::json(User::find($id, [ 'id', 'email', 'name' ]));
    }
}
