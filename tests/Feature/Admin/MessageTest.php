<?php
/**
 * Created by PhpStorm.
 * User: ericrf
 * Date: 27/10/2018
 * Time: 13:50
 */

namespace Tests\Feature\Admin;

use App\Http\Controllers\Admin\MessageController;
use App\Http\Helpers\MessageHelper;
use App\Models\Core\Message;
use App\Models\Core\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Response;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected $user;
    protected $numMessages;

    protected function setUp ()
    {
        parent::setUp();

        app()['cache']->forget('spatie.permission.cache');

        Role::create(['name' => 'admin']);

        $this->numMessages = $this->faker->numberBetween(0, 100);
        $this->user = factory(User::class)->create()->assignRole('admin');
        factory(Message::class, $this->numMessages)->create();
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

    public function testViewIndex ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->get(route('admin.messages'))
            ->assertSuccessful();
    }

    public function testViewIndexTrash ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->get(route('admin.messages'))
            ->assertSuccessful();
    }

    public function testPostGetMessagesWithoutParams ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->post(route('admin.messages.get', []))
            ->assertStatus(200)
            ->assertJsonFragment(['total' => $this->numMessages]);
    }

    public function testPostGetMessagesWithParams ()
    {
        $this->withExceptionHandling();

        $params = $this->getParamsToPostGetMessage();
        $controller = new MessageControllerTest();
        $responseToAssert = $controller->getTesting($params);

        $response = $this
            ->actingAs($this->user)
            ->post(route('admin.messages.get', $params))
            ->assertStatus(200);


        $response = json_decode(json_encode($response))->baseResponse->original;
        $responseToAssert = json_decode(json_encode($responseToAssert))->original;

        $this->assertEquals($response->per_page, $params['perPage']);
        $this->assertEquals(
            intval($response->per_page) > intval($response->total)
            ? intval($response->total)
            : intval($response->per_page),
            COUNT($response->data)
        );
        $this->assertEquals($response->total, $responseToAssert->total);
    }

    public function testPostGetMessagesTrashedWithParams ()
    {
        $this->withExceptionHandling();

        $params = $this->getParamsToPostGetMessage(true);
        $controller = new MessageControllerTest();
        $messages = Message::get();

        foreach ( $messages as $message ) {
            if ( $this->faker->boolean ) {
                $message->delete();
            }
        }
        $responseToAssert = $controller->getTesting($params);

        $response = $this
            ->actingAs($this->user)
            ->post(route('admin.messages.get', $params))
            ->assertStatus(200);
        $response = json_decode(json_encode($response))->baseResponse->original;
        $responseToAssert = json_decode(json_encode($responseToAssert))->original;

        $this->assertEquals($response->per_page, $params['perPage']);
        $this->assertEquals(
            intval($response->per_page) > intval($response->total)
                ? intval($response->total)
                : intval($response->per_page),
            COUNT($response->data)
        );
        $this->assertEquals($response->total, $responseToAssert->total);
    }

    private function getParamsToPostGetMessage ( $onlyTrashed = false )
    {
        $params = [ 'perPage' => $this->faker->numberBetween(1, 100), 'filters' => [ 'onlyTrashed' => $onlyTrashed ] ];
        $status = MessageHelper::getStatusList();
        $tags = MessageHelper::getTagsList();
        $statusSelected = [];
        $tagsSelected = [];

        foreach ( $status as $item ) {
            if ( $this->faker->boolean ) {
                array_push($statusSelected, $item['key']);
            }
        }

        foreach ( $tags as $item ) {
            if ( $this->faker->boolean ) {
                array_push($tagsSelected, $item['key']);
            }
        }

        if ( COUNT($statusSelected) > 0 ) {
            $params['filters']['status'] = $statusSelected;
        }

        if ( COUNT($tagsSelected) > 0 ) {
            $params['filters']['tags'] = $tagsSelected;
        }

        if ( $this->faker->boolean ) {
            $params['filters']['text'] = $this->faker->word;
        }

        return $params;
    }
}

class MessageControllerTest extends MessageController
{
    public function getTesting ( $params )
    {
        return Response::json($this->getMessages($params['filters'], $params['perPage']));
    }
}
