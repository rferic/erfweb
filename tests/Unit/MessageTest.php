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
use Tests\TestCase;

class MessageTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;

    protected $message, $user, $author;

    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->author = factory(User::class)->create();
        $this->message = factory(Message::class)->create([ 'user_id' => $this->author->id ]);
    }

    public function testHasAuthor()
    {
        $this->assertInstanceOf(User::class, $this->message->author);
        $this->assertEquals($this->message->author->id, $this->author->id);
    }

    public function testIsAuthor()
    {
        $this->assertFalse($this->message->isAuthor());
        $this->signIn($this->user)->assertFalse($this->message->isAuthor());
        $this->signIn($this->author)->assertTrue($this->message->isAuthor());
    }
}
