---
layout: post
title: Serve Jekyll Sites From Homestead Virtual Machine
category: posts
tags: [laravel, jekyll, nginx, bash, vagrant]
img-title: /images/jekyll.png
---
Jekyll is a great way to generate static websites.

## Create a nginx configuration script for Jekyll
In your homestead folder, create a new file at `/scripts/serve-jekyll.sh` with the following content.

{% highlight bash %}
#!/usr/bin/env bash

block="server {
    listen $3 $7;
    listen $4 ssl http2 $7;
    server_name $1;
    root \"$2\";

    index index.html;

    charset utf-8;

    try_files \$uri.html \$uri/ \$uri =404;

    access_log off;
    error_log off;
    sendfile off;

    ssl_certificate     /etc/nginx/ssl/$1.crt;
    ssl_certificate_key /etc/nginx/ssl/$1.key;
}
"

echo "$block" > "/etc/nginx/sites-available/$1"
ln -fs "/etc/nginx/sites-available/$1" "/etc/nginx/sites-enabled/$1"
{% endhighlight %}

---

## Modify your Homestead.yaml

    sites:
      ... 
      - map: jekyll.test
        to: /path_to_jekyll/_site
        type: jekyll
      ...


## Start developing
