<?php
/**
 * Created by PhpStorm.
 * User: ericrf
 * Date: 21/10/2018
 * Time: 02:16
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

class PageTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected $page, $user, $author, $pages, $count;

    protected function setUp (): void
    {
        parent::setUp();

        app()['cache']->forget('spatie.permission.cache');

        Role::create(['name' => 'admin']);

        $this->count = $this->faker->numberBetween(1, 10);

        $this->user = factory(User::class)->create()->assignRole('admin');
        $this->author = factory(User::class)->create()->assignRole('admin');
        $this->page = factory(Page::class)->create([ 'user_id' => $this->author->id]);
        factory(Menu::class)->create([ 'user_id' => $this->author->id]);
        factory(PageLocale::class, $this->count)->create([
            'lang' => $this->faker->languageCode,
            'page_id' => $this->page->id
        ])->each(function ($pageLocale) {
            factory(Content::class, $this->count)->create([ 'page_locale_id' => $pageLocale->id ]);
            factory(MenuItem::class, $this->count)->create([
                'page_locale_id' => $pageLocale->id,
                'lang' => $pageLocale->lang
            ]);
        });
    }

    public function testIsAuthor ()
    {
        $this->assertFalse($this->page->isAuthor());
        $this->signIn($this->user)->assertFalse($this->page->isAuthor());
        $this->signIn($this->author)->assertTrue($this->page->isAuthor());
    }

    public function testHasLocales ()
    {
        $this->assertInstanceOf(Collection::class, $this->page->locales);
        $this->assertInstanceOf(PageLocale::class, $this->page->locales->first());
        $this->assertCount($this->count, $this->page->locales);
    }

    public function testHasAuthor ()
    {
        $this->assertFalse($this->page->isAuthor());
        $this->signIn($this->user)->assertFalse($this->page->isAuthor());
        $this->signIn($this->author)->assertTrue($this->page->isAuthor());
    }

    public function testHasContents ()
    {
        $this->assertInstanceOf(Collection::class, $this->page->contents);
        $this->assertInstanceOf(Content::class, $this->page->contents->first());
    }

    public function testMenuItems ()
    {
        $this->assertInstanceOf(Collection::class, $this->page->menuItems);
        $this->assertInstanceOf(MenuItem::class, $this->page->menuItems->first());
    }

    public function testDestroy ()
    {
        factory(PageLocale::class, $this->faker->numberBetween(1, 10))->create([
            'lang' => $this->faker->languageCode,
            'page_id' => $this->page->id
        ]);

        $this->page->forceDelete();
        $this->assertCount(0, PageLocale::where('app_id', 'id')->get());
    }
}
