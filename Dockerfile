FROM registry.gitlab.com/arnapou/docker/php:8.3-dev as build

COPY --chown=www-data:www-data . /app
RUN composer install --no-interaction --no-progress --optimize-autoloader --no-dev \
 && rm composer.json composer.lock

FROM registry.gitlab.com/arnapou/docker/php:8.3-frankenphp as final

COPY --from=build /app /app
RUN sed -i 's!/app/public!/app/demo!' /etc/caddy/Caddyfile
