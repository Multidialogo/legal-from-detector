#!/bin/bash

docker compose -f provisioning/docker-compose.yml rm -f
docker compose -f provisioning/docker-compose.yml build --no-cache
docker compose -f provisioning/docker-compose.yml run dev-container composer install
docker compose -f provisioning/docker-compose.yml run dev-container vendor/bin/phpunit -c .
