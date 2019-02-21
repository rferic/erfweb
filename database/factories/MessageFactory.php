<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Core\Message::class, function (Faker $faker) {
	$status =  App\Http\Helpers\MessageHelper::getStatusList();
	$tags =  App\Http\Helpers\MessageHelper::getTagsList();

    return [
        'message_parent_id' => $faker->boolean && App\Models\Core\Message::count() > 0 ? App\Models\Core\Message::all()->random()->id : null,
        'author_id' => App\Models\Core\User::all()->random()->id,
        'receiver_id' => $faker->boolean ? App\Models\Core\User::all()->random()->id : null,
        'status' => $status[$faker->numberBetween(0, COUNT($status) - 1)]['key'],
        'tag' => $tags[$faker->numberBetween(0, COUNT($tags) - 1)]['key'],
        'subject' => $faker->word,
        'text' => $faker->paragraph
    ];
});
