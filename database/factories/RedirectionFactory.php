<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Core\Redirection::class, function (Faker $faker) {
    return [
        'code' => 301,
        'slug_origin' => $faker->slug,
        'slug_destine' => $faker->slug
    ];
});
