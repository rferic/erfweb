<?php
/**
 * Created by PhpStorm.
 * User: ericrf
 * Date: 02/11/2018
 * Time: 22:36
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\PasswordHelper;
use App\Http\Helpers\RoleHelper;
use App\Http\Helpers\UserHelper;
use App\Models\Core\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
        $title = __('Profile');
        $component = 'index-profile';
        $user = Auth::user();
        $data = [
            'user' => $user,
            'userRoles' => UserHelper::getRolesAssignToUser($user),
            'roles' => RoleHelper::getRoles()
        ];
        $routes = [
            'emailIsFree' => route('admin.profile.emailIsFree'),
            'profileUpdate' => route('admin.profile.update')
        ];

        return view('admin/default', compact( 'data', 'title', 'component', 'routes' ));
    }

    public function update ( Request $request )
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'name' => 'required',
            'password_confirmation' => 'same:password'
        ]);

        if ( !$validator->fails() && UserHelper::emailIsFree($request->input('email')) ) {
            // Save user
            $user = Auth::user();
            $user->email = $request->input('email');
            $user->name = $request->input('name');

            if ( !empty($request->input('password')) ) {
                $request->validate([ 'password' => PasswordHelper::validate() ]);
                $user->password = bcrypt($request->input('password'));
            }

            $user->save();
            // Refresh roles
            UserHelper::refreshRoles($user, $request->input('roles'));

            return Response::json([ 'result' => true ], 200);
        } else {
            return Response::json([], 400);
        }
    }

    public function getData ()
    {
        $id = Auth::id();
        return Response::json(User::find($id, [ 'id', 'email', 'name' ]));
    }

    public function emailIsFree ( Request $request )
    {
        $validator = Validator::make($request->all(), [
           'email' => 'required'
        ]);

        if ( !$validator->fails() ) {
            return Response::json([ 'result' => UserHelper::emailIsFree($request->input('email')) ], 200);
        } else {
            return Response::json([], 400);
        }
    }
}
