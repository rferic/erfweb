<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(App\Models\Core\AppLocale::class, function (Faker $faker) {
    $title = $faker->sentence;

    return [
        'app_id' => App\Models\Core\App::all()->random()->id,
        'slug' => Str::slug($title, '-'),
        'title' => $title,
        'description' => $faker->paragraph
    ];
});
