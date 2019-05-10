<?php

namespace Tests;

use App\Models\Core\User;
use App\Models\Laratrust\Role;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    protected function setUp():void
    {
        parent::setUp();
        $this->disableExceptionHandling();
    }
    protected function signIn($user = null) {
        $user = $user ?: factory(User::class)->create();
        $this->actingAs($user);
        return $this;
    }

    protected function disableExceptionHandling()
    {
        $this->oldExceptionHandler = $this->app->make(ExceptionHandler::class);
        $this->app->instance(ExceptionHandler::class, new class extends Handler {
            public function __construct() {}
            public function report(\Exception $e) {}
            public function render($request, \Exception $e) {
                throw $e;
            }
        });
    }
    protected function withExceptionHandling()
    {
        $this->app->instance(ExceptionHandler::class, $this->oldExceptionHandler);
        return $this;
    }

    protected function seedRoles()
    {
        $config = config('laratrust_seeder.role_structure');

        foreach ($config as $key => $modules) {
            // Create a new role
            Role::create([
                'name' => $key,
                'display_name' => ucwords(str_replace('_', ' ', $key)),
                'description' => ucwords(str_replace('_', ' ', $key))
            ]);
        }
    }
}
