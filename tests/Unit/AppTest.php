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
use App\Models\Core\AppLocale;
use App\Models\Core\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class AppTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected $appTest;

    protected function setUp (): void
    {
        parent::setUp();

        $this->appTest = factory(App::class)->create();
    }

    public function testHasLocales ()
    {
        $count = $this->faker->numberBetween(2, 100);

        factory(AppLocale::class, $count)->create([
            'lang' => $this->faker->languageCode,
            'app_id' => $this->appTest->id
        ]);

        $this->assertCount($count, $this->appTest->locales);
        $this->assertInstanceOf(Collection::class, $this->appTest->locales);
        $this->assertInstanceOf(AppLocale::class, $this->appTest->locales->first());
    }

    public function testHasImages ()
    {
        $count = $this->faker->numberBetween(2, 100);

        factory(AppImage::class, $count)->create([
            'app_id' => $this->appTest->id
        ]);

        $this->assertCount($count, $this->appTest->images);
        $this->assertInstanceOf(Collection::class, $this->appTest->images);
        $this->assertInstanceOf(AppImage::class, $this->appTest->images->first());
    }

    public function testHasUsers ()
    {
        $count = $this->faker->numberBetween(2, 100);

        $users = factory(User::class, $count)->create();
        $this->appTest->users()->sync($users);

        $this->assertCount($count, $this->appTest->users);
        $this->assertInstanceOf(Collection::class, $this->appTest->users);
        $this->assertInstanceOf(User::class, $this->appTest->users->first());
    }

    public function testHasPathImage ()
    {
        $this->assertIsString($this->appTest->imagePath);
    }

    public function testDestroy ()
    {
        factory(AppLocale::class, $this->faker->numberBetween(1, 10))->create([
            'lang' => $this->faker->languageCode,
            'app_id' => $this->appTest->id
        ]);

        factory(AppImage::class, $this->faker->numberBetween(1, 10))->create([
            'app_id' => $this->appTest->id
        ]);

        $this->appTest->forceDelete();
        $this->assertCount(0, AppLocale::where('app_id', 'id')->get());
        $this->assertCount(0, AppImage::where('app_id', 'id')->get());
    }
}
