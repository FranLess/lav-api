<?php

namespace App\Subscribers\Models;

use App\Events\Models\Comment\CommentCreated;
use App\Listeners\SendWelcomeEmail;
use Illuminate\Events\Dispatcher;

class CommentSubscriber
{
    public function subscribe(Dispatcher $events)
    {
        $events->listen(CommentCreated::class, SendWelcomeEmail::class);
    }
}
