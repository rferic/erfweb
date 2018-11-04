<?php
/**
 * Created by PhpStorm.
 * User: ericrf
 * Date: 21/10/2018
 * Time: 01:44
 */

namespace Tests\Unit;

use App\Models\Core\App;
use App\Models\Core\Comment;
use App\Models\Core\Message;
use App\Models\Core\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected $user, $faker;

    protected function setUp ()
    {
        parent::setUp();

        app()['cache']->forget('spatie.permission.cache');

        $this->user = factory(User::class)->create();
    }

    public function testIsMe ()
    {
        $this->assertFalse($this->user->isMe());

        $this->signIn($this->user);
        $this->assertTrue($this->user->isMe());
    }

    public function testHasApps ()
    {
        $count = $this->faker->numberBetween(1, 100);

        Role::create(['name' => 'public']);
        $this->user->assignRole('public');

        $apps = factory(App::class, $count)->create();
        $this->user->apps()->sync($apps);

        $this->assertCount($count, $this->user->apps);
        $this->assertInstanceOf(Collection::class, $this->user->apps);
        $this->assertInstanceOf(App::class, $this->user->apps->first());
    }

    public function testHasMessages ()
    {
        $count = $this->faker->numberBetween(1, 100);

        factory(Message::class, $count)->create();

        $this->assertCount($count, $this->user->messages);
        $this->assertInstanceOf(Collection::class, $this->user->messages);
        $this->assertInstanceOf(Message::class, $this->user->messages->first());
    }

    public function testIsBanned ()
    {
        $this->assertFalse($this->user->isBanned());

        $this->user->delete();

        $this->assertTrue($this->user->isBanned());
    }

    public function testHasComments ()
    {
        $count = $this->faker->numberBetween(1, 100);

        factory(Comment::class, $count)->create([
            'user_id' => $this->user->id
        ]);

        $this->assertCount($count, $this->user->comments);
        $this->assertInstanceOf(Collection::class, $this->user->comments);
        $this->assertInstanceOf(Comment::class, $this->user->comments->first());
    }
}
