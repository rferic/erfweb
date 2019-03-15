<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run ()
    {
        $this->call([
            UsersTableSeeder::class,
            CommentsTableSeeder::class,
            MessagesTableSeeder::class,
            PagesTableSeeder::class,
            MenusTableSeeder::class,
            AppsTableSeeder::class,
            RedirectionsTableSeeder::class,
        ]);
    }

}
