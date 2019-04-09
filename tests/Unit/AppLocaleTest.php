<?php
/**
 * Created by PhpStorm.
 * User: ericrf
 * Date: 21/10/2018
 * Time: 01:50
 */

namespace Tests\Unit;

use App\Models\Core\App;
use App\Models\Core\AppLocale;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AppLocaleTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected $appTest, $appLocale;

    protected function setUp (): void
    {
        parent::setUp();

        $this->appTest = factory(App::class)->create();
        $this->appLocale = factory(AppLocale::class)->create([
            'app_id' => $this->appTest->id,
            'lang' => $this->faker->languageCode
        ]);
    }

    public function testHasApp ()
    {
        $this->assertInstanceOf(App::class, $this->appLocale->app);
        $this->assertEquals($this->appLocale->app->id, $this->appTest->id);
    }
}
