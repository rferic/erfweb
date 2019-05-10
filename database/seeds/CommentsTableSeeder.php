<?php

use Illuminate\Database\Seeder;

use App\Models\Core\User;
use App\Models\Core\Comment;

class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::whereRoleIs('user')->get()->first();

        factory(Comment::class, 5)->create()->each(function ($comment) use ($user) {
            $user->comments()->save($comment);
        });
    }
}
