---
layout: post
title: Using redlock to ensure exclusive execution
tags: [php, laravel, redis]
---

A common problem faced in multi-user, real-world web applications is problems that can occur with simultaneous access to a resource.
Ensuring that a block of code can only be running in a single thread eliminates this class of problems.

---
#### Pseudocode demo

To understand the crux of the issue, look at this code:

```php
<?php

$x = get_number_from_database();

$x++;

sleep(5);

save_number_to_database($x);

```

The results are counter intuitive when being accessed by multiple threads.
Two successive calls lead to only one incrementation being saved.

---

#### Create `RedisLock` model

```
php artisan make:model RedisLock
```

```php
<?php

namespace App;

class RedisLock
{
    public function synchronize(string $key, int $wait, \Closure $closure)
    {
        // Load redis instance
        $redis = new \Predis\Client("redis://localhost");

        // Build the mutex object
        $mutex = new \malkusch\lock\mutex\PredisMutex([$redis], "redlock:$key", $wait);

        // Run closure
        return $mutex->synchronized($closure);
    }
}
```

---

#### Return values
```
<?php

```