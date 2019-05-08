<?php

namespace Tests\Feature\Admin;

use App\Http\Helpers\RoleHelper;
use App\Models\Core\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected $user, $roles;

    protected function setUp ():void
    {
        parent::setUp();

        app()['cache']->forget('spatie.permission.cache');

        $this->roles =  RoleHelper::getRoles();

        foreach ( $this->roles AS $role ) {
            Role::create(['name' => $role]);
        }

        $this->user = factory(User::class)->create()->assignRole('admin');
        factory(User::class, $this->faker->numberBetween(1, 100))->create()->each(function ($user) {
            foreach ( $this->roles AS $role ) {
                if ( $this->faker->boolean ) {
                    $user->assignRole($role);
                }
            }
        });
    }

}
