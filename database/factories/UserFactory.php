<?php

use App\Http\Helpers\UserHelper;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/


$factory->define(App\Models\Core\User::class, function (Faker $faker) {
    static $password;

    $avatars = UserHelper::getAvatars();

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'avatar' => COUNT($avatars) > 0 ? $avatars[$faker->numberBetween(0, COUNT($avatars) - 1)] : null,
        'remember_token' => str_random(10),
        'email_verified_at' => $faker->dateTime('now', null)
    ];
});
