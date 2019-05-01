<?php

namespace Tests\Feature\Admin;

use App\Http\Controllers\Admin\ImageTemporalController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Helpers\RoleHelper;
use App\Http\Helpers\UserHelper;
use App\Models\Core\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class Usertest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    public $admin, $roles = [];

    protected function setUp ():void
    {
        parent::setUp();

        app()['cache']->forget('spatie.permission.cache');

        foreach ( RoleHelper::getRoles() AS $role ) {
            array_push($this->roles, [
                'key' => $role,
                'value' => $this->faker->boolean
            ]);
        }

        foreach ( $this->roles AS $role ) {
            Role::create(['name' => $role['key']]);
        }

        $this->admin = factory(User::class)->create()->assignRole('admin');
        factory(User::class, $this->faker->numberBetween(1, 100))->create()->each(function ($user) {
            foreach ( $this->roles AS $role ) {
                if ( $this->faker->boolean ) {
                    $user->assignRole($role['key']);
                }
            }
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
                'roles' => $user->getRoleNames()
            ]);
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
