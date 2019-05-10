<?php
/**
 * Created by PhpStorm.
 * User: ericrf
 * Date: 21/10/2018
 * Time: 02:06
 */

namespace Tests\Unit;

use App\Models\Core\Content;
use App\Models\Core\Page;
use App\Models\Core\PageLocale;
use App\Models\Core\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContentTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected $content, $user, $page, $pageLocale;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedRoles();

        $this->user = factory(User::class)->create()->attachRole('superadministrator');
        $this->page = factory(Page::class)->create();
        $this->pageLocale = factory(PageLocale::class)->create([
            'lang' => $this->faker->languageCode
        ]);
        $this->content = factory(Content::class)->create();
    }

    public function testHasPageLocale ()
    {
        $this->assertInstanceOf(PageLocale::class, $this->content->pageLocale);
        $this->assertEquals($this->content->pageLocale->id, $this->pageLocale->id);
    }

    public function testHasPage ()
    {
        $this->assertInstanceOf(Page::class, $this->content->page);
        $this->assertEquals($this->content->page->id, $this->page->id);
    }
}
