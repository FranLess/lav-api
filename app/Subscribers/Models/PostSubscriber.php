<?php

namespace App\Subscribers\Models;

use App\Events\Models\Post\PostCreated;
use App\Listeners\SendWelcomeEmail;
use Illuminate\Events\Dispatcher;

class PostSubscriber
{
    public function subscribe(Dispatcher $events)
    {
        $events->listen(PostCreated::class, SendWelcomeEmail::class);
    }
}
