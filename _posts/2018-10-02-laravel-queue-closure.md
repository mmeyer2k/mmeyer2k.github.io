---
layout: post
title: How to queue closures in Laravel 5.1+
tags: [laravel, php]
---

As of Laravel 5.1, queueing closure jobs is no longer allowed.
I get it, serializing / storing / deserializing closures is not a good practice. 
Security vulnerabilities are just one of the many good reasons to ditch them...

But just one more closure job won't hurt, right? I can quit any time. Yep, any... t-time.

---
Add this job to your jobs folder (normally `app/Jobs`).
```php
<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use SuperClosure\Serializer;

class ClosureJob extends Command implements ShouldQueue
{
    protected $closure;

    public function __construct(\Closure $closure)
    {
        $serializer = new Serializer();
        
        $serialized = $serializer->serialize($closure);

        $this->closure = \Crypt::encryptString($serialized);
    }

    public function handle()
    {
        $serializer = new Serializer();

        $closure = \Crypt::decryptString($this->closure);

        $closure = $serializer->unserialize($closure);

        $closure();
    }
}
```

---
To utilize your job, pass your closure as a parameter.

```php
<?php

use App\Jobs\ClosureJob;

\Queue::push(new ClosureJob(function() {
    // do something...
}));
```