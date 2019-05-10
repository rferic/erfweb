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
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected $user;
    protected $numMessages;

    protected function setUp ():void
    {
        parent::setUp();

        Notification::fake();

        $this->seedRoles();

        $this->user = factory(User::class)->create()->attachRole('superadministrator');
        $this->numMessages = $this->faker->numberBetween(0, 100);
        factory(User::class, $this->faker->numberBetween(1, 10))->create();
        factory(Message::class, $this->numMessages)->create();
    }

    public function testPostGetStateWithoutFilters ()
    {
        $this->withExceptionHandling();

        $controller = new MessageControllerTest();
        $state = $controller->getStateStatusTagsTesting();

        $this
            ->actingAs($this->user)
            ->post(route('admin.messages.getState'))
            ->assertSuccessful()
            ->assertStatus(200)
            ->assertJsonCount(4)
            ->assertJsonFragment([
                'statusStructure' => MessageHelper::getStatusList()
            ])
            ->assertJsonFragment([
                'tagsStructure' => MessageHelper::getTagsList()
            ])
            ->assertJsonFragment([
                'status' => $state['countersStatus']
            ])
            ->assertJsonFragment([
                'tags' => $state['countersTags']
            ]);
    }

    public function testPostGetStateWithFilters ()
    {
        $this->withExceptionHandling();

        $filters = [ 'authors' => [ $this->user->id ] ];
        $controller = new MessageControllerTest();
        $state = $controller->getStateStatusTagsTesting( $filters );

        $this
            ->actingAs($this->user)
            ->post(route('admin.messages.getState'), [ 'filters' => $filters ])
            ->assertSuccessful()
            ->assertStatus(200)
            ->assertJsonCount(4)
            ->assertJsonFragment([
                'statusStructure' => MessageHelper::getStatusList()
            ])
            ->assertJsonFragment([
                'tagsStructure' => MessageHelper::getTagsList()
            ])
            ->assertJsonFragment([
                'status' => $state['countersStatus']
            ])
            ->assertJsonFragment([
                'tags' => $state['countersTags']
            ]);
    }

    public function testPostGetLastPending ()
    {
        $this->withExceptionHandling();

        Notification::fake();

        $count = $this->faker->numberBetween(1, 10);
        factory(Message::class, $this->faker->numberBetween(1, 10))->create([
            'author_id' => $this->user->id
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
            ->get(route('admin.messages.trash'))
            ->assertSuccessful();
    }

    public function testViewDetail ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->get(route('admin.messages.detail', Message::all()->random()->id))
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

    public function testPostGetAuthorMessage ()
    {
        $this->withExceptionHandling();

        Notification::fake();

        $message = factory(Message::class)->create();
        $author = User::find($message->author->id)->toArray();

        $this
            ->actingAs($this->user)
            ->post(route('admin.messages.getAuthor', $message->id))
            ->assertSuccessful()
            ->assertStatus(200)
            ->assertJsonFragment($author);
    }

    public function testPostRestoreMessage ()
    {
        $this->withExceptionHandling();

        Notification::fake();

        $message = factory(Message::class)->create();
        // Call restore to message not in trash
        $this
            ->actingAs($this->user)
            ->post(route('admin.messages.restore', $message->id))
            ->assertSuccessful()
            ->assertExactJson(['result' => false]);

        // Call restore to message in trash
        $message->delete();
        $this
            ->actingAs($this->user)
            ->post(route('admin.messages.restore', $message->id))
            ->assertSuccessful()
            ->assertExactJson(['result' => true]);

        $this->assertFalse(Message::withTrashed()->find($message->id)->trashed());
    }

    public function testDeleteRemoveMessage ()
    {
        $this->withExceptionHandling();

        Notification::fake();

        // Call restore to message in trash
        $message = factory(Message::class)->create();
        $message->delete();
        $this
            ->actingAs($this->user)
            ->delete(route('admin.messages.remove', $message->id))
            ->assertSuccessful()
            ->assertExactJson(['result' => false]);

        // Call restore to message not in trash
        $message = Message::get()->first();
        $message->restore();
        $this
            ->actingAs($this->user)
            ->delete(route('admin.messages.remove', $message->id))
            ->assertSuccessful()
            ->assertExactJson(['result' => true]);

        $this->assertTrue(Message::withTrashed()->find($message->id)->trashed());
    }

    public function testDeleteDestroyMessage ()
    {
        $this->withExceptionHandling();

        Notification::fake();

        $message = factory(Message::class)->create();
        // Call restore to message not in trash
        $this
            ->actingAs($this->user)
            ->delete(route('admin.messages.destroy', $message->id))
            ->assertSuccessful()
            ->assertExactJson(['result' => false]);

        // Call restore to message in trash
        $message->delete();
        $this
            ->actingAs($this->user)
            ->delete(route('admin.messages.destroy', $message->id))
            ->assertSuccessful()
            ->assertExactJson(['result' => true]);

        $this->assertNull(Message::withTrashed()->find($message->id));
    }

    public function testPostUploadMessageWithWrongParams ()
    {
        $this->withExceptionHandling();

        Notification::fake();

        $message = factory(Message::class)->create();
        $params = [];

        $this
            ->actingAs($this->user)
            ->post(route('admin.messages.update', $message->id), $params)
            ->assertStatus(400);

        $params = [
            'subject' => $this->faker->word,
            'message' => $this->faker->paragraph,
            'status' => $this->faker->word,
            'tag' => $this->faker->word
        ];

        $this
            ->actingAs($this->user)
            ->post(route('admin.messages.update', $message->id), $params)
            ->assertStatus(400);
    }

    public function tesPosttUpdateMessageSuccessful ()
    {
        $this->withExceptionHandling();

        Notification::fake();

        $statusList = MessageHelper::getStatusList();
        $tagsList = MessageHelper::getTagsList();
        $status = $statusList[$this->faker->numberBetween(0, COUNT($statusList) - 1)];
        $tag = $tagsList[$this->faker->numberBetween(0, COUNT($tagsList) - 1)];
        $message = factory(Message::class)->create();
        $params = [
            'subject' => $this->faker->word,
            'text' => $this->faker->paragraph,
            'status' => $status['key'],
            'tag' => $tag['key']
        ];

        $this
            ->actingAs($this->user)
            ->post(route('admin.messages.update', $message->id), $params)
            ->assertSuccessful()
            ->assertExactJson(['result' => true]);

        $message = Message::where('id', $message->id)->first();

        $this->assertEquals($message->subject, $params['subject']);
        $this->assertEquals($message->text, $params['text']);
        $this->assertEquals($message->status, $params['status']);
        $this->assertEquals($message->tag, $params['tag']);
    }

    public function testPostCreateMessageWithWrongParams ()
    {
        $this->withExceptionHandling();

        $params = [];

        $this
            ->actingAs($this->user)
            ->post(route('admin.messages.create'), $params)
            ->assertStatus(400);

        $params = [
            'subject' => $this->faker->word,
            'message' => $this->faker->paragraph,
            'status' => $this->faker->word,
            'tag' => $this->faker->word,
            'message_parent_id' => $this->faker->boolean ? Message::all()->random()->id : null,
            'receiver_id' => $this->faker->boolean ? User::all()->random()->id : null
        ];

        $this
            ->actingAs($this->user)
            ->post(route('admin.messages.create'), $params)
            ->assertStatus(400);
    }

    public function testPostCreateMessageSuccessful ()
    {
        $this->withExceptionHandling();

        $statusList = MessageHelper::getStatusList();
        $tagsList = MessageHelper::getTagsList();
        $status = $statusList[$this->faker->numberBetween(0, COUNT($statusList) - 1)];
        $tag = $tagsList[$this->faker->numberBetween(0, COUNT($tagsList) - 1)];
        $params = [
            'subject' => $this->faker->word,
            'text' => $this->faker->paragraph,
            'status' => $status['key'],
            'tag' => $tag['key'],
            'message_parent_id' => $this->faker->boolean ? Message::all()->random()->id : null,
            'receiver_id' => $this->faker->boolean ? User::all()->random()->id : null
        ];

        $this
            ->actingAs($this->user)
            ->post(route('admin.messages.create'), $params)
            ->assertSuccessful()
            ->assertJsonStructure(['result', 'message'])
            ->assertJsonFragment(['result' => true])
            ->assertJson(['message' => [
                'id' => Message::where('subject', $params['subject'])->get()->last()->id
            ]])
            ->assertJson(['message' => [
                'subject' => $params['subject']
            ]])
            ->assertJson(['message' => [
                'text' => $params['text']
            ]])
            ->assertJson(['message' => [
                'status' => $params['status']
            ]])
            ->assertJson(['message' => [
                'tag' => $params['tag']
            ]])
            ->assertJson(['message' => [
                'author_id' => Auth()->id()
            ]])
            ->assertJson(['message' => [
                'message_parent_id' => $params['message_parent_id']
            ]])
            ->assertJson(['message' => [
                'receiver_id' => $params['receiver_id']
            ]]);
    }

    /**
     * Internal test method
     * @param bool $onlyTrashed
     * @return array
     */
    private function getParamsToPostGetMessage ( $onlyTrashed = false )
    {
        $params = [
            'perPage' => $this->faker->numberBetween(1, 100),
            'filters' => [ 'onlyTrashed' => $onlyTrashed ],
            'orderBy' => [
                'way' => $this->faker->boolean ? 'ASC' : 'DESC',
                'attribute' => 'created_at'
            ]
        ];
        $status = MessageHelper::getStatusList();
        $tags = MessageHelper::getTagsList();
        $users = User::all();
        $statusSelected = [];
        $tagsSelected = [];
        // filter by status
        foreach ( $status as $item ) {
            if ( $this->faker->boolean ) {
                array_push($statusSelected, $item['key']);
            }
        }

        if ( COUNT($statusSelected) > 0 ) {
            $params['filters']['status'] = $statusSelected;
        }
        // filter by tags
        foreach ( $tags as $item ) {
            if ( $this->faker->boolean ) {
                array_push($tagsSelected, $item['key']);
            }
        }

        if ( COUNT($tagsSelected) > 0 ) {
            $params['filters']['tags'] = $tagsSelected;
        }
        // filter by text
        if ( $this->faker->boolean ) {
            $params['filters']['text'] = $this->faker->word;
        }
        // filter by authors
        if ( $this->faker->boolean ) {
            $params['filters']['authors'] = [];

            if ( $this->faker->boolean ) {
                $params['filters']['authors'][] = 'admin';
            }

            foreach ( $users AS $user ) {
                if ( $this->faker->boolean ) {
                    $params['filters']['authors'][] = $user->id;
                }
            }
        }
        // filter by receivers
        if ( $this->faker->boolean ) {
            $params['filters']['receivers'] = [];

            if ( $this->faker->boolean ) {
                $params['filters']['receivers'][] = 'admin';
            }

            foreach ( $users AS $user ) {
                if ( $this->faker->boolean ) {
                    $params['filters']['receivers'][] = $user->id;
                }
            }
        }
        // filter by message_parent
        if ( $this->faker->boolean ) {
            if ( $this->faker->boolean ) {
                $params['filters']['message_parent'] = Message::all()->random()->id;
            } else {
                $params['filters']['message_parent'] = false;
            }
        }

        return $params;
    }
}

class MessageControllerTest extends MessageController
{
    public function getTesting ( $params )
    {
        return Response::json($this->getMessages($params['filters'], $params['perPage'], $params['orderBy']));
    }

    public function getStateStatusTagsTesting ( $filters = [] )
    {
        $statusList = MessageHelper::getStatusList();
        $tagsList = MessageHelper::getTagsList();
        $countersStatus = [];
        $countersTags = [];

        foreach ( $statusList AS $status ) {
            $key = $status['key'];
            $query = Message::where('status', $key);

            if  ( isset($filters['authors']) || isset($filters['receivers']) ) {
                $query->where(function ($query) use ($filters) {
                    if ( isset($filters['authors']) ) {
                        foreach ($filters['authors'] as $item) {
                            $query->orWhere('author_id', '=', $item);
                        }
                    }

                    if ( isset($filters['receivers']) ) {
                        foreach ($filters['receivers'] as $item) {
                            $query->orWhere('receiver_id', '=', $item);
                        }
                    }
                });
            }

            $countersStatus[$key] = strval($query->get()->count());
        }

        foreach ( $tagsList AS $tag ) {
            $key = $tag['key'];
            $query = Message::where('tag', $key);

            if ( isset($filters['authors']) ) {
                $authors = $filters['authors'];

                $query->where(function ($query) use ($authors) {
                    foreach ($authors as $authorID) {
                        $query->orWhere('author_id', $authorID);
                    }
                });
            }

            $countersTags[$key] = strval($query->get()->count());
        }

        ksort($countersStatus);
        ksort($countersTags);
        $countersStatus = (object) $countersStatus;
        $countersTags = (object) $countersTags;

        return ['countersStatus' => $countersStatus, 'countersTags' => $countersTags];
    }
}
