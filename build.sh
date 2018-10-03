#!/usr/bin/env bash

sudo bundle exec jekyll clean

sudo bundle exec jekyll build --watch --force_polling --drafts