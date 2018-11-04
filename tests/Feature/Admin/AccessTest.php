<?php
/**
 * Created by PhpStorm.
 * User: ericrf
 * Date: 27/10/2018
 * Time: 14:00
 */

namespace Tests\Feature\Admin;

use App\Models\Core\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AccessTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected $user, $admin, $adminNotVerified;

    protected function setUp ()
    {
        parent::setUp();

        app()['cache']->forget('spatie.permission.cache');

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'public']);

        $this->user = factory(User::class)->create()->assignRole('public');
        $this->admin = factory(User::class)->create()->assignRole('admin');
        $this->adminNotVerified = factory(User::class)->create([ 'email_verified_at' => null ])->assignRole('admin');
    }

    public function testAccessUserNotLogged ()
    {
        $this->withExceptionHandling();

        $this
            ->get(route('admin.dashboard'))
            ->assertStatus(302)
            ->assertRedirect(route('verification.notice'));
    }

    public function testAccessUserLogged ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->get(route('admin.dashboard'))
            ->assertStatus(403);
    }

    public function testAccessAdminNotVerifiedLogged ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->adminNotVerified)
            ->get(route('admin.dashboard'))
            ->assertStatus(302)
            ->assertRedirect(route('verification.notice'));
    }

    public function testAccessAdminVerifiedLogged ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertSuccessful()
            ->assertStatus(200);
    }
}
