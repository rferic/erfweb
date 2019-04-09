<?php

namespace Tests\Feature\Admin;

use App\Models\Core\Menu;
use App\Models\Core\MenuItem;
use App\Models\Core\Page;
use App\Models\Core\PageLocale;
use App\Models\Core\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Spatie\Permission\Models\Role;

class MenuTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected $user;
    protected $menus;

    protected function setUp (): void
    {
        parent::setUp();

        app()['cache']->forget('spatie.permission.cache');

        Role::create(['name' => 'admin']);

        $this->user = factory(User::class)->create()->assignRole('admin');

        factory(Page::class, $this->faker->numberBetween(1, 10))->create()->each(function ($page) {
            factory(PageLocale::class, $this->faker->numberBetween(1, 10))->create([ 'page_id' => $page->id, 'lang' => $this->faker->word ]);
        });

        $menus = factory(Menu::class, $this->faker->numberBetween(1, 10))->create()->each(function ($menu) {
            factory(MenuItem::class, $this->faker->numberBetween(1, 10))->create([ 'menu_id' => $menu->id ]);
        });
    }

    public function testGet()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->post(route('admin.menus.get'), [])
            ->assertSuccessful()
            ->assertExactJson(Menu::with('items')->get()->toArray());
    }
}
