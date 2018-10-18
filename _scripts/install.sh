#!/usr/bin/env bash

apt install ruby ruby-dev make gcc

gem install bundler rouge execjs

bundler install
