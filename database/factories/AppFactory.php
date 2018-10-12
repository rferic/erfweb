<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Core\App::class, function (Faker $faker) {
	$type =  App\Http\Helpers\AppHelper::getTypes();
	$status =  App\Http\Helpers\AppHelper::getStatus();

	return [
        'version' => $faker->word,
        'vue_component' => $faker->word,
        'type' => $type[$faker->numberBetween(0, COUNT($type) - 1)]['key'],
        'status' => $status[$faker->numberBetween(0, COUNT($status) - 1)]['key']
    ];
});
