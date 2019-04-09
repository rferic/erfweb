<?php

namespace Tests\Feature\Admin;

use App\Http\Helpers\AdminMenuHelper;
use App\Models\Core\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Spatie\Permission\Models\Role;

class AdminMenuTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected $user;

    protected function setUp (): void
    {
        parent::setUp();

        app()['cache']->forget('spatie.permission.cache');

        Role::create(['name' => 'admin']);

        $this->user = factory(User::class)->create()->assignRole('admin');
    }

    public function testGet()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->post(route('admin.adminMenu'), [])
            ->assertSuccessful()
            ->assertExactJson([ 'result' => AdminMenuHelper::getMenu() ]);
    }
}
