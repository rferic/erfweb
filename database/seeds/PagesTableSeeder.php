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
        factory(Page::class, 8)->create()->each(function ($page) {
            $random = (bool)random_int(0, 1);
            
            if ($random) {
                $pageLocale = factory(PageLocale::class)->create([
                    'lang' => 'en',
                    'page_id' => $page->id
                ]);
                
                factory(Content::class, 3)->create([
                    'page_locale_id' => $pageLocale->id
                ]);
            }
            
            if (!$random || (bool)random_int(0, 1)) {
                $pageLocale = factory(PageLocale::class)->create([
                    'lang' => 'es',
                    'page_id' => $page->id
                ]);
                
                factory(Content::class, 3)->create([
                    'page_locale_id' => $pageLocale->id
                ]);
            }
        });
    }
}
