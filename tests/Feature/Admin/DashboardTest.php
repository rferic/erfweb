<?php

namespace Tests\Feature\Admin;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Helpers\LocalizationHelper;
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

        Notification::fake();

        $this->seedRoles();

        $this->roles = RoleHelper::getRoles();
        $this->user = factory(User::class)->create()->attachRole('superadministrator');
        factory(User::class, $this->faker->numberBetween(1, 20))->create()->each(function ($user) {
            foreach ( $this->roles AS $role ) {
                if ( $this->faker->boolean ) {
                    $user->attachRole($role);
                }
            }
        });

        factory(Page::class, $this->faker->  numberBetween(1, 20))->create()->each(function ($page) {
            $langs = LocalizationHelper::getSupportedFormatted();
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
            $langs = LocalizationHelper::getSupportedFormatted();

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
            ->assertJson($testController->getExpectedResult());
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
