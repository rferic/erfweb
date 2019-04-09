<?php
/**
 * Created by PhpStorm.
 * User: ericrf
 * Date: 21/10/2018
 * Time: 02:00
 */

namespace Tests\Unit;

use App\Models\Core\Comment;
use App\Models\Core\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;

    protected $comment, $user, $author;

    protected function setUp (): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->author = factory(User::class)->create();
        $this->comment = factory(Comment::class)->create([ 'user_id' => $this->author->id ]);
    }

    public function testHasAuthor ()
    {
        $this->assertInstanceOf(User::class, $this->comment->author);
        $this->assertEquals($this->comment->author->id, $this->author->id);
    }

    public function testIsAuthor ()
    {
        $this->assertFalse($this->comment->isAuthor());
        $this->signIn($this->user)->assertFalse($this->comment->isAuthor());
        $this->signIn($this->author)->assertTrue($this->comment->isAuthor());
    }
}
