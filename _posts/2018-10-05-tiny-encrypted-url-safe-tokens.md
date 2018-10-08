---
layout: post
title: How to create tiny, URL-safe encrypted tokens
category: posts
draft: false
tags: [php, security, laravel]
---

Recently, I updated my encryption library (<a href="https://github.com/mmeyer2k/dcrypt">dcrypt</a>) support customization of the cipher and checksum options.
This allows for the usage of smaller block and hash sizes, which is useful in size-limited applications.
Coupled with base62 encoding, encrypted blobs are safe for most protocols.

---

Composer requirements:
*<a href="https://github.com/tuupola/base62">tuupola/base62</a>
*<a href="https://github.com/mmeyer2k/dcrypt">mmeyer2k/dcrypt</a>

---

{% highlight php %}
<?php

namespace App\Models;

class TinyCrypt extends \Dcrypt\AesCbc
{

    const CIPHER = 'bf-cbc';

    const CHKSUM = 'crc32';

    public static function decrypt(string $cyphertext, string $password = '', int $cost = 0): string
    {
        $password = \config('app.key') . $password;

        $cyphertext = (new \Tuupola\Base62)->decode($cyphertext);

        // Gether portion of the message without the added random byte at the front of the data stream.
        $cyphertext = substr($cyphertext, 1);

        // Add a custom padding to this string
        return parent::decrypt($cyphertext, $password, $cost);
    }

    public static function encrypt(string $plaintext, string $password = '', int $cost = 0): string
    {
        $password = \config('app.key') . $password;

        $plaintext = parent::encrypt($plaintext, $password, $cost);

        // To prevent leading null bytes from causing errors, we will prepend a random non-null byte to the data
        // This random byte will be stripped off by the decrypt function to return the original string
        $plaintext = \chr(\mt_rand(1, 255)) . $plaintext;

        return (new \Tuupola\Base62)->encode($plaintext);
    }
}
{% endhighlight %}

---