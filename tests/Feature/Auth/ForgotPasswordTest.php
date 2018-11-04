<?php

namespace Tests\Feature;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use App\Models\Core\User;

class ForgotPasswordTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected $user;

    protected function setUp ()
    {
        parent::setUp();

        app()['cache']->forget('spatie.permission.cache');

        Role::create(['name' => 'public']);

        $this->user = factory(User::class)->create()->assignRole('public');
    }

    public function testDisplayedIfNotLogged ()
    {
        $this->withExceptionHandling();

        $this
            ->get(route('password.request'))
            ->assertSuccessful();
    }

    public function testRedirectIfLogged ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->get(route('password.request'))
            ->assertStatus(302)
            ->assertRedirect(route('account'));

    }

    public function testPostWorngEmail ()
    {
        $this->withExceptionHandling();
        Notification::fake();
        $email = $this->faker->email;
        // Assert if is required
        $this
            ->post(route('password.email'), [])
            ->assertSessionHasErrors('email');
        // Assert if email format is required
        $this
            ->post(route('password.email'), [ 'email' => $this->faker->name])
            ->assertSessionHasErrors('email');
        // Assert if validate is a exists email
        $this
            ->post(route('password.email'), [ 'email' => $email])
            ->assertSessionHasErrors('email');
        // Assert not sending email if is not a register email
        Notification::assertNotSentTo(factory(User::class)->make(), ResetPassword::class);
    }

    public function testPostSuccess ()
    {
        $this->withExceptionHandling();
        Notification::fake();

        $this
            ->post(route('password.email'), [ 'email' => $this->user->email ])
            ->assertSessionHasNoErrors();
        // Assert if has been registered token password reset
        $this->assertNotNull($token = DB::table('password_resets')->first());
        // Assert if sending email with token
        Notification::assertSentTo($this->user, ResetPassword::class, function ($notification, $channels) use ($token) {
            return Hash::check($notification->token, $token->token) === true;
        });
    }
}
