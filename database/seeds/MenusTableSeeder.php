<?php

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
        factory(Menu::class, 2)->create();
        factory(MenuItem::class, 10)->create();
    }
}
