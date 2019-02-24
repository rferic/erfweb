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
        $title = __('Profile');
        $component = 'index-profile';
        $user = Auth::user();
        $data = [
            'user' => $user,
            'userRoles' => UserHelper::getRolesAssignToUser($user),
            'roles' => RoleHelper::getRoles(),
            'avatars' => UserHelper::getAvatars()
        ];
        $routes = [
            'emailIsFree' => route('admin.profile.emailIsFree'),
            'profileUpdate' => route('admin.profile.update'),
            'uploadImage' => route('admin.imagesTemporal.upload'),
            'removeImage' => route('admin.imagesTemporal.remove')
        ];

        return view('admin/default', compact( 'data', 'title', 'component', 'routes' ));
    }

    public function update ( Request $request )
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'name' => 'required',
            'avatar' => 'required',
            'password_confirmation' => 'same:password'
        ]);

        $user = Auth::user();
        $image = $request->input('avatar');

        if ( !$validator->fails() && UserHelper::emailIsFree($request->input('email')) ) {
            // Save user
            $avatars = UserHelper::getAvatars();

            // If avatar is a temporal image move to static folder
            if ( !in_array($image, $avatars) && $image !== $user->avatar ) {
                $imageTmp = str_replace('storage/', '', $image);
                $imageNew = str_replace(ImageTemporalController::$temporalPath, 'images/users/' . $user->id . '/avatar', $imageTmp);

                if ( Storage::disk(ImageTemporalController::$disk)->exists($imageTmp) ) {
                    // Remove image if exists
                    if ( Storage::disk(ImageTemporalController::$disk)->exists($imageNew) ) {
                        Storage::disk(ImageTemporalController::$disk)->delete($imageNew);
                    }
                    // Move temporal image to static folder
                    Storage::disk(ImageTemporalController::$disk)->move($imageTmp, $imageNew);
                    $image = $imageNew;
                }
            }
            // Remove old image if is required
            if ( !in_array($user->avatar, $avatars) && $image !== $user->avatar ) {
                $imageOld = str_replace('storage/', '', $user->avatar);

                if ( Storage::disk(ImageTemporalController::$disk)->exists($imageOld) ) {
                    Storage::disk(ImageTemporalController::$disk)->delete($imageOld);
                }
            }

            $user->email = $request->input('email');
            $user->name = $request->input('name');
            $user->avatar = $image;

            if ( !empty($request->input('password')) ) {
                $request->validate([ 'password' => PasswordHelper::validate() ]);
                $user->password = bcrypt($request->input('password'));
            }

            $user->save();
            // Refresh roles
            UserHelper::refreshRoles($user, $request->input('roles'));

            return Response::json([
                'result' => true,
                'data' => [
                    'user' => User::findOrFail(Auth::id(), [ 'id', 'email', 'name', 'avatar' ])
                ]
            ], 200);
        } else {
            return Response::json([], 400);
        }
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
            'roles' => $profile->getRoleNames()
        ]);
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
