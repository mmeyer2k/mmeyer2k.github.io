---
layout: post
title: How to queue closures in Laravel 5.1+
category: posts
tags: [laravel, php]
---

As of Laravel 5.1, queueing closure jobs is no longer allowed.
I get it, serializing / storing / deserializing closures is not a good practice. 
Security vulnerabilities are just one of the many good reasons to ditch them.

---

{% highlight php %}
<?php

namespace App\Jobs;

use App\Models\Atomic;
use Illuminate\Contracts\Queue\ShouldQueue;
use SuperClosure\Serializer;

class ClosureJob extends Command implements ShouldQueue
{
    protected $closure;

    public function __construct(\Closure $closure)
    {
        $serializer = new Serializer();

        $this->closure = \Crypt::encryptString($serializer->serialize($closure));
    }

    public function handle()
    {
        $serializer = new Serializer();

        $closure = \Crypt::decryptString($this->closure);

        $closure = $serializer->unserialize($closure);

        $closure();
    }
}
{% endhighlight %}

---
To utilize your job, pass your closure as a parameter.

{% highlight php %}
<?php

use App\Jobs\ClosureJob;

\Queue::push(new ClosureJob(function() {
    // do something...
}));

{% endhighlight %}