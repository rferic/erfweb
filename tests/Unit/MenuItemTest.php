<?php
/**
 * Created by PhpStorm.
 * User: ericrf
 * Date: 21/10/2018
 * Time: 20:02
 */

namespace Tests\Unit;

use App\Models\Core\Menu;
use App\Models\Core\MenuItem;
use App\Models\Core\Page;
use App\Models\Core\PageLocale;
use App\Models\Core\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MenuItemTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected $menuItem, $menu, $user, $author, $pageLocale;

    protected function setUp (): void
    {
        parent::setUp();

        app()['cache']->forget('spatie.permission.cache');

        Role::create(['name' => 'admin']);

        $this->user = factory(User::class)->create()->assignRole('admin');
        $this->author = factory(User::class)->create()->assignRole('admin');
        $this->menu = factory(Menu::class)->create([ 'user_id' => $this->author->id ]);

        factory(Page::class)->create();
        $this->pageLocale = factory(PageLocale::class)->create([
            'lang' => $this->faker->languageCode
        ]);

        $this->menuItem = factory(MenuItem::class)->create([ 'page_locale_id' => $this->pageLocale->id]);
    }

    public function testHasMenu ()
    {
        $this->assertInstanceOf(Menu::class, $this->menuItem->menu);
        $this->assertEquals($this->menuItem->menu->id, $this->menu->id);
    }

    public function testIsAuthor ()
    {
        $this->assertFalse($this->menu->isAuthor());
        $this->signIn($this->user)->assertFalse($this->menu->isAuthor());
        $this->signIn($this->author)->assertTrue($this->menu->isAuthor());
    }

    public function testHasAuthor ()
    {
        $this->assertInstanceOf(User::class, $this->menu->author);
        $this->assertEquals($this->menu->author->id, $this->author->id);
    }

    public function testHasPageLocale ()
    {
        $this->assertInstanceOf(PageLocale::class, $this->menuItem->pageLocale);
        $this->assertEquals($this->menuItem->pageLocale->id, $this->pageLocale->id);
    }
}
