<?php

namespace Tests\Feature\Admin;

use App\Http\Controllers\Admin\ImageTemporalController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Helpers\RoleHelper;
use App\Http\Helpers\UserHelper;
use App\Models\Core\App;
use App\Models\Core\AppLocale;
use App\Models\Core\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Usertest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    public $admin, $roles = [], $numAppsPriv, $wordSearchApp;

    protected function setUp ():void
    {
        parent::setUp();

        $this->seedRoles();

        foreach ( RoleHelper::getRoles() AS $role ) {
            array_push($this->roles, [
                'key' => $role,
                'value' => $this->faker->boolean
            ]);
        }

        $this->admin = factory(User::class)->create()->attachRole('superadministrator');

        factory(User::class, $this->faker->numberBetween(1, 100))->create()->each(function ($user) {
            $hasAnyRole = false;

            foreach ( $this->roles AS $role ) {
                if ( $this->faker->boolean ) {
                    $hasAnyRole = true;
                    $user->attachRole($role['key']);
                }
            }

            if ( !$hasAnyRole ) {
                $user->attachRole('user');
            }
        });

        $this->numAppsPriv = $this->faker->numberBetween(1, 20);
        $this->wordSearchApp = $this->faker->word;
        factory(App::class, $this->numAppsPriv)->create([
            'vue_component' => $this->wordSearchApp,
            'type' => 'private'
        ])->each(function ($app) {
            factory(AppLocale::class, 2)->create([
                'app_id' => $app,
                'title' => $this->wordSearchApp,
                'lang' => $this->faker->word
            ]);
        });
        factory(App::class, $this->faker->numberBetween(1, 20))->create([
            'vue_component' => $this->wordSearchApp,
            'type' => 'public'
        ])->each(function ($app) {
            factory(AppLocale::class, 2)->create([
                'app_id' => $app,
                'title' => $this->wordSearchApp,
                'lang' => $this->faker->word
            ]);
        });
    }

    public function testEmailIsFreeWrongParamsRequest ()
    {
        $this->withExceptionHandling();

        $user = User::all()->random();

        $this
            ->actingAs($this->admin)
            ->post(route('admin.users.emailIsFree', $user->id ), [])
            ->assertStatus(400);
    }

    public function testEmailIsFree ()
    {
        $this->withExceptionHandling();

        $user = User::all()->random();

        $this
            ->actingAs($this->admin)
            ->post(route('admin.users.emailIsFree', $user->id), [ 'email' => $this->faker->safeEmail ])
            ->assertSuccessful()
            ->assertExactJson(['result' => true]);

        $this
            ->actingAs($this->admin)
            ->post(route('admin.users.emailIsFree', $user->id), [ 'email' => $user->email ])
            ->assertSuccessful()
            ->assertExactJson(['result' => true]);
    }

    public function testEmailIsNotFree ()
    {
        $this->withExceptionHandling();

        $user = User::all()->random();
        $user2 = User::all()->random();

        $this
            ->actingAs($this->admin)
            ->post(route('admin.users.emailIsFree', $user->id), [ 'email' => $user2->email ])
            ->assertSuccessful()
            ->assertExactJson(['result' => $user->email === $user2->email]);
    }

    public function testPostGetUsers ()
    {
        $this->withExceptionHandling();

        $controller = new UserControllerTest();

        $params = [
            'filters' => [
                'text' => $this->faker->boolean ? '' : User::all()->random()->email,
                'role' => $this->faker->boolean ? null : $this->roles[$this->faker->numberBetween(0, COUNT($this->roles)-1)]['key'],
                'banned' => $this->faker->boolean ? true : false
            ],
            'perPage' => $this->faker->numberBetween(1, 100),
        ];

        $responseToAssert = $controller->getTesting($params);
        $response = $this
            ->actingAs($this->admin)
            ->post(route('admin.users.get'), $params)
            ->assertSuccessful();

        $response = json_decode(json_encode($response))->baseResponse->original;
        $responseToAssert = json_decode(json_encode($responseToAssert))->original;

        $this->assertEquals($response->per_page, $params['perPage']);
        $this->assertEquals(
            intval($response->per_page) > intval($response->total)
                ? intval($response->total)
                : intval($response->per_page),
            COUNT($response->data)
        );
        $this->assertEquals($response->total, $responseToAssert->total);
    }

    public function testPostGetData ()
    {
        $this->withExceptionHandling();

        $user = User::all()->random();

        $this
            ->actingAs($this->admin)
            ->post(route('admin.users.getData', $user->id))
            ->assertSuccessful()
            ->assertExactJson([
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'avatar' => asset($user->avatar),
                'roles' => $user->roles->toArray()
            ]);
    }

    public function testPostGetAppsToAttach ()
    {
        $this->withExceptionHandling();

        $user = User::all()->random();

        $this
            ->actingAs($this->admin)
            ->post(route('admin.users.getAppsToAttach', $user->id))
            ->assertSuccessful()
            ->assertJsonCount($this->numAppsPriv);

        $this
            ->actingAs($this->admin)
            ->post(route('admin.users.getAppsToAttach', $user->id), [ 'text' => $this->wordSearchApp ])
            ->assertSuccessful()
            ->assertJsonCount($this->numAppsPriv);

        $app = App::where('type', '!=', 'public')
            ->whereDoesntHave('users', function ($query) use ($user) {
                $query->where('id', $user->id);
            })->get()->random();

        $user->apps()->attach([ $app->id ]);

        $this
            ->actingAs($this->admin)
            ->post(route('admin.users.getAppsToAttach', $user->id), [ 'text' => $this->wordSearchApp ])
            ->assertSuccessful()
            ->assertJsonCount($this->numAppsPriv - 1);
    }

    public function testPostAttachAppBadRequest ()
    {
        $this->withExceptionHandling();

        $user = User::all()->random();

        $this
            ->actingAs($this->admin)
            ->post(route('admin.users.attachApp', $user->id))
            ->assertStatus(400);

        $this
            ->actingAs($this->admin)
            ->post(route('admin.users.attachApp', $user->id), [ 'app_id' => $this->faker->word ])
            ->assertStatus(400);
    }

    public function testPostAttachAppSuccessful ()
    {
        $this->withExceptionHandling();

        $user = User::all()->random();
        $app = App::all()->random();

        $this
            ->actingAs($this->admin)
            ->post(route('admin.users.attachApp', $user->id), [ 'app_id' => $app->id ])
            ->assertSuccessful()
            ->assertJson([
                'result' => true,
                'apps' => $user->apps()->with([ 'locales', 'images' ])->withPivot(['active'])->get()->toArray()
            ]);

        $this->assertTrue($user->apps()->where('id', $app->id)->exists());
    }

    public function testPostDetachAppBadRequest ()
    {
        $this->withExceptionHandling();

        $user = User::all()->random();

        $this
            ->actingAs($this->admin)
            ->post(route('admin.users.detachApp', $user->id))
            ->assertStatus(400);

        $this
            ->actingAs($this->admin)
            ->post(route('admin.users.detachApp', $user->id), [ 'app_id' => $this->faker->word ])
            ->assertStatus(400);
    }

    public function testPostDetachAppSuccessful ()
    {
        $this->withExceptionHandling();

        $user = User::all()->random();
        $app = App::all()->random();
        $user->apps()->attach([ $app->id ]);

        $this
            ->actingAs($this->admin)
            ->post(route('admin.users.detachApp', $user->id), [ 'app_id' => $app->id ])
            ->assertSuccessful()
            ->assertExactJson([
                'result' => true,
                'apps' => $user->apps()->with([ 'locales', 'images' ])->withPivot(['active'])->get()->toArray()
            ]);

        $this->assertFalse($user->apps()->where('id', $app->id)->exists());
    }

    public function testPostEnableAttachAppBadRequest ()
    {
        $this->withExceptionHandling();

        $user = User::all()->random();

        $this
            ->actingAs($this->admin)
            ->post(route('admin.users.enableAttachApp', $user->id))
            ->assertStatus(400);

        $this
            ->actingAs($this->admin)
            ->post(route('admin.users.enableAttachApp', $user->id), [ 'app_id' => $this->faker->word ])
            ->assertStatus(400);
    }

    public function testPostEnableAttachAppSuccessful ()
    {
        $this->withExceptionHandling();

        $user = User::all()->random();
        $app = App::all()->random();
        $user->apps()->attach([ $app->id => [ 'active' => false ] ]);

        $this
            ->actingAs($this->admin)
            ->post(route('admin.users.enableAttachApp', $user->id), [ 'app_id' => $app->id ])
            ->assertSuccessful()
            ->assertExactJson([
                'result' => true,
                'apps' => $user->apps()->with([ 'locales', 'images' ])->withPivot(['active'])->get()->toArray()
            ]);

        $this->assertTrue($user->apps()->where('id', $app->id)->where('active', true)->exists());
    }

    public function testPostDisableAttachAppBadRequest ()
    {
        $this->withExceptionHandling();

        $user = User::all()->random();

        $this
            ->actingAs($this->admin)
            ->post(route('admin.users.disableAttachApp', $user->id))
            ->assertStatus(400);

        $this
            ->actingAs($this->admin)
            ->post(route('admin.users.disableAttachApp', $user->id), [ 'app_id' => $this->faker->word ])
            ->assertStatus(400);
    }

    public function testPostDisableAttachAppSuccessful ()
    {
        $this->withExceptionHandling();

        $user = User::all()->random();
        $app = App::all()->random();
        $user->apps()->attach([ $app->id => [ 'active' => true ] ]);

        $this
            ->actingAs($this->admin)
            ->post(route('admin.users.disableAttachApp', $user->id), [ 'app_id' => $app->id ])
            ->assertSuccessful()
            ->assertExactJson([
                'result' => true,
                'apps' => $user->apps()->with([ 'locales', 'images' ])->withPivot(['active'])->get()->toArray()
            ]);

        $this->assertTrue($user->apps()->where('id', $app->id)->where('active', false)->exists());
    }

    public function testPostUpdateWrongParamsRequest ()
    {
        $this->withExceptionHandling();

        $user = User::all()->random();

        $this
            ->actingAs($this->admin)
            ->post(route('admin.users.update', $user->id))
            ->assertStatus(400);

        $this
            ->actingAs($this->admin)
            ->post(route('admin.users.update', $user->id), [ 'email' => $this->faker->safeEmail ] )
            ->assertStatus(400);

        $this
            ->actingAs($this->admin)
            ->post(route('admin.users.update', $user->id), [ 'name' => $this->faker->name ] )
            ->assertStatus(400);

        $this
            ->actingAs($this->admin)
            ->post(route('admin.users.update', $user->id), [
                'email' => $this->faker->safeEmail,
                'name' => $this->faker->name,
                'password' => $this->faker->password,
                'password_confirmation' => $this->faker->password,
                'roles' => $this->roles
            ] )
            ->assertStatus(400);
    }

    public function testPostUpdateUserIsNotFree ()
    {
        $this->withExceptionHandling();

        $user = User::all()->random();
        $password = Hash::make($this->faker->password);

        $this
            ->actingAs($this->admin)
            ->post(route('admin.users.update', $user->id), [
                'email' => $user->email,
                'name' => $this->faker->name,
                'password' => $password,
                'password_confirmation' => $password,
                'roles' => $this->roles
            ])
            ->assertStatus(400);
    }

    public function testPostUpdateUserWithoutPassword ()
    {
        $this->withExceptionHandling();

        $user = User::all()->random();
        $avatars = UserHelper::getAvatars();
        $email = $this->faker->safeEmail;
        $name = $this->faker->name;
        $avatar = COUNT($avatars) > 0 ? $avatars[$this->faker->numberBetween(0, COUNT($avatars) - 1)] : null;

        $response = $this
            ->actingAs($this->admin)
            ->post(route('admin.users.update', $user->id), [
                'email' => $email,
                'name' => $name,
                'avatar' => $avatar,
                'roles' => $this->roles
            ])
            ->assertStatus(200);

        $user = User::findOrFail($user->id, [ 'id', 'email', 'name', 'avatar' ]);

        $response->assertJson([
            'result' => true,
            'data' => [
                'user' => $user->toArray()
            ]
        ], true);

        $this->assertEquals($user->email, $email);
        $this->assertEquals($user->name, $name);

        foreach ( $this->roles AS $role ) {
            if ( boolval($role['value']) ) {
                $this->assertTrue($user->hasRole($role['key']));
            } else {
                $this->assertFalse($user->hasRole($role['key']));
            }
        }
    }

    public function testPostUpdateUserWithPassword ()
    {
        $this->withExceptionHandling();

        $user = User::all()->random();
        $password = Hash::make($this->faker->password);
        $avatars = UserHelper::getAvatars();
        $email = $this->faker->safeEmail;
        $name = $this->faker->name;
        $avatar = COUNT($avatars) > 0 ? $avatars[$this->faker->numberBetween(0, COUNT($avatars) - 1)] : null;

        $response = $this
            ->actingAs($this->admin)
            ->post(route('admin.users.update', $user->id), [
                'email' => $email,
                'name' => $name,
                'avatar' =>$avatar,
                'password' => $password,
                'password_confirmation' => $password,
                'roles' => $this->roles
            ])
            ->assertStatus(200);

        $user = User::findOrFail($user->id, [ 'id', 'email', 'name', 'avatar' ]);

        $response->assertExactJson([
            'result' => true,
            'data' => [
                'user' => $user->toArray()
            ]
        ]);

        foreach ( $this->roles AS $role ) {
            if ( $role['value'] ) {
                $this->assertTrue($user->hasRole($role['key']));
            } else {
                $this->assertFalse($user->hasRole($role['key']));
            }
        }
    }

    public function testPostUpdateUserWithAvatarImage ()
    {
        $this->withExceptionHandling();

        $user = User::all()->random();
        $email = $this->faker->safeEmail;
        $name = $this->faker->name;
        $avatar = Storage::disk(ImageTemporalController::$disk)->putFile(
            ImageTemporalController::$temporalPath,
            UploadedFile::fake()->image('random.jpg'),
            ImageTemporalController::$disk
        );

        $response = $this
            ->actingAs($this->admin)
            ->post(route('admin.users.update', $user->id), [
                'email' => $email,
                'name' => $name,
                'avatar' => $avatar,
                'roles' => $this->roles
            ])
            ->assertStatus(200);

        $user = User::findOrFail($user->id, [ 'id', 'email', 'name', 'avatar' ]);

        $response->assertJson([
            'result' => true,
            'data' => [
                'user' => $user->toArray()
            ]
        ], true);

        $this->assertEquals($user->email, $email);
        $this->assertEquals($user->name, $name);

        foreach ( $this->roles AS $role ) {
            if ( boolval($role['value']) ) {
                $this->assertTrue($user->hasRole($role['key']));
            } else {
                $this->assertFalse($user->hasRole($role['key']));
            }
        }
        // Remove test image
        Storage::disk(ImageTemporalController::$disk)->delete('images/users/' . $user->id . '/avatar' . str_replace(ImageTemporalController::$temporalPath, '', $avatar));
    }

    public function testPostDisable ()
    {
        $this->withExceptionHandling();

        $user = User::all()->random();

        $this
            ->actingAs($this->admin)
            ->post(route('admin.users.disable', $user->id))
            ->assertSuccessful();

        $this->assertTrue(User::withTrashed()->find($user->id)->trashed());

        $this
            ->actingAs($this->admin)
            ->post(route('admin.users.disable', $user->id))
            ->assertSuccessful();
    }

    public function testPostEnable ()
    {
        $this->withExceptionHandling();

        $user = User::all()->random();
        $user->delete();

        $this
            ->actingAs($this->admin)
            ->post(route('admin.users.enable', $user->id))
            ->assertSuccessful();

        $this->assertFalse(User::withTrashed()->find($user->id)->trashed());

        $this
            ->actingAs($this->admin)
            ->post(route('admin.users.enable', $user->id))
            ->assertSuccessful();
    }

    public function testDeleteDestroy ()
    {
        $this->withExceptionHandling();

        $user = User::all()->random();

        $this
            ->actingAs($this->admin)
            ->delete(route('admin.users.destroy', $user->id))
            ->assertSuccessful();

        $this->assertNull(User::withTrashed()->find($user->id));

        $user = User::all()->random();
        $user->delete();

        $this
            ->actingAs($this->admin)
            ->delete(route('admin.users.destroy', $user->id))
            ->assertSuccessful();

        $this->assertNull(User::withTrashed()->find($user->id));
    }
}

class UserControllerTest extends UserController
{
    public function getTesting ( $params )
    {
        return Response::json($this->getUsers($params['filters'], $params['perPage']));
    }
}
