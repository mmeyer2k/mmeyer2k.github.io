---
title: Limiting email send rate in Laravel
tags: [laravel, redis, php]
layout: post
---
Recently, I was tasked with diagnosing why emails sent by a web application to a company internal email address were not always arriving in the inbox.
This internal email was hosted on Google Apps, which has a receiving rate limit of 60 per minute.
Any emails that exceed the quota are dropped, though the documentation says they are bounced. 
THIS IS A LIE!

To solve this problem, I used Laravel event listeners and the magic of @antirez's Redis.

---

Add the following events to your `app/Providers/EventServiceProvider.php` file's `listen` array.

{% highlight php %}
<?php
protected $listen = [

    ...

    \Illuminate\Mail\Events\MessageSent::class => [
        \App\Listeners\MessageSent::class,
    ],
    \Illuminate\Mail\Events\MessageSending::class => [
        \App\Listeners\MessageSending::class,
    ],

    ...

];
{% endhighlight %}

---

The `MessageSent` listener will use Redis to count the number of messages sent. 
The current minute is stored as part of the redis key.
When a new minute starts, a new key will be used.
To prevent the keys from overlapping to the next hour, an expiration value is set to 30 minutes.

{% highlight php %}
<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class MessageSent
{
    public function __construct()
    {
        //
    }

    public function handle($event)
    {
        $key = self::getCounterKey();

        // 30 minute cache time
        $ttl = 60 * 30;

        // Create new redis key every minute with count
        \Redis::incr($key);

        // Set expiration so these keys die after they are no longer needed
        \Redis::expire($key, $ttl);
    }

    public static function getCounterKey(): string
    {
        return "mailcounter:" . date('i');
    }
}

{% endhighlight %}

---

The `MessageSending` event runs _before_ an email gets sent and is where the rate limiting actually occurs.
This event can be tuned to fit your needs.

{% highlight php %}
<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class MessageSending
{

    public function __construct()
    {
        //
    }

    public function handle($event)
    {        
        // Threshhold number of messages before limiting occurs
        $threshhold = 45;
        
        // Number of seconds to sleep before sending.
        // Dev environments don't need to sleep (presumably)
        $sleep = \App::environment('production') ? 5 : 0;
        
        // Get the number of emails sent this minute
        $count = (int)\Redis::get(MessageSent::getCounterKey());

        // If more than 45 messages have been sent this minute, then we will sleep for 5 seconds.
        // This will prevent us from exceeding the 60 email/minute limit on our distribution lists.
        if ($count > $threshhold) {
            sleep($sleep);
        }
    }
}

{% endhighlight %}
