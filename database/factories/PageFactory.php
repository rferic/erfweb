<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Core\Page::class, function (Faker $faker) {
    return [
        'user_id' => App\Models\Core\User::role('admin')->get()->random()->id
    ];
});
