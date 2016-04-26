---
layout: post
title: Redis Facade Namespace Collision
---

### Be careful when using phpredis and Laravel's `\Redis` facade!

While upgrading from PHP 5.5 to 5.6 on a CEntOS6 machine, I started getting the following random exception `FatalErrorException`:
```
Non-static method Redis::get() cannot be called statically, assuming $this from incompatible context
```

After some googling, I decided to check in `/etc/php.d/`. Sure enough, the Redis extension I had long ago disabled was now running again.
This extension registers a `Redis` class in the root namespace which conflicts with the laravel facade. 

You can disable/delete the offending ini file or run `pecl uninstall php-redis`;

If you are useing `phpredis/phpredis` + laravel then you do not also need the PHP Redis extension. A safer long-term option may be to
make a wrapper class for Redis that does not rely on the Redis facade.
