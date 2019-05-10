<?php
/**
 * Created by PhpStorm.
 * User: ericrf
 * Date: 29/01/2019
 * Time: 08:28
 */

namespace Tests\Feature\Admin;

use App\Http\Controllers\Admin\ImageTemporalController;
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

    public $user;

    protected function setUp ():void
    {
        parent::setUp();

        $this->seedRoles();

        $this->user = factory(User::class)->create()->attachRole('superadministrator');
    }

    public function testPostUploadImageIfNotLogged ()
    {
        $this->withExceptionHandling();

        $this
            ->post(route('admin.imagesTemporal.upload'))
            ->assertStatus(302);
    }

    public function testPostUploadImageWrongParamsRequest ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->post(route('admin.imagesTemporal.upload'), [])
            ->assertStatus(400);
    }

    public function testPostUploadImage ()
    {
        $this->withExceptionHandling();

        $response = $this
            ->actingAs($this->user)
            ->post(route('admin.imagesTemporal.upload'), [
                'image' => UploadedFile::fake()->image('random.jpg')
            ])
            ->assertStatus(200);

        $imageName = str_replace('/storage/', '', $response->original['data']['image']);

        $this->assertTrue($response->original['result']);
        $this->assertTrue(Storage::disk(ImageTemporalController::$disk)->has($imageName));
    }

    public function testDeleteRemoveImageIfNotLogged ()
    {
        $this->withExceptionHandling();

        $this
            ->delete(route('admin.imagesTemporal.remove'))
            ->assertStatus(302);
    }

    public function testDeleteRemoveImageWrongParamsRequest ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->delete(route('admin.imagesTemporal.remove'), [])
            ->assertStatus(400);
    }

    public function testDeleteRemoveImageNotExists ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->delete(route('admin.imagesTemporal.remove'), [
                'image' => $this->faker->image()
            ])
            ->assertStatus(404);
    }

    public function testDeleteRemoveImage ()
    {
        $this->withExceptionHandling();

        $imagePath = Storage::disk(ImageTemporalController::$disk)->putFile(
            ImageTemporalController::$temporalPath,
            UploadedFile::fake()->image($this->faker->word . '.jpg'),
            ImageTemporalController::$disk
        );

        $this
            ->actingAs($this->user)
            ->delete(route('admin.imagesTemporal.remove'), [
                'image' => Storage::url($imagePath)
            ])
            ->assertStatus(200);
    }
}
