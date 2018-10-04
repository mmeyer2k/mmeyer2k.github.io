---
title: Limiting Email Send Rate in Laravel
tags: [laravel, redis, php]
category: posts
layout: post
---

Add the following events to your `app/Providers/EventServiceProvider.php` file.

{% highlight php %}
<?php

...

\Illuminate\Mail\Events\MessageSent::class => [
    \App\Listeners\MessageSent::class,
],
\Illuminate\Mail\Events\MessageSending::class => [
    \App\Listeners\MessageSending::class,
],
{% endhighlight %}