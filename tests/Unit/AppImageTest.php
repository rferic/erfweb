<?php
/**
 * Created by PhpStorm.
 * User: ericrf
 * Date: 21/10/2018
 * Time: 01:44
 */

namespace Tests\Unit;

use App\Models\Core\App;
use App\Models\Core\AppImage;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppImageTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;

    protected $appTest, $appImage;

    protected function setUp (): void
    {
        parent::setUp();

        $this->appTest = factory(App::class)->create();
        $this->appImage = factory(AppImage::class)->create([ 'app_id' => $this->appTest->id ]);
    }

    public function testHasApp ()
    {
        $this->assertInstanceOf(App::class, $this->appImage->app);
        $this->assertEquals($this->appImage->app->id, $this->appTest->id);
    }
}
