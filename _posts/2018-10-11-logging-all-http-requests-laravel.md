---
layout: post
title: Logging all HTTP requests to a laravel application
tags: [php, laravel, redis]
---

Monitoring the flow of HTTP requests into a web application is often helpful for debugging and watching for attacks.
The cleanest and safest way I have found to do this, even in production, is with redis + laravel middleware.

---

#### Add new middleware
Use artisan to create a new middleware.
```
php artisan make:middleware UriLogger
```

Add your new middleware to `app/Http/Kernel.php` and then make your `handle` method resemble the following...

```php
<?php

namespace App\Http\Middleware;

use Closure;

class UriLogger
{
    public function handle($request, Closure $next)
    {
        // Redis key name
        $key = 'urilog';

        // Number of requests to store in list
        $limit = 5000;

        $uri = \Request::fullUrl();

        $time = time();

        // Pad the method to normalize row layout
        $meth = str_pad(\Request::method(), 6);

        \Redis::rpush($key, "$time | $meth | $uri");

        // Trim the list to prune oldest values
        \Redis::ltrim($key, -$limit, -1);

        return $next($request);
    }
}
```

---

#### Monitor requests
Use `watch` to show the last 20 entries in the log and refresh every second. 
This view gives a good idea a web application's access patterns.

```bash
watch redis-cli lrange urilog -20 -1
```

<img src="/images/urilog.png">