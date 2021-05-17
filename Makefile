default: composer
	vendor/bin/php-cs-fixer fix
	vendor/bin/psalm --no-cache
	vendor/bin/phpunit

composer:
	composer install --no-interaction --no-progress --optimize-autoloader --quiet

update:
	composer update --no-interaction --no-progress --optimize-autoloader --quiet
