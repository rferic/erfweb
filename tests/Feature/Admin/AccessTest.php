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
use Tests\TestCase;

class AccessTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected $user, $superadmin, $admin, $adminNotVerified;

    protected function setUp ():void
    {
        parent::setUp();

        $this->seedRoles();

        $this->user = factory(User::class)->create()->attachRole('user');
        $this->superadmin = factory(User::class)->create()->attachRole('superadministrator');
        $this->admin = factory(User::class)->create()->attachRole('administrator');
        $this->adminNotVerified = factory(User::class)->create([ 'email_verified_at' => null ])->attachRole('superadministrator');
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
            ->actingAs($this->superadmin)
            ->get(route('admin.dashboard'))
            ->assertSuccessful()
            ->assertStatus(200);

        $this
            ->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertSuccessful()
            ->assertStatus(200);
    }
}
