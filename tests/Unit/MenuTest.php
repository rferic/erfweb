<?php
/**
 * Created by PhpStorm.
 * User: ericrf
 * Date: 21/10/2018
 * Time: 10:13
 */

namespace Tests\Unit;

use App\Models\Core\Menu;
use App\Models\Core\MenuItem;
use App\Models\Core\Page;
use App\Models\Core\PageLocale;
use App\Models\Core\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MenuTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected $menu,$user, $author;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedRoles();

        $this->user = factory(User::class)->create()->attachRole('superadministrator');
        $this->author = factory(User::class)->create()->attachRole('superadministrator');
        $this->menu = factory(Menu::class)->create([ 'user_id' => $this->author->id ]);
    }

    public function testIsAuthor ()
    {
        $this->assertFalse($this->menu->isAuthor());
        $this->signIn($this->user)->assertFalse($this->menu->isAuthor());
        $this->signIn($this->author)->assertTrue($this->menu->isAuthor());
    }

    public function testHasItems ()
    {
        $count = $this->faker->numberBetween(1, 10);

        factory(Page::class)->create();
        factory(PageLocale::class)->create([
            'lang' => $this->faker->languageCode
        ]);

        factory(MenuItem::class, $count)->create([
            'menu_id' => $this->menu->id
        ]);

        $this->assertCount($count, $this->menu->items);
        $this->assertInstanceOf(Collection::class, $this->menu->items);
        $this->assertInstanceOf(MenuItem::class, $this->menu->items->first());
    }

    public function testHasAuthor ()
    {
        $this->assertInstanceOf(User::class, $this->menu->author);
        $this->assertEquals($this->menu->author->id, $this->author->id);
    }
}
