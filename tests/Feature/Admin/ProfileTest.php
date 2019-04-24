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

    protected function setUp ():void
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
                'avatar' => asset($profile->avatar),
                'roles' => $profile->getRoleNames()
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

    public function testPostGetData ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->admin)
            ->post(route('admin.profile.getData'))
            ->assertSuccessful()
            ->assertExactJson([
                'id' => $this->admin->id,
                'email' => $this->admin->email,
                'name' => $this->admin->name,
                'avatar' => asset($this->admin->avatar),
                'roles' => $this->admin->getRoleNames()
            ]);
    }
}
