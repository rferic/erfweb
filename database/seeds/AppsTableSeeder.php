<?php

use Illuminate\Database\Seeder;

use App\Models\Core\User;
use App\Models\Core\App;
use App\Models\Core\AppLocale;
use App\Models\Core\AppImage;

class AppsTableSeeder extends Seeder
{
    use \Illuminate\Foundation\Testing\WithFaker;
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $users = User::role('public')->get();

        $apps = factory(App::class, 30)->create()->each(function ($app) {
            $langs = config('global.langsAvailables');

            foreach ( $langs AS $lang ) {
                factory(AppLocale::class)->create([
                    'app_id' => $app->id,
                    'lang' => $lang['iso']
                ]);
            }

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
        
        $user->apps()->sync($apps, [ 'active' => true ]);
    }
}
