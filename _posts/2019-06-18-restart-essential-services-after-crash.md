---
layout: post
title: Restart essential services
tags: [linux, mysql, redis, nginx, ssh]
last-updated: 2019-07-2 00:00:00 +0000
---

Add this to your production root crontab so you can get some sleep...

```
* * * * * nc -z localhost -w 5 22 || service ssh start
* * * * * nc -z localhost -w 5 80 || service nginx start
* * * * * nc -z localhost -w 5 443 || service nginx start
* * * * * nc -z localhost -w 5 3306 || service mysql start
* * * * * nc -z localhost -w 5 6379 || service redis start
* * * * * nc -z localhost -w 5 9000 || service php7.1-fpm start
```