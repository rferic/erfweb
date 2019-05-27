<?php

namespace Tests\Feature\Front;

use App\Models\Core\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected $user;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedRoles();
        $this->user = factory(User::class)->create()->attachRole('user');
    }

    public function testPostGetEmailIsFreeKO ()
    {
        $this->withExceptionHandling();

        $this
            ->post(route('email-is-free'), [])
            ->assertSuccessful()
            ->assertJson([ 'result' => false ]);

        $this
            ->post(route('email-is-free'), [ 'email' => $this->user->user ])
            ->assertSuccessful()
            ->assertJson([ 'result' => false ]);
    }

    public function testPostGetEmailIsFreeOK ()
    {
        $this->withExceptionHandling();

        $this
            ->post(route('email-is-free'), [ 'email' => $this->faker->safeEmail ])
            ->assertSuccessful()
            ->assertJson([ 'result' => true ]);
    }
}
