<?php

namespace Tests\Feature\Front;

use App\Http\Helpers\MessageHelper;
use App\Models\Core\Message;
use App\Models\Core\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MessageTest extends TestCase
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

    public function testPostStoreNotLogged ()
    {
        $this->withExceptionHandling();

        $this
            ->post(route('send-message'))
            ->assertStatus(302);
    }

    public function testPostStoreBadRequest ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->post(route('send-message'))
            ->assertSuccessful()
            ->assertJsonStructure([ 'result', 'errors' ])
            ->assertJsonFragment([ 'result' => false ]);
    }

    public function testPostStoreSuccessful ()
    {
        $this->withExceptionHandling();

        $tags = MessageHelper::getTagsList();
        $status = MessageHelper::getStatusList();
        $params = [
            'subject' => $this->faker->word,
            'text' => $this->faker->paragraph,
            'status' => $status[$this->faker->numberBetween(0, COUNT($status)-1)]['key'],
            'tag' => $tags[$this->faker->numberBetween(0, COUNT($tags)-1)]['key']
        ];

        $this
            ->actingAs($this->user)
            ->post(route('send-message'), $params)
            ->assertSuccessful()
            ->assertJsonStructure([ 'result', 'message' ])
            ->assertJsonFragment([ 'result' => true ]);

        $message = Message::latest()->first();

        $this->assertEquals($params['subject'], $message->subject);
        $this->assertEquals($params['text'], $message->text);
        $this->assertEquals($params['status'], $message->status);
        $this->assertEquals($params['tag'], $message->tag);
    }
}
