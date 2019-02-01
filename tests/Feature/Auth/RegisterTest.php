<?php

namespace Tests\Feature;

use App\Http\Helpers\UserHelper;
use App\Models\Core\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class RegisterTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected function setUp ()
    {
        parent::setUp();

        app()['cache']->forget('spatie.permission.cache');

        Role::create(['name' => 'public']);
    }

    public function testDisplayedIfNotLogged ()
    {
        $this->withExceptionHandling();

        $this
            ->get(route('register'))
            ->assertSuccessful();
    }

    public function testRedirectIfLogged ()
    {
        $this->withExceptionHandling();

        $user = factory(User::class)->make();

        $this
            ->actingAs($user)
            ->get(route('register'))
            ->assertRedirect(route('account'));
    }

    public function testPostMissingFieldsRequired ()
    {
        $this->withExceptionHandling();

        $this
            ->post(route('register'), [])
            ->assertSessionHasErrors('name')
            ->assertSessionHasErrors('email')
            ->assertSessionHasErrors('password')
            ->assertSessionHasErrors('password')
            ->assertSessionHasErrors('terms');

        $this->assertGuest();
    }

    public function testPostWrongEmail ()
    {
        $this->withExceptionHandling();

        $password = $this->faker->password;
        $user = factory(User::class)->create([ 'password' => $password ]);
        // Email has been registered
        $this
            ->post(route('register'), [ 'email' => $user->email ])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
        // Email has not a email format
        $this
            ->post(route('register'), [ 'email' => $this->faker->name ])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function testPostWrongPassword ()
    {
        $this->withExceptionHandling();

        $this
            ->post(route('register'), [ 'password' => $this->faker->text ])
            ->assertSessionHasErrors('password');

        $this->assertGuest();
    }

    public function testPostSuccess ()
    {
        $this->withExceptionHandling();
        Event::fake();

        $avatars = UserHelper::getAvatars();

        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'password' => 'Secret123!',
            'terms' => true
        ];

        $this
            ->post(route('register'), [
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => $userData['password'],
                'avatar' => COUNT($avatars) > 0 ? $avatars[$this->faker->numberBetween(0, COUNT($avatars) - 1)] : null,
                'password_confirmation' => $userData['password'],
                'terms' => $userData['terms']
            ])
            ->assertStatus(302)
            ->assertRedirect(route('account'));

        $users = User::all();
        $user = $users->first();

        $this->assertCount(1, $users);
        $this->assertAuthenticatedAs($user);
        $this->assertEquals($userData['name'], $user->name);
        $this->assertEquals($userData['email'], $user->email);
        $this->assertTrue(!$user->hasRole('admin'));
        $this->assertTrue($user->hasRole('public'));


        Event::assertDispatched(Registered::class, function ($e) use ($user) {
            return $e->user->id === $user->id;
        });
    }
}
