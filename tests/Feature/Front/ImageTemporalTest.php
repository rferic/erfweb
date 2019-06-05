<?php

namespace Tests\Feature\Front;

use App\Http\Helpers\ImageHelper;
use App\Models\Core\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageTemporalTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected $user;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedRoles();
        $this->user = factory(User::class)->create()->attachRole('user');
    }

    public function testPostUploadBadRequest()
    {
        $this->withExceptionHandling();

        $this
            ->post(route('upload-image-temporal'))
            ->assertStatus(302);

        $this
            ->actingAs($this->user)
            ->post(route('upload-image-temporal'))
            ->assertStatus(400);
    }

    public function testPostUploadSuccessful()
    {
        $this->withExceptionHandling();

        $response = $this
            ->actingAs($this->user)
            ->post(route('upload-image-temporal'), [
                'image' => UploadedFile::fake()->image('random.jpg')
            ])
            ->assertSuccessful()
            ->assertSuccessful()
            ->assertJsonFragment([ 'result' => true ]);

        $imageName = str_replace('/storage/', '', $response->original['data']['image']);

        $this->assertTrue(Storage::disk(ImageHelper::$disk)->has($imageName));
        Storage::disk(ImageHelper::$disk)->delete($response->original['data']['image']);
    }

    public function testDeleteBadRequest()
    {
        $this->withExceptionHandling();

        $this
            ->delete(route('delete-image-temporal'))
            ->assertStatus(302);

        $this
            ->actingAs($this->user)
            ->delete(route('delete-image-temporal'))
            ->assertStatus(400);
    }

    public function testDeleteSuccessful()
    {
        $this->withExceptionHandling();

        $imagePath = Storage::disk(ImageHelper::$disk)->putFile(
            ImageHelper::$temporalPath,
            UploadedFile::fake()->image($this->faker->word . '.jpg'),
            ImageHelper::$disk
        );

        $this
            ->actingAs($this->user)
            ->delete(route('delete-image-temporal'), [
                'image' => Storage::url($imagePath)
            ])
            ->assertSuccessful();

        $imageName = str_replace('/storage/', '', $imagePath);
        $this->assertFalse(Storage::disk(ImageHelper::$disk)->has($imageName));
    }
}
