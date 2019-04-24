<?php

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
        factory(Page::class, 10)->create([ 'page_id' => null])->each(function ($page) {
            $this->setRelations($page);
        });

        for ( $i = 0; $i < 20; $i++ ) {
            factory(Page::class, 1)->create([ 'page_id' => Page::all()->random()->id])->each(function ($page) {
                $this->setRelations($page);
            });
        }
    }

    private function setRelations ( $page ) {
        $langs = config('global.langsAvailables');
        $setAnyLang = false;

        foreach ( $langs AS $lang ) {
            if ( (bool)random_int(0, 1) ) {
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
