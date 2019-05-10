<?php

namespace Tests\Feature\Admin;

use App\Http\Helpers\AdminMenuHelper;
use App\Models\Core\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AdminMenuTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected $user;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedRoles();

        $this->user = factory(User::class)->create()->attachRole('superadministrator');
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
