<?php

namespace Tests\Feature\Admin;

use App\Http\Helpers\ImageHelper;
use App\Models\Core\Image;
use App\Models\Core\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ImageTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected $user;
    protected $numImages;

    protected function setUp ():void
    {
        parent::setUp();

        app()['cache']->forget('spatie.permission.cache');

        Role::create(['name' => 'admin']);

        $this->numImages = $this->faker->numberBetween(1, 20);
        $this->user = factory(User::class)->create()->assignRole('admin');
        factory(Image::class, $this->numImages)->create();
    }

    public function testViewIndex ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->get(route('admin.images'))
            ->assertSuccessful();
    }

    public function testPostCreateBadRequest ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->post(route('admin.images.create'), [])
            ->assertStatus(400);
    }

    public function testPostCreateSuccessful ()
    {
        $this->withExceptionHandling();

        $params = [
            'title' => $this->faker->word,
            'src' => ImageHelper::upload(UploadedFile::fake()->image($this->faker->word . '.jpeg'))
        ];

        $this
            ->actingAs($this->user)
            ->post(route('admin.images.create'), $params)
            ->assertSuccessful();

        $image = Image::get()->last();

        $this->assertEquals($image->title, $params['title']);
        $this->assertTrue(Storage::disk(ImageHelper::$disk)->has($image->src));
        $this->assertCount($this->numImages + 1, Image::all());

        $this->clearImage($image);
    }

    public function testPostUpdateBadRequest ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->post(route('admin.images.update', Image::get()->random()->id), [])
            ->assertStatus(400);
    }

    public function testPostUpdateSuccessful ()
    {
        $this->withExceptionHandling();

        $imageOrigin = Image::get()->random();
        $params = [
            'title' => $this->faker->word,
            'src' => ImageHelper::upload(UploadedFile::fake()->image($this->faker->word . '.jpeg'))
        ];

        $this
            ->actingAs($this->user)
            ->post(route('admin.images.update', $imageOrigin->id), $params)
            ->assertSuccessful();

        $image = Image::find($imageOrigin->id);

        $this->assertEquals($image->title, $params['title']);
        $this->assertTrue(Storage::disk(ImageHelper::$disk)->has($image->src));
        $this->assertFalse(Storage::disk(ImageHelper::$disk)->has($imageOrigin->src));
        $this->assertCount($this->numImages, Image::all());

        $this->clearImage($image);
    }

    public function testDeleteDelete ()
    {
        $this->withExceptionHandling();

        $image = Image::get()->random();

        $this
            ->actingAs($this->user)
            ->delete(route('admin.images.delete', $image->id))
            ->assertSuccessful();

        $this->assertNull(Image::find($image->id));
        $this->assertFalse(Storage::disk(ImageHelper::$disk)->has($image->src));
        $this->assertCount($this->numImages - 1, Image::all());
    }

    private function clearImage ( $image )
    {
        ImageHelper::destroyImage(Image::$folder, $image->src);
    }
}
