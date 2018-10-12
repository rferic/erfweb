<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Core\MenuItem::class, function (Faker $faker) {
    $random = (bool)random_int(0, 1);
    $pageLocale = App\Models\Core\PageLocale::all()->random();
    
    return [
        'user_id' => App\Models\Core\User::role('admin')->get()->random()->id,
        'menu_id' => App\Models\Core\Menu::all()->random()->id,
        'label' => $faker->word,
        'priority' => $faker->numberBetween(0, 10),
        'lang' => $pageLocale->lang,
        'type' => $random ? 'internal' : 'external',
        'page_locale_id' => $random ? $pageLocale->id : null,
        'url_external' => $random ? null : 'http://www.url.com'
    ];
});
