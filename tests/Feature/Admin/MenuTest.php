<?php

namespace Tests\Feature\Admin;

use App\Http\Helpers\LocalizationHelper;
use App\Models\Core\Menu;
use App\Models\Core\MenuItem;
use App\Models\Core\Page;
use App\Models\Core\PageLocale;
use App\Models\Core\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class MenuTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected $user;
    protected $numMenus;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedRoles();

        $this->user = factory(User::class)->create()->attachRole('superadministrator');
        $this->numMenus = $this->faker->numberBetween(1, 10);

        factory(Page::class, $this->faker->numberBetween(1, 10))->create()->each(function ($page) {
            factory(PageLocale::class, $this->faker->numberBetween(1, 10))->create([ 'page_id' => $page->id, 'lang' => $this->faker->word ]);
        });

        factory(Menu::class, $this->numMenus)->create()->each(function ($menu) {
            factory(MenuItem::class, $this->faker->numberBetween(1, 10))->create([ 'menu_id' => $menu->id ]);
        });
    }

    public function testViewIndex ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->get(route('admin.menus'))
            ->assertSuccessful();
    }

    public function testPostGet ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->post(route('admin.menus.get'))
            ->assertSuccessful()
            ->assertExactJson(Menu::with('items')->get()->toArray());
    }

    public function testPostStoreBadRequest ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->post(route('admin.menus.store'), [])
            ->assertStatus(400);
    }

    public function testPostStoreSuccessful ()
    {
        $this->withExceptionHandling();

        $langs = LocalizationHelper::getSupportedFormatted();
        // Testing create a new menu
        $numItems = $this->faker->numberBetween(1, 10);
        $items = [];

        for ( $i = 0; $i < $numItems; $i++ ) {
            $isExternal = $this->faker->boolean;
            $items[] = [
                'id' => '',
                'menu_id' => '',
                'lang' => $langs[$this->faker->numberBetween(0, COUNT($langs) - 1)]['iso'],
                'label' => $this->faker->unique()->word,
                'type' => $isExternal ? 'internal' : 'external',
                'page_locale_id' => $isExternal ? '' : PageLocale::get()->random()->id,
                'url_external' => $isExternal ? $this->faker->url : '',
                'priority' => $this->faker->numberBetween(0, 10)
            ];
        }

        $params = [
            'id' => '',
            'name' => $this->faker->word,
            'description' => $this->faker->paragraph,
            'is_default' => $this->faker->boolean,
            'items' => $items
        ];

        $this
            ->actingAs($this->user)
            ->post(route('admin.menus.store'), $params)
            ->assertSuccessful()
            ->assertJsonStructure(['result', 'menu'])
            ->assertJsonFragment([ 'result' => true ]);

        $this->assertCount($this->numMenus + 1, Menu::all());

        $menu = Menu::with(['items'])->get()->last();
        $this->assertsMenuWithItems($menu, $params);
        // Testing update a new menu
        $numItems = $this->faker->numberBetween(1, 10);
        $items = [];

        foreach ( $menu->items() AS $item ) {
            if ( $this->faker->boolean ) {
                $items[] = [
                    'id' => $item->id,
                    'menu_id' => $item->menu_id,
                    'lang' => $langs[$this->faker->numberBetween(0, COUNT($langs) - 1)]['iso'],
                    'label' => $this->faker->unique()->word,
                    'type' => $isExternal ? 'internal' : 'external',
                    'page_locale_id' => $isExternal ? '' : PageLocale::get()->random()->id,
                    'url_external' => $isExternal ? $this->faker->url : '',
                    'priority' => $this->faker->numberBetween(0, 10)
                ];
            }
        }

        for ( $i = 0; $i < $numItems; $i++ ) {
            $isExternal = $this->faker->boolean;
            $items[] = [
                'id' => '',
                'menu_id' => '',
                'lang' => $langs[$this->faker->numberBetween(0, COUNT($langs) - 1)]['iso'],
                'label' => $this->faker->word,
                'type' => $isExternal ? 'internal' : 'external',
                'page_locale_id' => $isExternal ? '' : PageLocale::get()->random()->id,
                'url_external' => $isExternal ? $this->faker->url : '',
                'priority' => $this->faker->numberBetween(0, 10)
            ];
        }

        $params = [
            'id' => $menu->id,
            'name' => $this->faker->word,
            'description' => $this->faker->paragraph,
            'is_default' => $this->faker->boolean,
            'items' => $items
        ];

        $this
            ->actingAs($this->user)
            ->post(route('admin.menus.store'), $params)
            ->assertSuccessful()
            ->assertJsonStructure(['result', 'menu'])
            ->assertJsonFragment([ 'result' => true ]);

        $this->assertCount($this->numMenus + 1, Menu::all());

        $menu = Menu::find($menu->id);
        $this->assertsMenuWithItems($menu, $params);
    }

    public function testDeleteDestroyMenu ()
    {
        $this->withExceptionHandling();

        $menu = Menu::all()->random();
        // Call restore to menu not in trash
        $this
            ->actingAs($this->user)
            ->delete(route('admin.menus.destroy', $menu->id))
            ->assertSuccessful()
            ->assertExactJson(['result' => true]);

        $this->assertCount($this->numMenus - 1, Menu::all());
    }

    private function assertsMenuWithItems ( Menu $menu, $params )
    {
        $this->assertEquals($params['name'], $menu->name);
        $this->assertEquals($params['description'], $menu->description);
        $this->assertCount(COUNT($params['items']), $menu->items);

        foreach ( $menu->items AS $item ) {
            foreach ( $params['items'] AS $paramsItem ) {
                if (
                    $paramsItem['id'] === $item->id ||
                    (
                        $paramsItem['id'] === '' &&
                        $paramsItem['label'] === $item->label
                    )
                ) {
                    $this->assertEquals($paramsItem['label'], $item->label);
                    $this->assertEquals($paramsItem['lang'], $item->lang);
                    $this->assertEquals($paramsItem['type'], $item->type);
                    $this->assertEquals($paramsItem['priority'], $item->priority);

                    if ( $paramsItem['page_locale_id'] === '' ) {
                        $this->assertNull($item->page_locale_id);
                        $this->assertEquals($paramsItem['url_external'], $item->url_external);
                    } else {
                        $this->assertEquals($paramsItem['page_locale_id'], $item->page_locale_id);
                        $this->assertEquals($paramsItem['url_external'], $item->url_external);
                    }
                }
            }
        }
    }
}
