---
layout: post
title: One Laravel 5 instance to rule them all
tags: [laravel, php]
last-updated: 2018-10-11 00:00:00 +0000
---

Laravel 5 is awesome, but out of the box, it seems to only allow for a single web root to be served for each installation. 
However, with a few minor tweaks, one installation of the framework can power any number of applications.

---
 
#### Handling the public/ folder
Most of the time, the `public/` folder IS the webroot. 
For our purpose, `public/example/` will be the new structure. 
This gives the flexibility to add as many roots as needed.

To turn `/public/example` into a webroot, place the following into `/public/example/index.php`:

```php
<?php

// Set a variable that contains the name of the
// webroot that was entered. This will be
// used to load the correct route file.
define('PUBLIC_SUBROOT', basename(__DIR__));

// Launch laravel as normal.
require __DIR__ . '/../index.php';
```

---

#### Re-Routing
Usually in Laravel 5, an applications's routes are stored at `/routes/app.php`. 
All that needs to be done to make this trick work is to modify this file to contain something like the following:

```php
<?php

require base_path('/resources/routes/' . PUBLIC_SUBROOT . '.php');
```

Now when a request comes into example.com (or where ever) Laravel will run and execute the `/resources/routes/example.php` file. 
Laravel should be working as usual now.

---

#### Some things will break!
Route caching, asset publishing and some unit testing features will certainly no longer work. 
But this is a small price to pay when porting a large, multi-domain legacy application to Laravel.

Enjoy!
