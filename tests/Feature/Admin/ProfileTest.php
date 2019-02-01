<?php
/**
 * Created by PhpStorm.
 * User: ericrf
 * Date: 02/11/2018
 * Time: 22:55
 */

namespace Tests\Feature\Admin;

use App\Http\Controllers\Admin\ImageTemporalController;
use App\Http\Helpers\RoleHelper;
use App\Http\Helpers\UserHelper;
use App\Models\Core\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    public $user, $admin, $roles = [];

    protected function setUp ()
    {
        parent::setUp();

        app()['cache']->forget('spatie.permission.cache');

        foreach ( RoleHelper::getRoles() AS $role ) {
            Role::create(['name' => $role]);
        }

        $this->admin = factory(User::class)->create()->assignRole('admin');
        $this->user = factory(User::class)->create()->assignRole('admin');

        foreach ( RoleHelper::getRoles() AS $role ) {
            array_push($this->roles, [
                'key' => $role,
                'value' => $this->faker->boolean
            ]);
        }
    }

    public function testPostDataIfNotLogged ()
    {
        $this->withExceptionHandling();

        $this
            ->post(route('admin.profile.getData'))
            ->assertStatus(302);
    }

    public function testPostDataIfLogged ()
    {
        $this->withExceptionHandling();
        $profile = User::find($this->admin->id, [ 'id', 'email', 'name', 'avatar' ]);

        $this
            ->actingAs($this->admin)
            ->post(route('admin.profile.getData'))
            ->assertSuccessful()
            ->assertExactJson([
                'id' => $profile->id,
                'email' => $profile->email,
                'name' => $profile->name,
                'avatar' => asset($profile->avatar)
            ]);
    }

    public function testViewIndex ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->admin)
            ->get(route('admin.profile'))
            ->assertSuccessful();
    }

    public function testEmailIsFreeWrongParamsRequest ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->admin)
            ->post(route('admin.profile.emailIsFree' ))
            ->assertStatus(400);
    }

    public function testEmailIsFree ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->admin)
            ->post(route('admin.profile.emailIsFree', [ 'email' => $this->faker->safeEmail ]))
            ->assertSuccessful()
            ->assertExactJson(['result' => true]);

        $this
            ->actingAs($this->admin)
            ->post(route('admin.profile.emailIsFree', [ 'email' => $this->admin->email ]))
            ->assertSuccessful()
            ->assertExactJson(['result' => true]);
    }

    public function testEmailIsNotFree ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->admin)
            ->post(route('admin.profile.emailIsFree', [ 'email' => $this->user->email ]))
            ->assertSuccessful()
            ->assertExactJson(['result' => false]);
    }

    public function testPostUpdateWrongParamsRequest ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->admin)
            ->post(route('admin.profile.update' ))
            ->assertStatus(400);

        $this
            ->actingAs($this->admin)
            ->post(route('admin.profile.update', [ 'email' => $this->faker->safeEmail ] ))
            ->assertStatus(400);

        $this
            ->actingAs($this->admin)
            ->post(route('admin.profile.update', [ 'name' => $this->faker->name ] ))
            ->assertStatus(400);

        $this
            ->actingAs($this->admin)
            ->post(route('admin.profile.update', [
                'email' => $this->faker->safeEmail,
                'name' => $this->faker->name,
                'password' => $this->faker->password,
                'password_confirmation' => $this->faker->password,
                'roles' => $this->roles
            ] ))
            ->assertStatus(400);
    }

    public function testPostUpdateProfileIsNotFree ()
    {
        $this->withExceptionHandling();

        $password = Hash::make($this->faker->password);

        $this
            ->actingAs($this->admin)
            ->post(route('admin.profile.update', [
                'email' => $this->user->email,
                'name' => $this->faker->name,
                'password' => $password,
                'password_confirmation' => $password,
                'roles' => $this->roles
            ]))
            ->assertStatus(400);
    }

    public function testPostUpdateProfileWithoutPassword ()
    {
        $this->withExceptionHandling();

        $avatars = UserHelper::getAvatars();
        $email = $this->faker->safeEmail;
        $name = $this->faker->name;
        $avatar = COUNT($avatars) > 0 ? $avatars[$this->faker->numberBetween(0, COUNT($avatars) - 1)] : null;

        $response = $this
            ->actingAs($this->admin)
            ->post(route('admin.profile.update', [
                'email' => $email,
                'name' => $name,
                'avatar' => $avatar,
                'roles' => $this->roles
            ]))
            ->assertStatus(200);

        $user = User::findOrFail($this->admin->id, [ 'id', 'email', 'name', 'avatar' ]);

        $response->assertJson([
            'result' => true,
            'data' => [
                'user' => $user->toArray()
            ]
        ], true);

        $this->assertTrue($this->admin->email === $email);
        $this->assertTrue($this->admin->name === $name);

        foreach ( $this->roles AS $role ) {
            if ( boolval($role['value']) ) {
                $this->assertTrue($user->hasRole($role['key']));
            } else {
                $this->assertFalse($user->hasRole($role['key']));
            }
        }
    }

    public function testPostUpdateProfileWithPassword ()
    {
        $this->withExceptionHandling();

        $password = Hash::make($this->faker->password);
        $avatars = UserHelper::getAvatars();
        $email = $this->faker->safeEmail;
        $name = $this->faker->name;
        $avatar = COUNT($avatars) > 0 ? $avatars[$this->faker->numberBetween(0, COUNT($avatars) - 1)] : null;

        $response = $this
            ->actingAs($this->admin)
            ->post(route('admin.profile.update', [
                'email' => $email,
                'name' => $name,
                'avatar' =>$avatar,
                'password' => $password,
                'password_confirmation' => $password,
                'roles' => $this->roles
            ]))
            ->assertStatus(200);

        $user = User::findOrFail($this->admin->id, [ 'id', 'email', 'name', 'avatar' ]);

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

    public function testPostUpdateProfileWithAvatarImage ()
    {
        $this->withExceptionHandling();

        $email = $this->faker->safeEmail;
        $name = $this->faker->name;
        $avatar = Storage::disk(ImageTemporalController::$disk )->putFile(
            ImageTemporalController::$temporalPath,
            UploadedFile::fake()->image('random.jpg'),
            ImageTemporalController::$disk
        );

        $response = $this
            ->actingAs($this->admin)
            ->post(route('admin.profile.update', [
                'email' => $email,
                'name' => $name,
                'avatar' => $avatar,
                'roles' => $this->roles
            ]))
            ->assertStatus(200);

        $user = User::findOrFail($this->admin->id, [ 'id', 'email', 'name', 'avatar' ]);

        $response->assertJson([
            'result' => true,
            'data' => [
                'user' => $user->toArray()
            ]
        ], true);

        $this->assertTrue($this->admin->email === $email);
        $this->assertTrue($this->admin->name === $name);

        foreach ( $this->roles AS $role ) {
            if ( boolval($role['value']) ) {
                $this->assertTrue($user->hasRole($role['key']));
            } else {
                $this->assertFalse($user->hasRole($role['key']));
            }
        }
    }
}
