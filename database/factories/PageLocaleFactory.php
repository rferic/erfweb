<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(App\Models\Core\PageLocale::class, function (Faker $faker) {
    $title = $faker->sentence;

    return [
        'page_id' => App\Models\Core\Page::all()->random()->id,
        'user_id' => App\Models\Core\User::whereRoleIs('superadministrator')->orWhereRoleIs('administrator')->get()->random()->id,
        'slug' => Str::slug($title, '-'),
        'title' => $title,
        'description' => $faker->paragraph,
        'layout' => 'default',
        'options' => json_encode([
            'inject' => [
                'css' => $faker->word,
                'js' => 'console.log(' . $faker->word . ')'
            ]
        ]),
        'seo_title' => $faker->sentence,
        'seo_description' => $faker->paragraph,
        'seo_keywords' => json_encode([
            $faker->word,
            $faker->word,
            $faker->word
        ])
    ];
});
