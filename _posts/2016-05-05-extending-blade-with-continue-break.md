---
layout: post
title: Extending Blade to support continue and break
---

Laravel's Blade templating engine is easy to extend. The following example service provider
adds support for `continue` and `break` within loops with `@continue` and `@break`. Even though
it is a best practice not to use logic like that in views, it is handy sometimes when porting
legacy code.

```php
<?php

namespace App\Providers;

use Blade;
use Illuminate\Support\ServiceProvider;

class BladeExtendServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // Create a custom blade directive for the @continue and @break commands
        Blade::directive('continue', function () {
            return "<?php continue; ?>";
        });
        Blade::directive('break', function () {
            return "<?php break; ?>";
        });
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
```
