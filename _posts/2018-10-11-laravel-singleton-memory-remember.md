---
layout: post
title: Laravel in-memory rememberance singleton object
tags: [php, laravel]
---

Often times, there is a need to remember a value across far-reaching areas of an application for just a single request.
Traditionally, global variables are used for this purpose, but they are unwieldy and somewhat of an anti-pattern.
A globally accessable singleton object which simply stores and fetches key/value pairs from memory has several advantages.

The memory is freed at the end of the request.

---
## Create `Remember` model

Create a new model at `app\Models\Remember.php`.

```php
<?php

namespace App\Models;

class Remember
{
    private static $instance = null;

    private static $attrs = [];

    private function __construct()
    {

    }

    /**
     * Start the singleton instance
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Set a variable
     *
     * @param string $key
     * @param $value
     */
    public static function set(string $key, $value)
    {
        self::$attrs[$key] = $value;
    }

    /**
     * Get a variable
     *
     * @param string $key
     * @return mixed
     */
    public static function get(string $key)
    {
        return self::$attrs[$key];
    }
}
```

--- 

## Use `Remember` model

```php
<?php

use App\Models\Remember;

// Set a key/value
Remember::set('key', 'value');

// Get the value, or throw an exception if not found
$value = Remember::get('key');
```

---

## Value types

Values can be of any type due to the fact they are stored as a simple indexed array.

```php
<?php

use App\Models\Remember;

// Set a key/value
Remember::set('key', [1, 2, 3, 4, 5]);

```