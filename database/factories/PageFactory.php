<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Core\Page::class, function (Faker $faker) {
    return [
        'page_id' => null,
        'user_id' => App\Models\Core\User::role('admin')->get()->random()->id
    ];
});
