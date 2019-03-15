<?php

use App\Models\Core\Redirection;
use Illuminate\Database\Seeder;

class RedirectionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Redirection::class, 30)->create();
    }
}
