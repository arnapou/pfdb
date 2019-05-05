language: php

php:
  - 5.6
  - 7.0
  - 7.1
  - 7.2
  - 7.3

branches:
  only:
    - master

cache:
  directories:
    - vendor
    - $HOME/.composer/cache/files

before_install:
  - composer self-update

install:
  - composer install --no-interaction --prefer-source

script:
  - phpunit
