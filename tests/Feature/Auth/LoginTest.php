<?php

namespace Tests\Feature;

use App\Http\Helpers\UserHelper;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use App\Models\Core\User;

class LoginTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected $admin, $user, $password;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedRoles();

        $this->password = $this->faker->password;

        $this->admin = factory(User::class)->create()->attachRole('superadministrator');
        $this->user = factory(User::class)->create([ 'password' => Hash::make($this->password) ])->attachRole('user');
    }

    public function testPermissionsUsers ()
    {
        $this->withExceptionHandling();

        $this->assertFalse($this->user->hasRole('superadministrator'));
        $this->assertTrue($this->user->hasRole('user'));
    }

    public function testPermissionsAdmin ()
    {
        $this->withExceptionHandling();

        $this->assertTrue($this->admin->hasRole('superadministrator'));
        $this->assertFalse($this->admin->hasRole('user'));
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

    public function testPostLoginAjaxBadRequest ()
    {
        $this->withExceptionHandling();

        $this
            ->post(route('login-ajax'), [])
            ->assertSuccessful()
            ->assertJson([ 'result' => false ]);

        $this
            ->post(route('login-ajax'), [
                'email' => $this->faker->freeEmail,
                'password' => $this->faker->password
            ])
            ->assertSuccessful()
            ->assertJson([ 'result' => false ]);
    }

    public function testPostLoginAjaxSuccessful ()
    {
        $this->withExceptionHandling();

        $user = User::find($this->user->id);
        $user->roles = UserHelper::getRolesAssignToUser($user);

        $this
            ->post(route('login-ajax'), [
                'email' => $this->user->email,
                'password' => $this->password
            ])
            ->assertSuccessful()
            ->assertJson([
                'result' => true,
                'user' => $user->toArray(),
                'csrfToken' => csrf_token()
            ]);
    }
}
