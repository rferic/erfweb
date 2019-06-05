<?php

namespace App\Http\Controllers\Front;

use App\Http\Helpers\AuthHelper;
use App\Http\Helpers\LocalizationHelper;
use App\Http\Helpers\PasswordHelper;
use App\Http\Helpers\UserHelper;
use App\Models\Core\App;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function getEmailIsFree ( Request $request )
    {
        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                Rule::unique('users', 'email')->ignore(Auth::id())
            ]
        ]);
        return Response::json(['result' => !$validator->fails()]);
    }

    public function updateBase ( Request $request )
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => [
                'required',
                Rule::unique('users', 'email')->ignore(Auth::id())
            ],
            'avatar' => 'required',
            'lang' => [
                'required',
                'string',
                Rule::in(LocalizationHelper::getSupportedRegional())
            ]
        ]);

        if ( !$validator->fails() ) {
            $user = Auth::user();
            $image = UserHelper::storeAvatar($user, $request);
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->avatar = $image;
            $user->lang = $request->input('lang');
            $user->save();

            return Response::json(['result' => true, 'auth' => AuthHelper::getAuth()]);
        } else {
            abort(400);
        }
    }

    public function updatePassword ( Request $request )
    {
        $validator = Validator::make($request->all(), [
            'password' => PasswordHelper::validate(),
            'password_confirmation' => 'required|same:password'
        ]);

        if ( !$validator->fails() ) {
            $user = Auth::user();
            $user->password = Hash::make($request->input('password'));
            $user->save();

            return Response::json(['result' => true]);
        } else {
            abort(400);
        }
    }

    public function attachApp ( Request $request )
    {
        $validator = Validator::make($request->all(), [ 'app_id' => 'required|exists:apps,id' ]);

        if ( !$validator->fails() ) {
            $app = App::find($request->input('app_id'));

            Auth::user()->apps()->attach([ $app->id => [
                'active' => $app->type === 'protected'
            ]]);

            return Response::json([
                'result' => true,
                'apps' => Auth::user()->apps()->with([ 'locales', 'images' ])->withPivot(['active'])->get()->toArray()
            ]);
        } else {
            abort(400);
        }
    }
}
