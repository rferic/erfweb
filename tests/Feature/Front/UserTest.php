<?php

namespace Tests\Feature\Front;

use App\Http\Helpers\ImageHelper;
use App\Http\Helpers\LocalizationHelper;
use App\Http\Helpers\UserHelper;
use App\Models\Core\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
        factory(User::class, 10)->create();
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

    public function testPostUpdateBaseBadRequest ()
    {
        $this->withExceptionHandling();

        $langs = LocalizationHelper::getSupportedRegional();

        $this
            ->post(route('update-base-user'))
            ->assertStatus(302);

        $this
            ->actingAs($this->user)
            ->post(route('update-base-user'))
            ->assertStatus(400);

        $params = [
            'name' => $this->faker->word,
            'email' => User::where('email', '!=', $this->user->email)->first()->email,
            'avatar' => $this->user->avatar,
            'lang' => $langs[$this->faker->numberBetween(0, COUNT($langs) - 1)]
        ];

        $this
            ->actingAs($this->user)
            ->post(route('update-base-user'), $params)
            ->assertStatus(400);
    }

    public function testPostUpdateBaseSuccessful ()
    {
        $this->withExceptionHandling();

        $avatars = UserHelper::getAvatars();
        $langs = LocalizationHelper::getSupportedRegional();

        $params = [
            'name' => $this->faker->word,
            'email' => $this->faker->freeEmail,
            'avatar' => $avatars[$this->faker->numberBetween(0, COUNT($avatars) - 1)],
            'lang' => $langs[$this->faker->numberBetween(0, COUNT($langs) - 1)]
        ];

        $this
            ->actingAs($this->user)
            ->post(route('update-base-user'), $params)
            ->assertSuccessful()
            ->assertJsonStructure(['result', 'auth'])
            ->assertJsonFragment([
                'result' => true
            ]);

        $user = User::find($this->user->id);

        $this->assertEquals($user->name, $params['name']);
        $this->assertEquals($user->email, $params['email']);
        $this->assertEquals($user->avatar, $params['avatar']);

        $params['avatar'] = Storage::disk(ImageHelper::$disk)->putFile(
            ImageHelper::$temporalPath,
            UploadedFile::fake()->image('random.jpg'),
            ImageHelper::$disk
        );
        $imagePaths = UserHelper::getImagePaths($this->user, $params['avatar']);

        $this
            ->actingAs($this->user)
            ->post(route('update-base-user'), $params)
            ->assertSuccessful()
            ->assertJsonStructure(['result', 'auth'])
            ->assertJsonFragment([
                'result' => true
            ]);

        $user = User::find($this->user->id);

        $this->assertEquals($user->name, $params['name']);
        $this->assertEquals($user->email, $params['email']);
        $this->assertEquals($user->avatar, $imagePaths['new']);
        $this->assertEquals($user->lang, $params['lang']);

        // Remove test image
        Storage::disk(ImageHelper::$disk)->delete($imagePaths['tmp']);
    }

    public function testPostUpdatePasswordUserBadRequest ()
    {
        $this->withExceptionHandling();

        $password = $this->faker->word;

        $this
            ->post(route('update-password-user'))
            ->assertStatus(302);

        $this
            ->actingAs($this->user)
            ->post(route('update-password-user'))
            ->assertStatus(400);

        $this
            ->actingAs($this->user)
            ->post(route('update-password-user'), [
                'password' => $this->faker->password,
                'password_confirmation' => $this->faker->password
            ])
            ->assertStatus(400);

        $this
            ->actingAs($this->user)
            ->post(route('update-password-user'), [
                'password' => $password,
                'password_confirmation' => $password
            ])
            ->assertStatus(400);
    }

    public function testPostUpdatePasswordUserSuccessful ()
    {
        $this->withExceptionHandling();

        $password = env('ADMIN_PASSWORD_DEFAULT', 'Secret1!');

        $this
            ->actingAs($this->user)
            ->post(route('update-password-user'), [
                'password' => $password,
                'password_confirmation' => $password
            ])
            ->assertSuccessful()
            ->assertJson([ 'result' => true ]);

        $this->assertTrue(Hash::check($password, User::find($this->user->id)->password));
    }
}
