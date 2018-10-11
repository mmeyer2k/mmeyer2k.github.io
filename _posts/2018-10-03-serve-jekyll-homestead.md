---
layout: post
title: Serve Jekyll sites from a Homestead virtual machine
category: posts
tags: [laravel, jekyll, nginx, bash, vagrant]
last-updated: 2018-10-11 00:00:00 +0000
---
@Jekyll is a great way to generate static websites. Its simplicity and beauty make it a natural choice for developers
who prefer to use laravel for heavy lifting but need to create static sites for some reason (like this blog).

For development, Jekyll ships with a `serve` mode which runs an integrated http server and detects local file changes.
However, I found this was un-reliable in vagrant/homestead shared folders, so I opted for a more integrated solution.

## Create a nginx configuration script for Jekyll sites

In your homestead folder, create a new file at `/scripts/serve-jekyll.sh` with the following content.

```bash
#!/usr/bin/env bash

block="server {
    listen $3 $7;
    listen $4 ssl http2 $7;
    server_name $1;
    root \"$2\";

    index index.html;

    charset utf-8;

    # disable all caching which could cause confusion in a dev environment
    add_header 'Cache-Control' 'no-store, no-cache, must-revalidate, proxy-revalidate, max-age=0';
    expires off;
    
    # restrict to only allow GET and HEAD directives
    # this is optional and mostly done for autistic reasons
    add_header Allow \"GET, HEAD\" always;
    if ( \$request_method !~ ^(GET|HEAD)\$ ) {
    	return 405;
    }
        
    # if request is asking for .html page, redirect to url without .html
    # (for seo purposes, you may not want this)
    if ( \$request_uri ~ ^/(.*)\.html\$ ) {
        return 302 /\$1;
    }
    
    # attempt to resolve urls lacking .html extension
    try_files $uri $uri.html $uri/ =404;
    
    # disable logging
    access_log off;
    error_log off;
    
    sendfile off;
    
    # set error templates
    error_page 404 /404.html;
    error_page 405 /405.html;

    ssl_certificate     /etc/nginx/ssl/$1.crt;
    ssl_certificate_key /etc/nginx/ssl/$1.key;
}
"

echo "$block" > "/etc/nginx/sites-available/$1"
ln -fs "/etc/nginx/sites-available/$1" "/etc/nginx/sites-enabled/$1"
```

---

## Modify your Homestead.yaml
```yaml
    sites:
      ... 
      - map: jekyll.test
        to: /path_to_jekyll/_site
        type: jekyll
      ...
```


## Start developing
Once the configurator file is created and Homestead.yaml changes saved, re-provision the VM for the change to take effect. 
To begin developing run the following command:

```bash
sudo bundle exec jekyll build --watch --force_polling --drafts --future
```