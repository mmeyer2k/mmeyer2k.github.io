---
layout: post
title: Using PHP semaphores to ensure exclusive execution
tags: [php]
---

A common problem faced in multi-user, real-world web applications is errors that arise with simultaneous access to a resource.
Ensuring that a block of code can only be running in a single thread eliminates this class of problems and ensures data integrity.
PHP's semaphores are useful for this purpose, but properly implementing them can be tricky and it is best to encapsulate this functionality.

---
#### Pseudocode demonstration of error

To understand the crux of the issue, look at this code:

```php
<?php

$x = get_number_from_database();

$x++;

sleep(5);

save_number_to_database($x);

```

The results are counter intuitive when being accessed by multiple threads.
Two successive calls (within 5 seconds) lead to only one incrementation being _saved_.

---

#### Create `SemLock` model

Create this model somewhere in your source code. 
I'm going to assume you have the basics of autoloading conquered.

```php
<?php

class SemLock
{
    /**
     * Ensure synchronized execution with PHP semaphore.
     *
     * @param string $semkey
     * @param \Closure $closure
     * @return mixed
     * @throws \Exception
     */
    public static function synchronize(string $semkey, \Closure $closure)
    {
        // Convert the key into an integer representation
        // String keys are simply easier to read
        $semkey = self::key2int($semkey);

        // Get the semaphore
        $sem = sem_get($semkey, 1);

        // If semaphore can not be created, throw an exception
        if (!$sem) {
            throw new \Exception('Could not obtain semaphore.');
        }

        // Wait for semaphore
        $acquired = sem_acquire($sem);

        // Throw exception if semaphore can not be acquired
        if (!$acquired) {
            throw new \Exception('Could not lock semaphore.');
        }

        // Execute the closure and gather the return value
        $ret = $closure();

        // Release the semaphore for next thread
        sem_release($sem);

        // Return whatever the closure returned
        return $ret;
    }

    /**
     * Converts a string key into a decimal integer which is used by sem_get()
     *
     * @param string $semkey
     * @return int
     */
    private static function key2int(string $semkey): int
    {
        // Hash key with md5
        $semkey = md5($semkey);

        // Take first 4 bytes of hash
        $semkey = substr($semkey, 0, 8);

        // Convert to decimal and return
        return hexdec($semkey);
    }
}

```

---

### Basic Usage

To fix our flawed demonstration above, we use `SemLock` to ensure singular execution.

```php
<?php
SemLock::synchronize('increment_value', function () {
    $x = get_number_from_database();

    $x++;

    sleep(5);

    save_number_to_database($x);
});
```

Now, only one thread at a time can access this function and two successive calls will result in two incrementations being _saved_.

---

#### Handling Return Values

Ocassionally, it is necessary to use data that was derived inside of the synchronized closure _after_ the lock is released.
Values returned inside of the closure are also returned from the `synchronize` function. 

```php
<?php
$value = SemLock::synchronize('increment_value', function () {
    return 1 + 1;
});

echo $value; # = 2
```