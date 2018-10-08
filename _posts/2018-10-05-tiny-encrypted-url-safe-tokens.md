---
layout: post
title: How to create tiny, URL-safe encrypted tokens
category: posts
draft: false
tags: [php, security, laravel, composer]
---

Recently, I updated my encryption library (<a href="https://github.com/mmeyer2k/dcrypt">dcrypt</a>) support customization of the cipher and checksum options.
This allows for the usage of smaller block and hash sizes, which is useful in size-limited applications.
Coupled with base62 encoding, encrypted blobs are safe for most protocols.

Smaller hashes and block sizes are less secure. You will have to be the judge of what security level is adequate. 
The example below uses the smallest values possible (blowfish + crc32).

---

#### Install composer requirements:
<ul>
<li>
<a href="https://github.com/tuupola/base62">tuupola/base62</a>
</li>
<li>
<a href="https://github.com/mmeyer2k/dcrypt">mmeyer2k/dcrypt</a>
</li>
</ul>

---

{% highlight php %}
<?php

namespace App\Models;

class TinyCrypt extends \Dcrypt\AesCbc
{

    const CIPHER = 'bf-ofb';

    const CHKSUM = 'crc32';

    public static function decrypt(string $cyphertext, string $password, int $cost = 0): string
    {
        $cyphertext = (new \Tuupola\Base62)->decode($cyphertext);

        return parent::decrypt($cyphertext, $password, $cost);
    }

    public static function encrypt(string $plaintext, string $password, int $cost = 0): string
    {
        $plaintext = parent::encrypt($plaintext, $password, $cost);

        return (new \Tuupola\Base62)->encode($plaintext);
    }
}
{% endhighlight %}

---

#### Test sizes
Once you have added the TinyCrypt, use `tinker` to view the sizes of your encrypted tokens. 
Plaintext string size is on the left; final output size is at the far right.

    $x = 1; while($x <= 100) {$b = \App\Models\TinyCrypt::encrypt(str_repeat("A", $x)) ; echo $x . ' --> ' . $b . '(' . strlen($b) . ')' . PHP_EOL; $x++;}

<a href="/images/bf-ofb.png" target="_blank">
![tinker output](/images/bf-ofb.png){: .img-responsive }
</a>

---

#### Usage
{% highlight php %}

<?php

use App\Models\TinyCrypt;

$token = TinyCrypt::encrypt('secret', 'password');

# you can now use this token in a url, for example
# https://example.com/something/$token

$secret = TinyCrypt::decrypt($token, 'password');

{% endhighlight %}

---