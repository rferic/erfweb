<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Core\Page::class, function (Faker $faker) {
    $types = \App\Http\Helpers\PageHelper::getTypes();

    return [
        'page_id' => null,
        'type' => $types[$faker->numberBetween(0, COUNT($types) - 1)],
        'user_id' => App\Models\Core\User::whereRoleIs('superadministrator')->orWhereRoleIs('administrator')->get()->random()->id
    ];
});
