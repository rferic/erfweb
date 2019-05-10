<?php
/**
 * Created by PhpStorm.
 * User: ericrf
 * Date: 27/10/2018
 * Time: 13:50
 */

namespace Tests\Feature\Admin;

use App\Models\Core\Page;
use App\Models\Core\PageLocale;
use App\Models\Core\Content;
use App\Models\Core\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ContentTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected $user;
    protected $pageLocale;
    protected $numContents;

    protected function setUp ():void
    {
        parent::setUp();

        Notification::fake();

        $this->seedRoles();

        $this->numContents = $this->faker->numberBetween(0, 100);
        $this->user = factory(User::class)->create()->attachRole('superadministrator');
        factory(User::class, $this->faker->numberBetween(1, 10))->create();
        $page = factory(Page::class)->create();
        $this->pageLocale = factory(PageLocale::class)->create([
            'page_id' => $page->id,
            'lang' => 'esES'
        ]);
        factory(Content::class, $this->numContents)->create([ 'page_locale_id' => $this->pageLocale->id ]);
    }

    public function testPostGetContentWithoutParams ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->post(route('admin.contents.get'), [])
            ->assertStatus(400);

        $this
            ->actingAs($this->user)
            ->post(route('admin.contents.get'), [ 'page_locale_id' => $this->pageLocale->id + 1 ])
            ->assertStatus(400);
    }

    public function testPostGetContentWithParams ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->post(route('admin.contents.get'), [ 'page_locale_id' => $this->pageLocale->id ])
            ->assertSuccessful()
            ->assertJsonCount($this->numContents)
            ->assertExactJson(Content::where('page_locale_id', $this->pageLocale->id)->get()->toArray());
    }

    public function testPostCreateContentWithoutParams ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->post(route('admin.contents.create'), [])
            ->assertStatus(400);

        $this
            ->actingAs($this->user)
            ->post(route('admin.contents.create'), [ 'page_locale_id' => $this->pageLocale->id + 1 ])
            ->assertStatus(400);
    }

    public function testPostCreateContentWithParams ()
    {

        $this->withExceptionHandling();

        $params = $this->getParamsToContent();

        $this
            ->actingAs($this->user)
            ->post(route('admin.contents.create'), $params)
            ->assertSuccessful()
            ->assertJsonStructure([ 'result', 'content' ])
            ->assertJsonFragment(['result' => true])
            ->assertJson(['content' => [
                'id' => Content::get()->last()->id,
                'page_locale_id' => $params['page_locale_id'],
                'key' => $params['key'],
                'id_html' => $params['id_html'],
                'class_html' => $params['class_html'],
                'text' => $params['text'],
                'header_inject' => $params['header_inject'],
                'footer_inject' => $params['footer_inject'],
                'priority' => $params['priority']
            ]]);
    }

    public function testPostUpdateContentWithoutParams ()
    {
        $this->withExceptionHandling();

        $content = Content::get()->random();

        $this
            ->actingAs($this->user)
            ->post(route('admin.contents.update', $content->id), [])
            ->assertStatus(400);
    }

    public function testPostUpdateContentWithParams ()
    {
        $this->withExceptionHandling();

        $content = Content::get()->random();
        $params = $params = $this->getParamsToContent();

        $this
            ->actingAs($this->user)
            ->post(route('admin.contents.update', $content->id), $params)
            ->assertSuccessful()
            ->assertExactJson(['result' => true]);
    }

    public function testPostRestoreContent ()
    {
        $this->withExceptionHandling();

        $content = Content::get()->random();

        $this
            ->actingAs($this->user)
            ->post(route('admin.contents.restore', $content->id), [])
            ->assertSuccessful()
            ->assertExactJson(['result' => false]);

        $this->assertFalse(Content::find($content->id)->trashed());

        $content->delete();

        $this
            ->actingAs($this->user)
            ->post(route('admin.contents.restore', $content->id), [])
            ->assertSuccessful()
            ->assertExactJson(['result' => true]);

        $this->assertFalse(Content::find($content->id)->trashed());
    }

    public function testDeleteRemoveContent ()
    {
        $this->withExceptionHandling();

        $content = Content::get()->random();

        $this
            ->actingAs($this->user)
            ->delete(route('admin.contents.remove', $content->id), [])
            ->assertSuccessful()
            ->assertExactJson(['result' => true]);

        $this->assertTrue(Content::withTrashed()->find($content->id)->trashed());

        $content->delete();

        $this
            ->actingAs($this->user)
            ->delete(route('admin.contents.remove', $content->id), [])
            ->assertSuccessful()
            ->assertExactJson(['result' => false]);

        $this->assertTrue(Content::withTrashed()->find($content->id)->trashed());
    }

    public function testDeleteDestroyContent ()
    {
        $this->withExceptionHandling();

        $content = Content::get()->random();
        // Call restore to content not in trash
        $this
            ->actingAs($this->user)
            ->delete(route('admin.contents.destroy', $content->id))
            ->assertSuccessful()
            ->assertExactJson(['result' => false]);

        // Call restore to content in trash
        $content->delete();
        $this
            ->actingAs($this->user)
            ->delete(route('admin.contents.destroy', $content->id))
            ->assertSuccessful()
            ->assertExactJson(['result' => true]);

        $this->assertNull(Content::withTrashed()->find($content->id));
    }

    private function getParamsToContent ()
    {
        return [
            'page_locale_id' => $this->pageLocale->id,
            'key' => $this->faker->word,
            'id_html' => $this->faker->word,
            'class_html' => $this->faker->word,
            'text' => $this->faker->word,
            'header_inject' => $this->faker->word,
            'footer_inject' => 'console.log("' . $this->faker->word . '")',
            'priority' => $this->faker->numberBetween(0, 100)
        ];
    }
}
