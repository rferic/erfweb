<?php

use App\Http\Helpers\LocalizationHelper;
use App\Http\Helpers\PageHelper;
use Illuminate\Database\Seeder;

use App\Models\Core\PageLocale;
use App\Models\Core\Page;
use App\Models\Core\Content;

class PagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Set home
        factory(Page::class, 1)->create([ 'page_id' => null, 'is_home' => true ])->each(function ($page) {
            $langs = LocalizationHelper::getSupportedFormatted();

            foreach ( $langs AS $lang ) {
                $pageLocale = factory(PageLocale::class)->create([
                    'lang' => $lang['iso'],
                    'page_id' => $page->id,
                    'slug' => '/',
                    'title' => 'ERFWeb',
                    'description' => __('ERFWeb site'),
                    'layout' => 'default',
                    'options' => '{}',
                    'seo_title' => __('Home'),
                    'seo_description' => __('ERFWeb site'),
                    'seo_keywords' => '[]'
                ]);

                factory(Content::class, 1)->create([
                    'page_locale_id' => $pageLocale->id,
                    'key' => 'home-layout',
                    'id_html' => 'home-layout',
                    'class_html' => 'flex',
                    'text' => '<home-layout />',
                    'header_inject' => '',
                    'footer_inject' => '',
                    'priority' => 0
                ]);
            }
        });
        // Set account page
        factory(Page::class, 1)->create([ 'page_id' => null, 'is_home' => false ])->each(function ($page) {
            $langs = LocalizationHelper::getSupportedFormatted();

            foreach ( $langs AS $lang ) {
                App::setLocale($lang['code']);
                $pageLocale = factory(PageLocale::class)->create([
                    'lang' => $lang['iso'],
                    'page_id' => $page->id,
                    'slug' => PageHelper::getSlugTranslate('account', $lang),
                    'title' => __('Account'),
                    'description' => __('Account user'),
                    'layout' => 'default',
                    'options' => '{}',
                    'seo_title' => __('Account'),
                    'seo_description' => __('Account user'),
                    'seo_keywords' => '[]'
                ]);

                factory(Content::class, 3)->create([
                    'page_locale_id' => $pageLocale->id
                ]);
            }
        });
        // Set Who I Am
        factory(Page::class, 1)->create([ 'page_id' => null, 'is_home' => false ])->each(function ($page) {
            $langs = LocalizationHelper::getSupportedFormatted();

            foreach ( $langs AS $lang ) {
                $pageLocale = factory(PageLocale::class)->create([
                    'lang' => $lang['iso'],
                    'page_id' => $page->id,
                    'slug' => PageHelper::getSlugTranslate('who-i-am', $lang),
                    'title' => __('Who I am'),
                    'description' => __('Who I am'),
                    'layout' => 'default',
                    'options' => '{}',
                    'seo_title' => __('Who I am'),
                    'seo_description' => __('Who I am'),
                    'seo_keywords' => '[]'
                ]);

                factory(Content::class, 1)->create([
                    'page_locale_id' => $pageLocale->id,
                    'key' => 'who-i-am-layout',
                    'id_html' => 'who-i-am-layout',
                    'class_html' => 'flex',
                    'text' => '<who-i-am-layout />',
                    'header_inject' => '',
                    'footer_inject' => '',
                    'priority' => 0
                ]);
            }
        });
        // Set resources page
        factory(Page::class, 1)->create([ 'page_id' => null, 'is_home' => false ])->each(function ($page) {
            $langs = LocalizationHelper::getSupportedFormatted();

            foreach ( $langs AS $lang ) {
                App::setLocale($lang['code']);
                $pageLocale = factory(PageLocale::class)->create([
                    'lang' => $lang['iso'],
                    'page_id' => $page->id,
                    'slug' => PageHelper::getSlugTranslate('technologies', $lang),
                    'title' => __('Technologies'),
                    'description' => __('Technologies'),
                    'layout' => 'default',
                    'options' => '{}',
                    'seo_title' => __('Technologies'),
                    'seo_description' => __('Technologies'),
                    'seo_keywords' => '[]'
                ]);

                factory(Content::class, 3)->create([
                    'page_locale_id' => $pageLocale->id
                ]);
            }
        });

        factory(Page::class, 10)->create([ 'page_id' => null])->each(function ($page) {
            $this->setRelations($page);
        });

        for ( $i = 0; $i < 20; $i++ ) {
            factory(Page::class, 1)->create([ 'page_id' => Page::all()->random()->id])->each(function ($page) {
                $this->setRelations($page);
            });
        }
    }

    private function setRelations ( $page, $isHome = false ) {
        $langs = LocalizationHelper::getSupportedFormatted();
        $setAnyLang = false;

        foreach ( $langs AS $lang ) {
            if ( (bool)random_int(0, 1) || $isHome ) {
                $setAnyLang = true;
                $pageLocale = factory(PageLocale::class)->create([
                    'lang' => $lang['iso'],
                    'page_id' => $page->id
                ]);
                factory(Content::class, 3)->create([
                    'page_locale_id' => $pageLocale->id
                ]);
            }
        }

        if ( !$setAnyLang ) {
            $pageLocale = factory(PageLocale::class)->create([
                'lang' => $langs[random_int(0, COUNT($langs)-1)]['iso'],
                'page_id' => $page->id
            ]);
            factory(Content::class, 3)->create([
                'page_locale_id' => $pageLocale->id
            ]);
        }
    }
}
