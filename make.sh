#!/bin/bash

docker build -f provisioning/php74-composer.Dockerfile -t multidialogo-php-legal-form-guesser-composer:latest .
docker build -f provisioning/php82-cli.Dockerfile -t multidialogo-php-legal-form-guesser-php82:latest .
docker run --rm --interactive --tty -v "${PWD}"/:/app multidialogo-php-legal-form-guesser-composer:latest composer install
