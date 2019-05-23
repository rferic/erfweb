<?php

namespace App\Http\Controllers\Auth;

use App\Http\Helpers\UserHelper;
use App\Models\Core\User;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Http\Helpers\PasswordHelper;
use Faker\Factory as Faker;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        $this->redirectTo = localization()->localizeURL('account');
    }

    public function registerAjax ( Request $request )
    {
        if ( !$this->validator($request->all())->fails() ) {
            event(new Registered($user = $this->create($request->all())));
            $this->guard()->login($user);
            $user = Auth::user();
            $user->roles = UserHelper::getRolesAssignToUser($user);

            return Response::json([
                'result' => true,
                'user' => $user
            ]);
        }

        return Response::json(['result' => false]);
    }

        /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => PasswordHelper::validate(),
            'terms' => 'required'
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $faker = Faker::create();
        $avatars = UserHelper::getAvatars();

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'avatar' => COUNT($avatars) > 0 ? $avatars[$faker->numberBetween(0, COUNT($avatars) - 1)] : null
        ])->attachRole('user');
    }
}
