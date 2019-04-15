<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Core\Image::class, function (Faker $faker) {
    return [
        'src' => $faker->imageUrl(640, 480),
        'title' => $faker->word
    ];
});
