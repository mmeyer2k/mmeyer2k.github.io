#!/usr/bin/env bash

sudo bundle exec jekyll clean

sudo bundle exec jekyll serve --watch --force_polling --host=192.168.10.10 --drafts