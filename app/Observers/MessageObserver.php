<?php

namespace App\Observers;

use App\Models\Core\Message;
use App\Notifications\MessageDeleted;
use App\Notifications\MessageReceived;

class MessageObserver
{
    /**
     * Handle the message "created" event.
     *
     * @param  \App\Message  $message
     * @return void
     */
    public function created ( Message $message )
    {
        if ( !is_null($message->receiver) ) {
            $message->receiver->notify(new MessageReceived($message));
        }
    }

    /**
     * Handle the message "updated" event.
     *
     * @param  \App\Message  $message
     * @return void
     */
    public function updated ( Message $message )
    {
        //
    }

    /**
     * Handle the message "deleted" event.
     *
     * @param  \App\Message  $message
     * @return void
     */
    public function deleted ( Message $message )
    {
        if ( !is_null($message->author) ) {
            $message->author->notify(new MessageDeleted($message));
        }
    }

    /**
     * Handle the message "restored" event.
     *
     * @param  \App\Message  $message
     * @return void
     */
    public function restored ( Message $message )
    {
        //
    }

    /**
     * Handle the message "force deleted" event.
     *
     * @param  \App\Message  $message
     * @return void
     */
    public function forceDeleted ( Message $message )
    {
        //
    }
}
