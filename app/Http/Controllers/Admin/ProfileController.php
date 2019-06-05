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
use Illuminate\Support\Facades\Storage;
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
        $title = __('This is your profile');
        $description = __('This is you, do you want to change something?');
        $component = 'index-profile';
        $user = Auth::user();
        $data = [
            'user' => $user,
            'userRoles' => UserHelper::getRolesAssignToUser($user),
            'roles' => RoleHelper::getRoles(),
            'avatars' => UserHelper::getAvatars()
        ];
        $routes = [
            'emailIsFree' => route('admin.users.emailIsFree', $user->id),
            'getUserData' => route('admin.users.getData', $user->id),
            'userUpdate' => route('admin.users.update', $user->id),
            'uploadImage' => route('admin.imagesTemporal.upload'),
            'removeImage' => route('admin.imagesTemporal.remove'),
            'getAppsToAttach' => route('admin.users.getAppsToAttach', $user->id),
            'attachApp' => route('admin.users.attachApp', $user->id),
            'detachApp' => route('admin.users.detachApp', $user->id),
            'enableAttachApp' => route('admin.users.enableAttachApp', $user->id),
            'disableAttachApp' => route('admin.users.disableAttachApp', $user->id)
        ];

        return view('admin/default', compact( 'data', 'title', 'description', 'component', 'routes' ));
    }

    public function getData ()
    {
        $id = Auth::id();
        $profile = User::findOrFail($id, [ 'id', 'email', 'name', 'avatar' ]);
        return Response::json([
            'id' => $profile->id,
            'email' => $profile->email,
            'name' => $profile->name,
            'avatar' => asset($profile->avatar),
            'roles' => $profile->roles
        ]);
    }
}
