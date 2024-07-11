## php Legal form guesser

This library aim is to guess the legal form of a company given it's extended name.
It can match common dictionary occurrences and acronyms.


## Local development

### First local installation

Run make.sh script

### How to build required docker images

To build the main image (php 7.4)
```bash
# docker command
docker build -f provisioning/php74-composer.Dockerfile -t multidialogo-php-legal-form-guesser-composer:latest .

# docker compose command
docker compose -f provisioning/docker-compose.yml build dev-container
```

To build the php 8.2 version image (note: it does not contain composer).
```bash
docker build -f provisioning/php82-cli.Dockerfile -t multidialogo-php-legal-form-guesser-php82:latest .
```

Keep in mind that the php82 version is intended to be used only to run unit tests, that's why composer is not included.

### Install/update/manage vendors

```bash
# docker command
docker run --rm --interactive --tty -v ${PWD}/:/app multidialogo-php-legal-form-guesser-composer:latest composer <rest of the composer command>

# docker compose command
docker compose -f provisioning/docker-compose.yml run --rm dev-container composer <rest of the composer command>
```

### Run unit tests

Run tests under php 7.4:
```bash
#docker command
docker run --rm --interactive --tty -v ${PWD}/:/app multidialogo-php-legal-form-guesser-composer:latest ./vendor/bin/phpunit -c .

#docker compose command
docker compose -f provisioning/docker-compose.yml run --rm dev-container ./vendor/bin/phpunit -c .
```

Run tests under php 8.2:
```bash
docker run --rm --interactive --tty -v ${PWD}/:/app multidialogo-php-legal-form-guesser-php82:latest ./vendor/bin/phpunit -c .
```

### Semantic versioning

Project is following semantic versioning.
Please use properly git tags before any release in master.

Example:
```bash
git tag -a v0.0.2 -m "Bugfix"
git push --follow-tags
```
