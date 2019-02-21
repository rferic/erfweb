<?php

use Illuminate\Database\Seeder;

use App\Models\Core\Message;

class MessagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Message::class, 10)->create();
        factory(Message::class, 10)->create([ 'message_parent_id' => App\Models\Core\Message::all()->random()->id ]);
        factory(Message::class, 10)->create([ 'message_parent_id' => App\Models\Core\Message::all()->random()->id ]);
        factory(Message::class, 10)->create([ 'message_parent_id' => App\Models\Core\Message::all()->random()->id ]);
        factory(Message::class, 10)->create([ 'message_parent_id' => App\Models\Core\Message::all()->random()->id ]);
        factory(Message::class, 10)->create([ 'message_parent_id' => App\Models\Core\Message::all()->random()->id ]);
        factory(Message::class, 10)->create([ 'message_parent_id' => App\Models\Core\Message::all()->random()->id ]);
        factory(Message::class, 10)->create([ 'message_parent_id' => App\Models\Core\Message::all()->random()->id ]);
        factory(Message::class, 10)->create([ 'message_parent_id' => App\Models\Core\Message::all()->random()->id ]);
        factory(Message::class, 10)->create([ 'message_parent_id' => App\Models\Core\Message::all()->random()->id ]);
        factory(Message::class, 10)->create([ 'message_parent_id' => App\Models\Core\Message::all()->random()->id ]);
        factory(Message::class, 10)->create([ 'message_parent_id' => App\Models\Core\Message::all()->random()->id ]);
    }
}
