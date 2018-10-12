<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Core\Message::class, function (Faker $faker) {
	$status =  App\Http\Helpers\MessageHelper::getStatusList();
	$tags =  App\Http\Helpers\MessageHelper::getTagsList();

    return [
        'user_id' => App\Models\Core\User::all()->random()->id,
        'status' => $status[$faker->numberBetween(0, COUNT($status) - 1)]['key'],
        'tag' => $tags[$faker->numberBetween(0, COUNT($tags) - 1)]['key'],
        'subject' => $faker->word,
        'text' => $faker->paragraph
    ];
});
