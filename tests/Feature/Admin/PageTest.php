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

        $this->numPages = $this->faker->numberBetween(1, 10);
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
        $page = Page::onlyTrashed()->get()->first();
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

    public function testPostStorePageWrongParams ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->post(route('admin.pages.store', []))
            ->assertStatus(500);
    }

    public function testPostStorePageSuccessful ()
    {
        $this->withExceptionHandling();

        $langs = config('global.langsAvailables');
        $pageLocales = [];
        $allContents = [];

        foreach ( $langs AS $lang ) {
            $contents = [];

            for ( $i = 0; $i < $this->faker->numberBetween(1, 10); $i++ ) {
                $contents[] = [
                    'id' => '',
                    'page_locale_id' => '',
                    'key' => $this->faker->word,
                    'id_html' => $this->faker->word,
                    'class_html' => $this->faker->word,
                    'text' => $this->faker->randomHtml(),
                    'header_inject' => $this->faker->word,
                    'footer_inject' => $this->faker->word,
                    'priority' => $this->faker->numberBetween(1, 10),
                    'deleted_at' => $this->faker->boolean ? '' : $this->faker->date()
                ];
            }

            $allContents = array_merge($allContents, $contents);
            $pageLocales[] = [
                'id' => '',
                'page_id' => '',
                'lang' => $lang['iso'],
                'slug' => $this->faker->slug,
                'title' => $this->faker->word,
                'description' => $this->faker->paragraph,
                'layout' => $this->faker->word,
                'options' => $this->faker->word,
                'seo_title' => $this->faker->word,
                'seo_description' => $this->faker->word,
                'seo_keywords' => $this->faker->word,
                'deleted_at' => '',
                'contents' => $contents
            ];
        }

        $params = [
            'id' => null,
            'locales' => $pageLocales
        ];
        // Testing create a new page
        $this
            ->actingAs($this->user)
            ->post(route('admin.pages.store', $params))
            ->assertSuccessful()
            ->assertExactJson(['result' => true]);

        $page = Page::with(['locales', 'contents'])->get()->last();

        $this->assertsPageWithLocalesAndContents($page, [
            'pageLocales' => $pageLocales,
            'allContents' => $allContents,
            'params' => $params
        ]);
        // Update page
        $params = [
            'id' => $page->id,
            'locales' => []
        ];

        foreach ( $page->locales AS $locale ) {
            $pageLocale = $locale->toArray();
            $pageLocale['contents'] = $locale->contents->toArray();
            // Overwrite
            $pageLocale['title'] = $this->faker->word;
            $pageLocale['description'] = $this->faker->paragraph;
            $pageLocale['layout'] = $this->faker->word;
            $pageLocale['options'] = $this->faker->word;
            $pageLocale['seo_title'] = $this->faker->word;
            $pageLocale['seo_description'] = $this->faker->word;
            $pageLocale['seo_keywords'] = $this->faker->word;
            $pageLocale['deleted_at'] = '';

            foreach ( $pageLocale['contents'] AS $i => $content ) {
                $pageLocale['contents'][$i]['key'] = $this->faker->word;
                $pageLocale['contents'][$i]['id_html'] = $this->faker->word;
                $pageLocale['contents'][$i]['class_html'] = $this->faker->word;
                $pageLocale['contents'][$i]['text'] = $this->faker->randomHtml();
                $pageLocale['contents'][$i]['header_inject'] = $this->faker->word;
                $pageLocale['contents'][$i]['footer_inject'] = $this->faker->word;
                $pageLocale['contents'][$i]['priority'] = $this->faker->numberBetween(1, 10);
                $pageLocale['contents'][$i]['deleted_at'] = $this->faker->boolean ? '' : $this->faker->date();
            }

            $params['locales'][] = $pageLocale;
        }
        // Testing update a existed page
        $this
            ->actingAs($this->user)
            ->post(route('admin.pages.store', $params))
            ->assertSuccessful()
            ->assertExactJson(['result' => true]);

        $this->assertsPageWithLocalesAndContents($page, [
            'pageLocales' => $pageLocales,
            'allContents' => $allContents,
            'params' => $params
        ]);
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

    private function assertsPageWithLocalesAndContents ( Page $page, $data = [])
    {
        $pageLocales = $data['pageLocales'];
        $allContents = $data['allContents'];
        $params = $data['params'];

        $this->assertCount(COUNT($pageLocales), $page->locales);
        $this->assertCount(COUNT($allContents), $page->contents);

        foreach ( $page->locales AS $item ) {
            $pageLocale = PageLocale::where('id', $item->id)->with('contents')->withTrashed()->first();

            foreach ( $params['locales'] AS $locale ) {
                if ( $pageLocale->lang === $locale['lang'] ) {
                    $this->assertEquals($pageLocale->title, $locale['title']);
                    $this->assertEquals($pageLocale->slug, $locale['slug']);
                    $this->assertEquals($pageLocale->description, $locale['description']);
                    $this->assertEquals($pageLocale->layout, $locale['layout']);
                    $this->assertEquals($pageLocale->options, $locale['options']);
                    $this->assertEquals($pageLocale->seo_title, $locale['seo_title']);
                    $this->assertEquals($pageLocale->seo_description, $locale['seo_description']);
                    $this->assertEquals($pageLocale->seo_keywords, $locale['seo_keywords']);
                    $this->assertEquals(is_null($pageLocale->deleted_at), $locale['deleted_at'] === '');
                    $this->assertCount(COUNT($locale['contents']), $pageLocale->contents);

                    foreach ( $pageLocale->contents AS $content ) {
                        foreach ( $locale['contents'] AS $contentData ) {
                            if ( $content->key === $contentData['key'] && $content->priority === $contentData['priority'] ) {
                                $this->assertEquals($content->id_html, $contentData['id_html']);
                                $this->assertEquals($content->class_html, $contentData['class_html']);
                                $this->assertEquals($content->text, $contentData['text']);
                                $this->assertEquals($content->header_inject, $contentData['header_inject']);
                                $this->assertEquals($content->footer_inject, $contentData['footer_inject']);
                                $this->assertEquals($content->priority, $contentData['priority']);
                                $this->assertEquals(is_null($content->deleted_at), $contentData['deleted_at'] === '');
                            }
                        }
                    }
                }
            }
        }
    }
}

class PageControllerTest extends PageController
{
    public function getTesting ( $params )
    {
        return Response::json($this->getPages($params['filters'], $params['perPage'], $params['orderBy']));
    }
}
