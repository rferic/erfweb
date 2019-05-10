<?php

namespace Tests\Feature;

use App\Models\Core\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmailVerificationTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;

    protected $userNotVerified, $userVerified;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedRoles();

        $this->userNotVerified = factory(User::class)->create([ 'email_verified_at' => null ])->attachRole('user');
        $this->userVerified = factory(User::class)->create([ 'email_verified_at' => now() ])->attachRole('user');
    }

    public function testNotDisplayedNoticeIfNotLogged ()
    {
        $this->withExceptionHandling();

        $this
            ->get(route('verification.notice'))
            ->assertStatus(302)
            ->assertRedirect(route('login'));
    }

    public function testDisplayedNoticeIfNotVerified ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->userNotVerified)
            ->get(route('verification.notice'))
            ->assertSuccessful()
            ->assertViewIs('auth.verify');
    }

    public function testRedirectIfLoggedAndVerified ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->userVerified)
            ->get(route('verification.notice'))
            ->assertStatus(302)
            ->assertRedirect(route('account'));
    }
}
