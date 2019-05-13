<?php

namespace Tests\Feature\Admin;

use App\Http\Helpers\LocalizationHelper;
use App\Models\Core\Page;
use App\Models\Core\PageLocale;
use App\Models\Core\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PageLocaleTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected $user;

    protected function setUp ():void
    {
        parent::setUp();

        $this->seedRoles();

        $this->user = factory(User::class)->create()->attachRole('superadministrator');
        factory(User::class, $this->faker->numberBetween(1, 10))->create();
        factory(Page::class, $this->faker->numberBetween(1, 10))->create()->each(function ($page) {
            $langs = LocalizationHelper::getSupportedFormatted();
            $setAnyLang = false;

            foreach ( $langs AS $lang ) {
                if ( $this->faker->boolean ) {
                    $setAnyLang = true;
                    factory(PageLocale::class)->create([
                        'lang' => $lang['iso'],
                        'page_id' => $page->id
                    ]);
                }
            }

            if ( !$setAnyLang ) {
                factory(PageLocale::class)->create([
                    'lang' => $langs[$this->faker->numberBetween(0, COUNT($langs)-1)]['iso'],
                    'page_id' => $page->id
                ]);
            }
        });
    }

    public function testPostGet()
    {
        $langs = LocalizationHelper::getSupportedFormatted();

        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->post(route('admin.pageLocales.get'), [])
            ->assertSuccessful()
            ->assertExactJson(PageLocale::all()->toArray());

        $filters = [];
        $langsFilter = [];

        foreach ( $langs AS $lang ) {
            if ( $this->faker->boolean ) {
                $langsFilter[] = $lang['iso'];
            }
        }

        if ( COUNT($langsFilter) > 0 ) {
            $filters['langs'] = $langsFilter;
        }

        if ( $this->faker->boolean ) {
            $filters['text'] = PageLocale::all()->random()->slug;
        }

        $this
            ->actingAs($this->user)
            ->post(route('admin.pageLocales.get'), [ 'filters' => $filters])
            ->assertSuccessful()
            ->assertExactJson($this->getPageLocalesTest($filters)->toArray());
    }

    private function getPageLocalesTest ( $filters )
    {
        $query = PageLocale::query();

        if ( isset($filters['langs']) ) {
            $langs = $filters['langs'];

            $query->where(function ($query) use ($langs) {
                foreach ($langs as $lang) {
                    $query->orWhere('lang', $lang);
                }

                return $query;
            });
        }

        if ( isset($filters['text']) ) {
            $text = $filters['text'];
            $query->where(function ($query) use ($text) {
                return $query
                    ->where('slug', 'LIKE', '%' . $text. '%')
                    ->orWhere('title', 'LIKE', '%' . $text. '%');
            });
        }

        return $query->get();
    }
}
