<?php

use Illuminate\Database\Seeder;

use App\Models\Core\User;
use App\Models\Core\App;
use App\Models\Core\AppLocale;
use App\Models\Core\AppImage;

class AppsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::role('public')->get();

        $apps = factory(App::class, 5)->create()->each(function ($app) {
            factory(AppLocale::class)->create([
                'lang' => 'en',
                'app_id' => $app->id
            ]);

            factory(AppLocale::class)->create([
                'lang' => 'es',
                'app_id' => $app->id
            ]);

            factory(AppImage::class, 3)->create([
                'app_id' => $app->id
            ]);
        });

        foreach ( $apps AS $app ) {
            foreach ( $users AS $user ) {
                if ( (bool)random_int(0, 1) ) {
                    $app->users()->attach($user->id, [ 'active' => (bool)random_int(0, 1) ]);
                }
            }
        }
        
        //$user->apps()->sync($apps, [ 'active' => true ]);
    }
}
