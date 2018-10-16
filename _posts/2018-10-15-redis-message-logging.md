---
layout: post
title: Memory-safe redis message logging
tags: [php, laravel, redis, linux]
---

Every sys-admin knows that resource allocation is everything.
In redis, this is especially important.
Keys are evicted when redis surpasses its `maxmemory` value, which, depending on your application, could be disasterous.
If you use redis to log information, you must take care to prune out old entries or somehow free up data.
Otherwise, memory is lost or no new keys are written.
This model also takes the extra step of applying a 3 month expiration to the log, so in case you ever stop using it, it will expire after 3 months.

To safely handle using redis as a message log, this class is a great starting point.

---

#### Create `RedisLog` model

```php
<?php

namespace App;

class RedisLog extends Model
{
    private $limit;
    private $key;

    public function __construct(string $key, int $limit = 10000)
    {
        $this->limit = $limit;
        $this->key = $key;
    }

    public function save(string $text)
    {
        // rpush
        \Redis::rpush($this->key, $text);

        // ltrim
        \Redis::ltrim($this->key, -$this->limit, -1);

        // expire
        \Redis::expire($this->key, 60 * 60 * 24 * 30 * 3);
    }
}
```

---

#### Use it!

```php
<?php

$logger = new \App\RedisLog('keyname', 100);

$logger->save('here is a new message!');
```

---

#### View it!

Unfortunately, there is no good way to mimic `tail` (that I could think of, anyway).
A decent option is to use `watch` to contantly view the last 10 (or so) log messages.

```bash
watch redis-cli keyname -10 -1
```