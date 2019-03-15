<?php
/**
 * Created by PhpStorm.
 * User: ericrf
 * Date: 27/10/2018
 * Time: 13:50
 */

namespace Tests\Feature\Admin;

use App\Http\Controllers\Admin\PageController;
use App\Models\Core\Menu;
use App\Models\Core\Page;
use App\Models\Core\PageLocale;
use App\Models\Core\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Response;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PageTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected $user;
    protected $numPages;

    protected function setUp ():void
    {
        parent::setUp();

        app()['cache']->forget('spatie.permission.cache');

        Role::create(['name' => 'admin']);

        $this->numPages = $this->faker->numberBetween(0, 10);
        $this->user = factory(User::class)->create()->assignRole('admin');
        factory(User::class, $this->faker->numberBetween(1, 10))->create();
        factory(Page::class, $this->numPages)->create()->each(function ($page) {
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
    }

    public function testViewIndex ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->get(route('admin.pages'))
            ->assertSuccessful();
    }

    public function testPostGetPagesWithoutParams ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->post(route('admin.pages.get', []))
            ->assertStatus(200)
            ->assertJsonFragment(['total' => $this->numPages]);
    }

    public function testPostGetPagesWithParams ()
    {
        $this->withExceptionHandling();

        $params = $this->getParamsToPostGetPage();
        $controller = new PageControllerTest();
        $responseToAssert = $controller->getTesting($params);

        $response = $this
            ->actingAs($this->user)
            ->post(route('admin.pages.get', $params))
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

    public function testPostGetAllSlugsPage ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->post(route('admin.pages.getAllSlugsPage'))
            ->assertSuccessful()
            ->assertExactJson(PageLocale::query()->pluck('slug')->all());
    }

    public function testPostRestorePage ()
    {
        $this->withExceptionHandling();

        $page = factory(Page::class)->create();
        // Call restore to page not in trash
        $this
            ->actingAs($this->user)
            ->post(route('admin.pages.restore', $page->id))
            ->assertSuccessful()
            ->assertExactJson(['result' => false]);

        // Call restore to page in trash
        $page->delete();
        $this
            ->actingAs($this->user)
            ->post(route('admin.pages.restore', $page->id))
            ->assertSuccessful()
            ->assertExactJson(['result' => true]);

        $this->assertFalse(Page::withTrashed()->find($page->id)->trashed());
    }

    public function testDeleteRemovePage ()
    {
        $this->withExceptionHandling();

        // Call restore to page in trash
        $page = factory(Page::class)->create();
        $page->delete();
        $this
            ->actingAs($this->user)
            ->delete(route('admin.pages.remove', $page->id))
            ->assertSuccessful()
            ->assertExactJson(['result' => false]);

        // Call restore to page not in trash
        $page = Page::get()->first();
        $page->restore();

        $this
            ->actingAs($this->user)
            ->delete(route('admin.pages.remove', $page->id))
            ->assertSuccessful()
            ->assertExactJson(['result' => true]);

        $this->assertTrue(Page::withTrashed()->find($page->id)->trashed());
    }

    public function testDeleteDestroyPage ()
    {
        $this->withExceptionHandling();

        $page = factory(Page::class)->create();
        // Call restore to page not in trash
        $this
            ->actingAs($this->user)
            ->delete(route('admin.pages.destroy', $page->id))
            ->assertSuccessful()
            ->assertExactJson(['result' => false]);

        // Call restore to page in trash
        $page->delete();
        $this
            ->actingAs($this->user)
            ->delete(route('admin.pages.destroy', $page->id))
            ->assertSuccessful()
            ->assertExactJson(['result' => true]);

        $this->assertNull(Page::withTrashed()->find($page->id));
    }

    /**
     * Internal test method
     * @param bool $onlyTrashed
     * @return array
     */
    private function getParamsToPostGetPage ()
    {
        $params = [
            'perPage' => $this->faker->numberBetween(1, 100),
            'filters' => [],
            'orderBy' => [
                'way' => $this->faker->boolean ? 'ASC' : 'DESC',
                'attribute' => 'created_at'
            ]
        ];
        $users = User::all();
        // filter by text
        if ( $this->faker->boolean ) {
            $params['filters']['text'] = $this->faker->word;
        }
        // filter by authors
        if ( $this->faker->boolean ) {
            $params['filters']['authors'] = [];

            if ( $this->faker->boolean ) {
                $params['filters']['authors'][] = 'admin';
            }

            foreach ( $users AS $user ) {
                if ( $this->faker->boolean ) {
                    $params['filters']['authors'][] = $user->id;
                }
            }
        }
        // filter by lang
        if ( $this->faker->boolean ) {
            $params['filters']['langs'] = [];
            $langs = config('global.langsAvailables');
            $setAnyLang = false;

            foreach ( $langs AS $lang ) {
                if ( $this->faker->boolean ) {
                    $setAnyLang = true;
                    $params['filters']['langs'][] = $lang['iso'];
                }
            }

            if ( !$setAnyLang ) {
                $params['filters']['langs'][] = $langs[$this->faker->numberBetween(0, COUNT($langs)-1)];
            }
        }

        // filter by menus
        if ( $this->faker->boolean ) {
            $params['filters']['menus'] = [];
            $menus = Menu::all();

            foreach ( $menus as $menu ) {
                if ( $this->faker->boolean ) {
                    $params['filters']['menus'][] = $menu->id;
                }
            }
        }

        // filter by status
        if ( $this->faker->boolean ) {
            $params['filters']['enables'] = $this->faker->boolean;
            $params['filters']['disables'] = $this->faker->boolean;
        }

        return $params;
    }
}

class PageControllerTest extends PageController
{
    public function getTesting ( $params )
    {
        return Response::json($this->getPages($params['filters'], $params['perPage'], $params['orderBy']));
    }
}
