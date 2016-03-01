---
layout: post
title: Placeholder post
---

Laravel 5 is awesome, but what if you are migrating a huge project with many webroots? With a few minor changes, you can run an infinite number of sites of a single laravel install.
<h4>Handling the public/ folder</h4>
Most of the time, the public/ folder IS the webroot. For our purpose, public/example/ will be the new structure. This gives us the flexibility to add as many roots as needed.

To turn /public/example into a webroot, place the following into /public/example/index.php:

```php
<?php

// Set a variable that contains the name of the
// webroot that was entered. This will be
// used to load the correct route file.
define('PUBLIC_SUBROOT', basename(__DIR__));

// Launch laravel as normal.
require __DIR__ . '/../index.php';
```
<h4>Re-Routing</h4>
Usually, an applications's routes are stored at /app/Http/routes.php. All that needs to be done to make this trick work is to modify this file to contain something like the following:

```php
<?php

if (defined('PUBLIC_SUBROOT')) {
require base_path() . '/resources/routes/' . PUBLIC_SUBROOT . '.php';
}
```

Now when a request comes into example.com (or whereever) Laravel will run and execute the /resources/routes/example.php file. Laravel should be working as usual now.
<h4>Some things will break</h4>
Route caching will certainly no longer work. Things like asset publishing may be more complicated.

Enjoy!
