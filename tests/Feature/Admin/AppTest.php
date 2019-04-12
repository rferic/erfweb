<?php
/**
 * Created by PhpStorm.
 * User: ericrf
 * Date: 11/04/2019
 * Time: 10:50
 */

namespace Tests\Feature\Admin;

use App\Http\Controllers\Admin\AppController;
use App\Http\Helpers\ImageHelper;
use App\Models\Core\App;
use App\Models\Core\AppImage;
use App\Models\Core\AppLocale;
use App\Models\Core\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class AppTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected $user;
    protected $numApps;

    protected function setUp ():void
    {
        parent::setUp();

        app()['cache']->forget('spatie.permission.cache');

        Role::create(['name' => 'admin']);

        $this->numApps = $this->faker->numberBetween(0, 100);
        $this->user = factory(User::class)->create()->assignRole('admin');
        factory(User::class, $this->faker->numberBetween(1, 10))->create();

        factory(App::class, $this->numApps)->create()->each(function ($app) {
            $langs = config('global.langsAvailables');

            foreach ( $langs AS $lang ) {
                factory(AppLocale::class)->create([
                    'lang' => $lang['iso'],
                    'app_id' => $app->id
                ]);
            }

            factory(AppImage::class, $this->faker->numberBetween(1, 10))->create([ 'app_id' => $app->id ]);
        });
    }

    public function testViewIndex()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->get(route('admin.apps'))
            ->assertSuccessful();
    }

    public function testPostGetAppsWithoutParams()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->post(route('admin.apps.get', []))
            ->assertStatus(200)
            ->assertJsonFragment(['total' => $this->numApps]);
    }

    public function testPostGetAppsWithParams()
    {
        $this->withExceptionHandling();

        $params = $this->getParamsToPostGetApp();
        $controller = new AppControllerTest();
        $responseToAssert = $controller->getTesting($params);

        $response = $this
            ->actingAs($this->user)
            ->post(route('admin.apps.get', $params))
            ->assertStatus(200);

        $response = json_decode(json_encode($response))->baseResponse->original;
        $responseToAssert = json_decode(json_encode($responseToAssert))->original;

        $this->assertEquals($response->per_page, $params['perPage']);
        $this->assertEquals(
            intval($response->per_page) > intval($response->total)
                ? intval($response->total)
                : intval($response->per_page),
            COUNT($response->data)
        );
        $this->assertEquals($response->total, $responseToAssert->total);
    }

    public function testPostStoreWrong()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->post(route('admin.apps.store', []))
            ->assertStatus(400);
    }

    public function testPostStoreSuccessful()
    {
        //$this->withExceptionHandling();

        $langs = config('global.langsAvailables');
        $appLocales = [];
        $appImages = [];
        $testController = new AppControllerTest();

        foreach ( $langs AS $index => $lang ) {
            if ( $this->faker->boolean || $index === 0 ) {
                $appLocales[] = [
                    'id' => '',
                    'app_id' => '',
                    'lang' => $lang['iso'],
                    'title' => $this->faker->word,
                    'description' => $this->faker->paragraph
                ];
            }
        }

        for ( $i = 0; $i < $this->faker->numberBetween(1, 10); $i++ ) {
            $appImages[] = [
                'id' => '',
                'title' => $this->faker->word,
                'langs' => json_encode([$this->faker->word]),
                'priority' => $this->faker->numberBetween(0, 10),
                'src' => ImageHelper::upload(UploadedFile::fake()->image($this->faker->word . '.jpeg'))
            ];
        }

        $params = [
            'id' => '',
            'version' => $this->faker->word,
            'vue_component' => $this->faker->word,
            'type' => $this->faker->word,
            'status' => $this->faker->word,
            'locales' => $appLocales,
            'images' => $appImages
        ];
        // Testing create a new app
        $this
            ->actingAs($this->user)
            ->post(route('admin.apps.store', $params))
            ->assertSuccessful();

        $app = App::with(['locales', 'images'])->get()->last();

        $this->assertsPageWithLocalesAndContents($app, [
            'appLocales' => $appLocales,
            'appImages' => $appImages,
            'params' => $params
        ]);
        // Update page
        $appLocales = [];
        $appImages = [];

        foreach ( $langs AS $index => $lang ) {
            if ( $this->faker->boolean || $index === 0 ) {
                $id = '';

                foreach ( $app->locales AS $locale ) {
                    if ( $locale->lang === $lang ) {
                        $id = $locale->id;
                    }
                }

                $appLocales[] = [
                    'id' => $id,
                    'app_id' => '',
                    'lang' => $lang['iso'],
                    'title' => $this->faker->word,
                    'description' => $this->faker->paragraph
                ];
            }
        }

        foreach ( $app->images AS $image ) {
            if ( $this->faker->boolean ) {
                $appImages[] = [
                    'id' => $image->id,
                    'title' => $this->faker->word,
                    'langs' => json_encode([$this->faker->word]),
                    'priority' => $this->faker->numberBetween(0, 10),
                    'src' => ImageHelper::upload(UploadedFile::fake()->image($this->faker->word . '.jpeg'))
                ];
            }
        }

        for ( $i = 0; $i < $this->faker->numberBetween(1, 10); $i++ ) {
            $appImages[] = [
                'id' => '',
                'title' => $this->faker->word,
                'langs' => json_encode([$this->faker->word]),
                'priority' => $this->faker->numberBetween(0, 10),
                'src' => ImageHelper::upload(UploadedFile::fake()->image($this->faker->word . '.jpeg'))
            ];
        }

        $params = [
            'id' => $app->id,
            'version' => $this->faker->word,
            'vue_component' => $this->faker->word,
            'type' => $this->faker->word,
            'status' => $this->faker->word,
            'locales' => $appLocales,
            'images' => $appImages
        ];
        // Testing update a new app
        $this
            ->actingAs($this->user)
            ->post(route('admin.apps.store', $params))
            ->assertSuccessful();

        $app = App::with(['locales', 'images'])->get()->last();

        $this->assertsPageWithLocalesAndContents($app, [
            'appLocales' => $appLocales,
            'appImages' => $appImages,
            'params' => $params
        ]);

        $testController->clearImages($app);
    }

    public function testDeleteDestroy()
    {
        $this->withExceptionHandling();

        $app = App::get()->random();

        $this
            ->actingAs($this->user)
            ->delete(route('admin.apps.destroy', $app->id))
            ->assertStatus(200);

        $this->assertNull(App::find($app->id));
        $this->assertCount(0, AppLocale::where('app_id', $app->if)->get());
        $this->assertCount(0, AppImage::where('app_id', $app->if)->get());
        $this->assertDirectoryNotExists($app->imagePath());
    }

    /**
     * Internal test method
     * @return array
     */
    private function getParamsToPostGetApp ()
    {
        $params = [
            'perPage' => $this->faker->numberBetween(1, 100),
            'filters' => [],
            'orderBy' => [
                'way' => $this->faker->boolean ? 'ASC' : 'DESC',
                'attribute' => 'created_at'
            ]
        ];
        // filter by text
        if ( $this->faker->boolean ) {
            $params['filters']['text'] = $this->faker->word;
        }
        // filter by types
        $params['filters']['types'] = [];

        if ( $this->faker->boolean ) {
            $params['filters']['types'][] = [ 'key' => 'public' ];
        }

        if ( $this->faker->boolean ) {
            $params['filters']['types'][] = [ 'key' => 'protected' ];
        }

        if ( $this->faker->boolean ) {
            $params['filters']['types'][] = [ 'key' => 'private' ];
        }
        // filter by status
        $params['filters']['status'] = [];

        if ( $this->faker->boolean ) {
            $params['filters']['status'][] = [ 'key' => 'success' ];
        }

        if ( $this->faker->boolean ) {
            $params['filters']['status'][] = [ 'key' => 'working' ];
        }

        if ( $this->faker->boolean ) {
            $params['filters']['status'][] = [ 'key' => 'error' ];
        }

        return $params;
    }

    private function assertsPageWithLocalesAndContents ( App $app, $data = [])
    {
        $appLocales = $data['appLocales'];
        $appImages = $data['appImages'];
        $params = $data['params'];

        $this->assertEquals($app->version, $params['version']);
        $this->assertEquals($app->vue_component, $params['vue_component']);
        $this->assertEquals($app->type, $params['type']);
        $this->assertEquals($app->status, $params['status']);
        $this->assertCount(COUNT($appLocales), $app->locales);
        $this->assertCount(COUNT($appImages), $app->images);

        foreach ( $app->locales AS $appLocale ) {
            foreach ( $params['locales'] AS $locale ) {
                if ( $appLocale->lang === $locale['lang'] ) {
                    $this->assertEquals($appLocale->title, $locale['title']);
                    $this->assertEquals($appLocale->description, $locale['description']);
                }
            }
        }

        foreach ( $app->images AS $appImage ) {
            foreach ( $params['images'] AS $image ) {
                if ( $appImage->title === $image['title'] ) {
                    $this->assertEquals($appImage->title, $image['title']);
                    $this->assertEquals($appImage->langs, $image['langs']);
                    $this->assertEquals($appImage->priority, $image['priority']);
                    $this->assertTrue(Storage::disk(ImageHelper::$disk)->has($appImage->src));
                }
            }
        }
    }
}

class AppControllerTest extends AppController
{
    public function getTesting ( $params )
    {
        return Response::json($this->getApps($params['filters'], $params['perPage'], $params['orderBy']));
    }

    public function clearImages ( App $app )
    {
        foreach ( $app->images AS $image ) {
            ImageHelper::destroyImage($app->imagePath(), $image->src);
        }

        if ( COUNT(Storage::disk(ImageHelper::$disk)->files($app->imagePath())) <= 0 ) {
            ImageHelper::destroyDirectory($app->imagePath());
        }
    }
}
