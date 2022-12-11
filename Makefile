.PHONY: help

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

all: install cs analysis test ## code style + analysis + test

analysis: ## static analysis
	vendor/bin/psalm --no-cache
	vendor/bin/phpstan

test: ## phpunit
	vendor/bin/phpunit

cs: ## code style
	PHP_CS_FIXER_IGNORE_ENV=1 vendor/bin/php-cs-fixer fix --using-cache=no

install: ## composer install
	composer install --no-interaction --no-progress --optimize-autoloader --quiet

update: ## composer update
	composer update --no-interaction --no-progress --optimize-autoloader
