<?php
/**
 * Created by PhpStorm.
 * User: ericrf
 * Date: 02/11/2018
 * Time: 22:55
 */

namespace Tests\Feature\Admin;

use App\Models\Core\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;

    public $user, $admin;

    protected function setUp ()
    {
        parent::setUp();

        app()['cache']->forget('spatie.permission.cache');

        Role::create(['name' => 'admin']);

        $this->admin = factory(User::class)->create()->assignRole('admin');
    }

    public function testGetDataIfNotLogged ()
    {
        $this->withExceptionHandling();

        $this
            ->post(route('admin.profile.getData'))
            ->assertStatus(302);
    }

    public function testGetDataIfLogged ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->admin)
            ->post(route('admin.profile.getData'))
            ->assertSuccessful()
            ->assertExactJson(User::find($this->admin->id, [ 'id', 'email', 'name' ])->toArray());
    }
}
