<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Core\Menu::class, function (Faker $faker) {
    return [
        'user_id' => App\Models\Core\User::whereRoleIs('superadministrator')->orWhereRoleIs('administrator')->get()->random()->id,
        'name' => $faker->word,
        'description' => $faker->paragraph
    ];
});
