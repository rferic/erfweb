<?php

use App\Http\Helpers\LocalizationHelper;
use App\Models\Core\PageLocale;
use Illuminate\Database\Seeder;

use App\Models\Core\User;
use App\Models\Core\Page;
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
        $langs = LocalizationHelper::getSupportedFormatted();

        factory(Page::class, 10)->create([ 'type' => 'apps', 'page_id' => null ])->each(function($page) use ($langs) {
            foreach ( $langs AS $lang ) {
                factory(PageLocale::class)->create([
                    'lang' => $lang['iso'],
                    'page_id' => $page->id
                ]);
            }
            factory(App::class)->create([ 'page_id' => $page->id ])->each(function ($app) use ($langs) {
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
        });

        $apps = App::all();
        $users = User::whereRoleIs('superadministrator')->orWhereRoleIs('user')->get();

        foreach ( $apps AS $app ) {
            foreach ( $users AS $user ) {
                if ( $user->hasRole('superadministrator') || (bool)random_int(0, 1) ) {
                    $app->users()->attach($user->id, [ 'active' => (bool)random_int(0, 1) ]);
                }
            }
        }
    }
}
