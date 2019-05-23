<?php

use App\Http\Helpers\LocalizationHelper;
use Illuminate\Database\Seeder;

use App\Models\Core\Menu;
use App\Models\Core\MenuItem;

class MenusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $langs = LocalizationHelper::getSupportedFormatted();

        factory(Menu::class, 1)->create([
            'name' => __('Default'),
            'description' => __('Global menu'),
            'is_default' => true
        ]);

        foreach ( $langs AS $lang ) {
            factory(MenuItem::class, 5)->create([ 'lang' => $lang['iso'] ]);
        }
    }
}
