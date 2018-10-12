<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Core\AppLocale::class, function (Faker $faker) {
    $title = $faker->sentence;

    return [
        'app_id' => App\Models\Core\App::all()->random()->id,
        'slug' => str_slug($title, '-'),
        'title' => $title,
        'description' => $faker->paragraph
    ];
});
