<?php

namespace Tests\Feature\Admin;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Helpers\RoleHelper;
use App\Models\Core\App;
use App\Models\Core\AppImage;
use App\Models\Core\AppLocale;
use App\Models\Core\Message;
use App\Models\Core\Page;
use App\Models\Core\PageLocale;
use App\Models\Core\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected $user, $roles;

    protected function setUp ():void
    {
        parent::setUp();

        app()['cache']->forget('spatie.permission.cache');

        Notification::fake();

        $this->roles =  RoleHelper::getRoles();

        foreach ( $this->roles AS $role ) {
            Role::create(['name' => $role]);
        }

        $this->user = factory(User::class)->create()->assignRole('admin');
        factory(User::class, $this->faker->numberBetween(1, 20))->create()->each(function ($user) {
            foreach ( $this->roles AS $role ) {
                if ( $this->faker->boolean ) {
                    $user->assignRole($role);
                }
            }
        });

        factory(Page::class, $this->faker->  numberBetween(1, 20))->create()->each(function ($page) {
            $langs = config('global.langsAvailables');
            $setAnyLang = false;

            foreach ( $langs AS $lang ) {
                if ( $this->faker->boolean ) {
                    $setAnyLang = true;
                    factory(PageLocale::class)->create([
                        'lang' => $lang['iso'],
                        'page_id' => $page->id
                    ]);
                }
            }

            if ( !$setAnyLang ) {
                factory(PageLocale::class)->create([
                    'lang' => $langs[$this->faker->numberBetween(0, COUNT($langs)-1)]['iso'],
                    'page_id' => $page->id
                ]);
            }
        });

        factory(App::class, $this->faker->numberBetween(1, 20))->create()->each(function ($app) {
            $langs = config('global.langsAvailables');

            foreach ( $langs AS $lang ) {
                factory(AppLocale::class)->create([
                    'lang' => $lang['iso'],
                    'app_id' => $app->id
                ]);
            }

            factory(AppImage::class, $this->faker->numberBetween(1, 10))->create([ 'app_id' => $app->id ]);
        });

        factory(Message::class, $this->faker->numberBetween(1, 20))->create();
    }

    public function testViewIndex ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->get(route('admin.dashboard'))
            ->assertSuccessful();
    }

    public function testPostGetStatistics ()
    {
        $this->withExceptionHandling();

        $testController = new DashboardControllerTest();

        $this
            ->actingAs($this->user)
            ->post(route('admin.dashboard.getStatistics'))
            ->assertSuccessful()
            ->assertExactJson($testController->getExpectedResult());
    }
}

class DashboardControllerTest extends DashboardController
{
    public function getExpectedResult () {
        return [
            'messages' => $this->getStatisticsMessages(),
            'users' => $this->getStatisticsUsers(),
            'apps' => $this->getStatisticsApps(),
            'pages' => $this->getStatisticsPages()
        ];
    }
}
