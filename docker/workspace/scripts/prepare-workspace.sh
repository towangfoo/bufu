#!/bin/sh

# do as project files owner
su www-data

# create required dirs and files
mkdir -p data/cache/config
mkdir -p data/cache/shariff
mkdir -p data/DoctrineORMModule/Proxy
mkdir -p data/logs

touch data/logs/development.log

# composer install
composer install --prefer-dist --no-suggest --optimize-autoloader