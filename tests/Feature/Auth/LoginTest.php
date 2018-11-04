<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\Core\User;

class LoginTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected $admin, $user, $password;

    protected function setUp ()
    {
        parent::setUp();

        app()['cache']->forget('spatie.permission.cache');

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'public']);

        $this->password = $this->faker->password;

        $this->admin = factory(User::class)->create()->assignRole('admin');
        $this->user = factory(User::class)->create([ 'password' => Hash::make($this->password) ])->assignRole('public');
    }

    public function testPermissionsUsers ()
    {
        $this->withExceptionHandling();

        $this->assertTrue(!$this->user->hasRole('admin'));
        $this->assertTrue($this->user->hasRole('public'));
    }

    public function testPermissionsAdmin ()
    {
        $this->withExceptionHandling();

        $this->assertTrue($this->admin->hasRole('admin'));
        $this->assertTrue(!$this->admin->hasRole('public'));
    }

    public function testRedirectIfLogged ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->get(route('login'))
            ->assertStatus(302)
            ->assertRedirect(route('account'));
    }

    public function testDisplayedIfNotLogged ()
    {
        $this->withExceptionHandling();

        $this
            ->get(route('login'))
            ->assertSuccessful();
    }

    public function testPostIsVoid ()
    {
        $this->withExceptionHandling();

        $this
            ->post(route('login'), [])
            ->assertSessionHasErrors('email')
            ->assertSessionHasErrors('password');

        $this->assertGuest();
    }

    public function testPostWrongFields ()
    {
        $this->withExceptionHandling();

        // Form post password is missing
        $this
            ->post(route('login'), [
                'email' => $this->user->email,
                'password' => $this->faker->password
            ])
            ->assertSessionHasErrors();
        // Form post email is missing
        $this
            ->post(route('login'), [
                'email' => $this->faker->email,
                'password' => $this->password
            ])
            ->assertSessionHasErrors();

        $this->assertGuest();
    }

    public function testPostSuccess ()
    {
        $this->withExceptionHandling();

        $this
            ->post(route('login'), [
                'email' => $this->user->email,
                'password' => $this->password
            ])
            ->assertStatus(302)
            ->assertRedirect(route('account'));

        $this->assertAuthenticatedAs($this->user);
    }

    public function testLogout ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->post(route('logout'))
            ->assertSessionHasNoErrors()
            ->assertStatus(302)
            ->assertRedirect('/');

        $this->assertGuest();
    }
}
