<?php
/**
 * Created by PhpStorm.
 * User: ericrf
 * Date: 21/10/2018
 * Time: 01:44
 */

namespace Tests\Unit;

use App\Models\Core\Message;
use App\Models\Core\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected $message, $messageChild, $user, $author;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->seedRoles();

        $this->user = factory(User::class)->create()->attachRole('user');
        $this->author = factory(User::class)->create()->attachRole('superadministrator');
        $this->message = factory(Message::class)->create([
            'author_id' => $this->author->id,
            'receiver_id' => $this->user->id,
            'message_parent_id' => NULL
        ]);
        $this->messageChild = factory(Message::class)->create([ 'message_parent_id' => $this->message->id ]);
    }

    public function testHasAuthor()
    {
        $this->assertInstanceOf(User::class, $this->message->author);
        $this->assertEquals($this->message->author->id, $this->author->id);
        $this->assertInstanceOf(Collection::class, $this->message->author->roles);
    }

    public function testHasReceiver()
    {
        $this->assertInstanceOf(User::class, $this->message->receiver);
        $this->assertEquals($this->message->receiver->id, $this->user->id);
        $this->assertInstanceOf(Collection::class, $this->message->receiver->roles);
    }

    public function testHasNotReceiver()
    {
        Notification::fake();

        $messageWithoutReceiver = factory(Message::class)->create([ 'author_id' => $this->author->id, 'receiver_id' => null ]);
        $this->assertEquals($messageWithoutReceiver->receiver_id, null);
    }

    public function testIsAuthor()
    {
        $this->assertFalse($this->message->isAuthor());
        $this->signIn($this->user)->assertFalse($this->message->isAuthor());
        $this->signIn($this->author)->assertTrue($this->message->isAuthor());
    }

    public function testIsParent ()
    {
        $this->assertTrue($this->message->isParent());
        $this->assertFalse($this->messageChild->isParent());
    }

    public function testHasParent ()
    {
        $this->assertEquals($this->message->parent, NULL);
        $this->assertInstanceOf(Message::class, $this->messageChild->parent);
        $this->assertEquals($this->messageChild->parent->id, $this->message->id);
    }

    public function testHasChilds ()
    {
        Notification::fake();
        $count = $this->faker->numberBetween(1, 100) + 1;

        factory(Message::class, $count - 1)->create([ 'message_parent_id' => $this->message->id ]);

        $this->assertCount($count, $this->message->childs);
        $this->assertCount(0, $this->messageChild->childs);
        $this->assertInstanceOf(Collection::class, $this->message->childs);
        $this->assertInstanceOf(Message::class, $this->message->childs->first());
    }
}
