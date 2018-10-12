<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Core\Comment::class, function (Faker $faker) {
    return [
        'user_id' => App\Models\Core\User::all()->random()->id,
        'status' => $faker->numberBetween(0, 3),
        'origin' => $faker->word,
        'subject' => $faker->word,
        'text' => $faker->paragraph
    ];
});
