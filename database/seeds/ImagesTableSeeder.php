<?php

use Illuminate\Database\Seeder;

use App\Models\Core\Image;

class ImagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Image::class, 30)->create();
    }
}
