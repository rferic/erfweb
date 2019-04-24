<?php

namespace App\Http\Controllers\Admin;

use App\Http\Helpers\PasswordHelper;
use App\Http\Helpers\RoleHelper;
use App\Http\Helpers\UserHelper;
use App\Models\Core\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
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
        $vieOptions = $this->getIndexViewOptions('public');
        $title = __('Users');
        $component = $vieOptions['component'];
        $data = $vieOptions['data'];
        $routes = $vieOptions['routes'];

        return view('admin/default', compact( 'data', 'title', 'component', 'routes' ));
    }

    public function indexAdmins ()
    {
        $vieOptions = $this->getIndexViewOptions('admin');
        $title = __('Admin users');
        $component = $vieOptions['component'];
        $data = $vieOptions['data'];
        $routes = $vieOptions['routes'];

        return view('admin/default', compact( 'data', 'title', 'component', 'routes' ));
    }

    public function detail ( User $user )
    {
        $title = __('User') . ': ' . $user->name;
        $component = 'index-profile';
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
            'removeImage' => route('admin.imagesTemporal.remove')
        ];

        return view('admin/default', compact( 'data', 'title', 'component', 'routes' ));
    }

    public function get ( Request $request )
    {
        $perPage = $request->input('perPage');
        $filters = $request->input('filters');

        return Response::json($this->getUsers($filters, $perPage ));
    }

    public function getData ( User $user )
    {
        return Response::json([
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'avatar' => asset($user->avatar),
            'roles' => $user->getRoleNames()
        ]);
    }

    public function emailIsFree ( User $user, Request $request )
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required'
        ]);

        if ( !$validator->fails() ) {
            return Response::json([ 'result' => UserHelper::emailIsFree($user, $request->input('email')) ], 200);
        } else {
            return Response::json([], 400);
        }
    }

    public function update ( User $user, Request $request )
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'name' => 'required',
            'avatar' => 'required',
            'password_confirmation' => 'nullable|same:password'
        ]);

        $image = $request->input('avatar');

        if ( !$validator->fails() && UserHelper::emailIsFree($user, $request->input('email')) ) {
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
                    'user' => User::findOrFail($user->id, [ 'id', 'email', 'name', 'avatar' ])
                ]
            ], 200);
        } else {
            return Response::json([], 400);
        }
    }

    public function disable ( User $user )
    {
        $user->delete();
        return Response::json([ 'result' => true ], 200);
    }

    public function enable ( User $user )
    {
        $user->restore();
        return Response::json([ 'result' => true ], 200);
    }

    public function destroy ( User $user )
    {
        $user->forceDelete();
        return Response::json([ 'result' => true ], 200);
    }

    private function getIndexViewOptions ( $role, $user = null )
    {
        return [
            'component' => 'index-user',
            'data' => [
                'role' => $role,
                'defaultUser' => $user
            ],
            'routes' => [
                'getUsers' => route('admin.users.get')
            ]
        ];
    }

    private function getUsers ( $filters, $perPage )
    {
        $query = User::role($filters['role'])->query();

        if ( isset($filters['banned']) && $filters['banned'] ) {
            $query = $query->withTrashed();
        }

        if (  isset($filters['text']) && !is_null($filters['text']) ) {
            $text = $filters['text'];
            $query->where(function ($query) use ($text) {
                return $query
                    ->orWhere('name', 'LIKE', '%' . $text. '%')
                    ->orWhere('email', 'LIKE', '%' . $text. '%');
            });
        }

        return $query->paginate($perPage);
    }
}
