<?php

namespace App\Listeners;

use App\Events\ChatRoomsUpdated;
use App\Events\UserIsTyping;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendUserIsTypingNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\UserIsTyping  $event
     * @return void
     */
    public function handle(UserIsTyping $event)
    {
        //
    }
}
