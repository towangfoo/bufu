# Docker setup for PHP / MySQL Development environment
Heavily inspired by Pascal Landau's blog series, esp. Part III: [Structuring the Docker setup for PHP Projects](https://www.pascallandau.com/blog/structuring-the-docker-setup-for-php-projects/).

Run a minimal development infrastructure for PHP developers in Docker. How to organize the docker folder structure (e.g. shared scripts for containers)? 
How to use shared configuration / scripts across multiple services? How to establish a convenient workflow via `make`? How to add a MySQL database to the mix?

## Containers included
- php-fpm
- php-cli / workspace
- nginx
- mysql

## Add ssh keys before building
In `docker/workspace/.ssh` add a newly generated public and private key pair for SSH'ing into the workspace container.

## Getting started
````
make docker-clean
make docker-init
make docker-build-from-scratch
make docker-test
make docker-prepare-app
````
