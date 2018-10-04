---
title: 
tags: [laravel, redis, php]
layout: post
---



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