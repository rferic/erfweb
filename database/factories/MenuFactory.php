<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Core\Menu::class, function (Faker $faker) {
    return [
        'user_id' => App\Models\Core\User::role('admin')->get()->random()->id,
        'name' => $faker->word,
        'description' => $faker->paragraph
    ];
});
