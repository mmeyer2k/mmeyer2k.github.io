---
layout: post
title: Protecting IDs in URLs with authenticated encryption
---

Passing identifiers inside of URL's is a very common practice, but it offers no security against manipulation of the ID value. Simple encoding of this value (via base64 or hex, for example) is trivial to reverse. Even more sophisticated techiniques like simple encryption with XOR is vulnerable to bit flipping attacks.

The only complete solution is to use authenticated encryption. An encrypted payload is "authenticated" if it carries along a checksum of itself to compare against before decryption.

To demonstrate I will be using the AES 256 encryption functions in my own [dcrypt](https://github.com/mmeyer2k/dcrypt) encryption library and a URL friendly base62 encoder from my polyfill library called [retro](https://github.com/mmeyer2k/retro).

First we need to generate a url..

```php
<?php
$id = \Dcrypt\Aes::encrypt('secret_id', 'password');
$url = 'https://www.example.com/users?id=' . base62_encode($id);
# outputs something like:
# http://www.example.com/users?id=1kt43UPAQmt6D900kuWGoi496ckUYr2mPKYsyMs070rA5lOMu1hNN8W7Y5Y2ePGqoECbmchC96mRO5bUXFozGn4n
```

To decrypt, reverse the process:

```php
<?php
$id = \Dcrypt\Aes::decrypt(base62_decode($_GET['id']), 'password');
```

Now, any kind of tampering will result in a checksum mismatch and a thrown exception.
