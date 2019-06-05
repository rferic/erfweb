<?php

namespace Tests\Feature\Front;

use App\Http\Helpers\LocalizationHelper;
use App\Http\Helpers\PageHelper;
use App\Models\Core\Page;
use App\Models\Core\PageLocale;
use App\Models\Core\User;
use Arcanedev\Localization\Facades\Localization;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PageTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected $user, $page, $appPage;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedRoles();
        $this->user = factory(User::class)->create()->attachRole('user')->attachRole('administrator');
        // Static pages
        // Create home page
        factory(Page::class)->create([ 'page_id' => null, 'is_home' => true ])->each(function ($page) {
            $langs = LocalizationHelper::getSupportedFormatted();

            foreach ($langs AS $lang) {
                factory(PageLocale::class)->create([
                    'lang' => $lang['iso'],
                    'page_id' => $page->id,
                    'slug' => '/'
                ]);
            }
        });
        // Create account page
        factory(Page::class)->create([ 'page_id' => null, 'is_home' => false ])->each(function ($page) {
            $langs = LocalizationHelper::getSupportedFormatted();

            foreach ($langs AS $lang) {
                factory(PageLocale::class)->create([
                    'lang' => $lang['iso'],
                    'page_id' => $page->id,
                    'slug' => PageHelper::getSlugTranslate('account', $lang['code'])
                ]);
            }
        });
        // Create who-i-am page
        factory(Page::class)->create([ 'page_id' => null, 'is_home' => false ])->each(function ($page) {
            $langs = LocalizationHelper::getSupportedFormatted();

            foreach ($langs AS $lang) {
                factory(PageLocale::class)->create([
                    'lang' => $lang['iso'],
                    'page_id' => $page->id,
                    'slug' => PageHelper::getSlugTranslate('who-i-am', $lang['code'])
                ]);
            }
        });
        // Create app page
        factory(Page::class)->create([ 'page_id' => null, 'is_home' => false ])->each(function ($page) {
            $langs = LocalizationHelper::getSupportedFormatted();

            foreach ( $langs AS $lang ) {
                factory(PageLocale::class)->create([
                    'lang' => $lang['iso'],
                    'page_id' => $page->id,
                    'slug' => PageHelper::getSlugTranslate('apps', $lang['code'])
                ]);
            }
        });
        // Create html page
        $this->page = factory(Page::class)->create([ 'type' => 'html', 'page_id' => null ]);

        foreach (LocalizationHelper::getSupportedFormatted() AS $lang) {
            factory(PageLocale::class)->create([
                'lang' => $lang['iso'],
                'page_id' => $this->page->id,
                'slug' => $this->faker->word,
                'layout' => 'default'
            ]);
        }
        // Create app page
    }

    public function testViewApps()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->get(Localization::localizeURL(route('apps')))
            ->assertSuccessful()
            ->assertViewIs('front.default');

    }

    public function testViewApp()
    {
        $this->withExceptionHandling();
    }

    public function testViewAccount()
    {
        $this->withExceptionHandling();

        $this
            ->get(route('account'))
            ->assertStatus(302)
            ->assertRedirect(route('login'));

        $this
            ->actingAs($this->user)
            ->get(route('account'))
            ->assertSuccessful()
            ->assertViewIs('front.default');
    }

    public function testViewWhoIAm()
    {
        $this->withExceptionHandling();

        $this
            ->get(Localization::localizeURL(route('who-i-am')))
            ->assertSuccessful()
            ->assertViewIs('front.default');
    }

    public function testViewHome()
    {
        $this->withExceptionHandling();

        $this
            ->get(Localization::localizeURL(route('home')))
            ->assertSuccessful()
            ->assertViewIs('front.default');
    }

    public function testViewIndex()
    {
        $this->withExceptionHandling();

        $this
            ->get(Localization::localizeURL(route('index', $this->faker->word)))
            ->assertStatus(404);

        $locale = PageLocale::where('page_id', $this->page->id)->first();

        $this
            ->get(Localization::localizeURL(route('index', $locale->slug)))
            ->assertSuccessful()
            ->assertViewIs('front.' . $locale->layout);
    }
}
