<?php
/**
 * Created by PhpStorm.
 * User: ericrf
 * Date: 27/10/2018
 * Time: 13:50
 */

namespace Tests\Feature\Admin;

use App\Models\Core\Message;
use App\Models\Core\User;
use App\Http\Helpers\MessageHelper;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected $user;

    protected function setUp ()
    {
        parent::setUp();

        app()['cache']->forget('spatie.permission.cache');

        Role::create(['name' => 'admin']);

        $this->user = factory(User::class)->create()->assignRole('admin');
    }

    public function testPostGetState ()
    {
        $this->withExceptionHandling();

        factory(Message::class, $this->faker->numberBetween(1, 10))->create([
            'user_id' => $this->user->id
        ]);

        $this
            ->actingAs($this->user)
            ->post(route('admin.messages.getState'))
            ->assertSuccessful()
            ->assertStatus(200)
            ->assertJsonCount(4)
            ->assertJsonStructure([
                'statusStructure' => [],
                'tagsStructure' => [],
                'status' => [],
                'tags' => []
            ]);
    }

    public function testPostGetLastPending ()
    {
        $this->withExceptionHandling();

        $count = $this->faker->numberBetween(1, 10);
        factory(Message::class, $this->faker->numberBetween(1, 10))->create([
            'user_id' => $this->user->id
        ]);

        $this
            ->actingAs($this->user)
            ->post(route('admin.messages.getLastPending'), [ 'count' => $count ])
            ->assertSuccessful()
            ->assertStatus(200)
            ->assertJsonStructure([])
            ->assertExactJson(Message::where('status', 'pending')->orderBy('created_at', 'desc')->take($count)->get()->toArray());
    }
}
