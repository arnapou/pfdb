.PHONY: help

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

all: install cs sa test coverage ## code style + analysis + test

sa: ## static analysis
	vendor/bin/phpstan

test: ## phpunit
	vendor/bin/phpunit --testdox --colors=always

coverage: ## phpunit coverage
ifdef CI_JOB_NAME
	vendor/bin/phpunit --colors=never --coverage-text
else
	vendor/bin/phpunit --colors=always --coverage-text --coverage-html tests/coverage
endif

cs: ## code style
	PHP_CS_FIXER_IGNORE_ENV=1 vendor/bin/php-cs-fixer fix --using-cache=no

install: ## composer install
	composer install --no-interaction --no-progress --optimize-autoloader --prefer-dist --quiet

update: ## composer update
	composer update --no-interaction --no-progress --optimize-autoloader --prefer-dist

build: ## build docker image
	docker build -t registry.gitlab.com/arnapou/pfdb:latest .
