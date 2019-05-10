<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run ()
    {
        $this->call([
            LaratrustSeeder::class,
            UsersTableSeeder::class,
            CommentsTableSeeder::class,
            MessagesTableSeeder::class,
            PagesTableSeeder::class,
            MenusTableSeeder::class,
            AppsTableSeeder::class,
            RedirectionsTableSeeder::class,
            ImagesTableSeeder::class,
        ]);
    }

}
