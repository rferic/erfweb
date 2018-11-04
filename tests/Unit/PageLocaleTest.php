<?php
/**
 * Created by PhpStorm.
 * User: ericrf
 * Date: 21/10/2018
 * Time: 02:51
 */

namespace Tests\Unit;

use App\Models\Core\Content;
use App\Models\Core\Menu;
use App\Models\Core\MenuItem;
use App\Models\Core\Page;
use App\Models\Core\PageLocale;
use App\Models\Core\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PageLocaleTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected $pageLocale, $page, $user, $author;

    protected function setUp ()
    {
        parent::setUp();

        app()['cache']->forget('spatie.permission.cache');

        Role::create(['name' => 'admin']);

        $this->user = factory(User::class)->create()->assignRole('admin');
        $this->author = factory(User::class)->create()->assignRole('admin');
        $this->page = factory(Page::class)->create([ 'user_id' => $this->author->id ]);
        $this->pageLocale = factory(PageLocale::class)->create([
            'user_id' => $this->author->id,
            'page_id' => $this->page->id,
            'lang' => $this->faker->languageCode
        ]);

        factory(Content::class, $this->faker->numberBetween(0, 10))->create([ 'page_locale_id' => $this->pageLocale->id ]);
        factory(Menu::class)->create([ 'user_id' => $this->author->id]);
        factory(MenuItem::class, $this->faker->numberBetween(0, 10))->create([
            'page_locale_id' => $this->pageLocale->id,
            'lang' => $this->pageLocale->lang
        ]);
    }

    public function testIsAuthor ()
    {
        $this->assertFalse($this->pageLocale->isAuthor());
        $this->signIn($this->user)->assertFalse($this->pageLocale->isAuthor());
        $this->signIn($this->author)->assertTrue($this->pageLocale->isAuthor());
    }

    public function testHasMenuItems ()
    {
        $this->assertInstanceOf(Collection::class, $this->pageLocale->menuItems);
        $this->assertInstanceOf(MenuItem::class, $this->pageLocale->menuItems->first());
    }

    public function testHasContents ()
    {
        $this->assertInstanceOf(Collection::class, $this->pageLocale->contents);
        $this->assertInstanceOf(Content::class, $this->pageLocale->contents->first());
    }

    public function testHasPage ()
    {
        $this->assertInstanceOf(Page::class, $this->pageLocale->page);
        $this->assertEquals($this->pageLocale->page->id, $this->page->id);
    }

    public function testHasAuthor ()
    {
        $this->assertFalse($this->pageLocale->isAuthor());
        $this->signIn($this->user)->assertFalse($this->pageLocale->isAuthor());
        $this->signIn($this->author)->assertTrue($this->pageLocale->isAuthor());
    }
}
