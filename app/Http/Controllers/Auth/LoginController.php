<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Helpers\UserHelper;
use Arcanedev\Localization\Facades\Localization;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/account';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->redirectTo = Localization::localizeURL(route('account'));
    }

    public function loginAjax ( Request $request )
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users,email',
            'password' => 'required'
        ]);

        if ( !$validator->fails() ) {
            if (Auth::attempt($request->only('email', 'password'))) {
                $user = Auth::user();
                $user->roles = UserHelper::getRolesAssignToUser($user);

                return Response::json([
                    'result' => true,
                    'user' => $user,
                    'csrfToken' => csrf_token()
                ]);
            }
        }

        return Response::json(['result' => false]);
    }
}
