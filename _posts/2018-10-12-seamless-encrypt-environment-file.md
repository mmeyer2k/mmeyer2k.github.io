---
layout: post
title: Seamless encrypted environment files in Laravel
tags: [php, laravel, security, linux]
---

Best practice and common sense (if there is a difference between the two) dictate that storing secret information like API keys in your repository is _bad_.
Keeping `.env.production` encrypted is an effective way to prevent malicious actors and developer mistakes from impacting the stability of your application.

---

#### Generate a key file

To keep the env file secure, we must have a secure password which will be stored and distributed outside of the repository.

```bash
strings /dev/urandom | grep -o '[[:alnum:]]' | head -n 32 | tr -d '\n'; echo
```

Save the generated password to the base of your laravel project in a file named `.envkey`.
Only the most trusted developers and production machines should get the key.
<!--See my post on secure developer vaults <a href="">here</a>.-->

---

#### Structure
When everything is complete, your env files will look something like this.
```
\                           # base_path()
 | .env                     # your local config
 | .envkey                  # secret key file
 | .env.production          # plaintext production env file (.gitignored)
 | .env.production.enc      # encrypted production env file (source controlled)
```

---

#### Create console commands

Add `app/Console/Commands/EnvEncrypt.php`.

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class EnvEncrypt extends Command
{
    protected $signature = 'env:encrypt';

    protected $description = 'Encrypt production environment variable file';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $crypt = \Illuminate\Encryption\Encrypter(Decrypt::getKeyFileContents());

        $path = \base_path('.env.production');

        $text = \file_get_contents($path);

        $enc = $crypt->encrypt($text);

        \file_put_contents($path . '.enc', $enc);

        $this->info("Encypted environment file");
    }
}
```

---

Add `app/Console/Commands/EnvDecrypt.php`.

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class EnvDecrypt extends Command
{
    protected $signature = 'env:decrypt';

    protected $description = 'Decrypt production environment variable file';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $crypt = \Illuminate\Encryption\Encrypter(self::getKeyFileContents());

        $path = \base_path('.env.production');

        $enc = \file_get_contents($path . '.enc');

        $dec = $crypt->decrypt($enc);

        \file_put_contents($path, $dec);

        $this->info("Decrypted environment file");
    }

    public static function getKeyFileContents(): string
    {
        return trim(\file_get_contents(\base_path('.envkey')));
    }
}

```

---

#### Basic usage

Encryption and decryption functions can be accessed via artisan commands. 
Always be sure to freshly run `env:decrypt` before making changes so that any other developer changes in `.env.production.enc` are not reversed.

```bash
php artisan env:encrypt # .env.production -> (encrypt) -> .env.production.enc
php artisan env:decrypt # .env.production <- (decrypt) <- .env.production.enc
```
---

#### Automate change deployment

Add this to any relevant sections of your `Envoy.blade.php` to decrypt and overwrite the contents of `.env` with `.env.production`.

```bash
php artisan env:decrypt ; cp -fv .env.production .env
```

---

#### Sanitize repository
Now that you are following best practices, its time to atone for past mistakes.

- delete the plaintext `.env.production` from source control
- add `.env.production` to `.gitignore` or whatever you have to do to exclude this file from source control
- change all important api keys