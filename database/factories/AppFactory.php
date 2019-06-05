<?php

use App\Models\Core\App;
use App\Http\Helpers\AppHelper;
use Faker\Generator as Faker;

$factory->define(App::class, function (Faker $faker) {
	$type =  AppHelper::getTypes();
	$status =  AppHelper::getStatus();

	return [
        'version' => $faker->numberBetween(0, 100),
        'vue_component' => $faker->word,
        'type' => $type[$faker->numberBetween(0, COUNT($type) - 1)]['key'],
        'status' => $status[$faker->numberBetween(0, COUNT($status) - 1)]['key']
    ];
});
