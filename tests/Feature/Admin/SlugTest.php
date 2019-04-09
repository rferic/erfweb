<?php

namespace Tests\Feature\Admin;

use App\Models\Core\Page;
use App\Models\Core\PageLocale;
use App\Models\Core\Redirection;
use App\Models\Core\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;

class SlugTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected $user;
    protected $langs;

    protected function setUp (): void
    {
        parent::setUp();

        app()['cache']->forget('spatie.permission.cache');

        Role::create(['name' => 'admin']);

        $this->langs = config('global.langsAvailables');
        $this->user = factory(User::class)->create()->assignRole('admin');

        factory(Page::class, $this->faker->numberBetween(1, 10))->create()->each(function ($page) {
            foreach ( $this->langs AS $lang ) {
                factory(PageLocale::class)->create([
                    'lang' => $lang['iso'],
                    'page_id' => $page->id
                ]);
            }
        });

        factory(Redirection::class, $this->faker->numberBetween(1, 10))->create();
    }

    public function testPostGetIsFreeWrongParams ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->post(route('admin.slugs.getIsFree'), [])
            ->assertStatus(400);
    }

    public function testPostGetIsFreeWithSlugFree ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->post(route('admin.slugs.getIsFree'), [
                'type' => 'pageLocale',
                'slug' => $this->faker->slug,
                'lang' => $this->langs[$this->faker->numberBetween(0, COUNT($this->langs) - 1)]['iso'],
                'currentParentId' => $this->faker->numberBetween(0, 100)
            ])
            ->assertSuccessful()
            ->assertExactJson([
                'result' => true,
                'isFree' => true,
                'isUsed' => false,
                'isMine' => false,
                'hasRedirection' => false
            ]);
    }

    public function testPostGetIsFreeWithSlugFreeExistsAndIsMine ()
    {
        $this->withExceptionHandling();

        $pageLocale = PageLocale::all()->random();

        $this
            ->actingAs($this->user)
            ->post(route('admin.slugs.getIsFree'), [
                'type' => 'pageLocale',
                'slug' => $pageLocale->slug,
                'lang' => $pageLocale->lang,
                'currentParentId' => $pageLocale->id
            ])
            ->assertSuccessful()
            ->assertJsonFragment([ 'result' => true ])
            ->assertJsonFragment([ 'isFree' => true ])
            ->assertJsonFragment([ 'isUsed' => true ])
            ->assertJsonFragment([ 'isMine' => true ]);
    }

    public function testPostGetIsFreeWithSlugIsUsed ()
    {
        $this->withExceptionHandling();

        $pageLocale = PageLocale::all()->random();
        $currentPageLocale = $this->faker->numberBetween(0, 100);

        if ( $pageLocale->id === $currentPageLocale ) {
            $currentPageLocale++;
        }

        $this
            ->actingAs($this->user)
            ->post(route('admin.slugs.getIsFree'), [
                'type' => 'pageLocale',
                'slug' => $pageLocale->slug,
                'lang' => $pageLocale->lang,
                'currentParentId' => $currentPageLocale
            ])
            ->assertSuccessful()
            ->assertJsonFragment([ 'result' => true ])
            ->assertJsonFragment([ 'isFree' => false ])
            ->assertJsonFragment([ 'isUsed' => true ])
            ->assertJsonFragment([ 'isMine' => false ]);
    }

    public function testPostGetIsFreeWithSlugHasRedirection ()
    {
        $this->withExceptionHandling();

        $redirection = Redirection::all()->random();

        $this
            ->actingAs($this->user)
            ->post(route('admin.slugs.getIsFree'), [
                'type' => 'pageLocale',
                'slug' => $redirection->slug_origin,
                'lang' => $this->langs[$this->faker->numberBetween(0, COUNT($this->langs) - 1)]['iso']
            ])
            ->assertSuccessful()
            ->assertJsonFragment([ 'result' => true ])
            ->assertJsonFragment([ 'isFree' => false ])
            ->assertJsonFragment([ 'hasRedirection' => true ]);
    }
}
