---
layout: post
title: Extending Blade to support continue and break
---

Laravel's Blade templating engine is easy to extend. 
The following example service provider adds support for `continue` and `break` within loops with `@continue` and `@break`. 
Even though it is a best practice not to use logic like that in views, it is handy sometimes when porting legacy code.

{% highlight php %}
<?php

namespace App\Providers;

use Blade;
use Illuminate\Support\ServiceProvider;

class BladeExtendServiceProvider extends ServiceProvider
{
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

    public function register()
    {
        //
    }
}
{% endhighlight %}
