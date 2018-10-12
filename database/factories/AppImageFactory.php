<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Core\AppImage::class, function (Faker $faker) {
	return [
        'app_id' => App\Models\Core\App::all()->random()->id,
        'src' => $faker->imageUrl(640, 480),
        'title' => $faker->word,
        'priority' => $faker->numberBetween(0, 10)
    ];
});
