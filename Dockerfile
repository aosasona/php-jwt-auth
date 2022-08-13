FROM php:8.0-apache

WORKDIR /var/www/html

COPY . .

COPY api.conf /etc/apache2/sites-available/api.conf

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install -y libpq-dev  \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql  \
    && composer install --no-dev  \
    && /usr/local/bin/docker-php-ext-enable pdo_mysql \
    && /usr/local/bin/docker-php-ext-enable pdo_pgsql

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf && \
    a2enmod rewrite && \
    a2dissite 000-default && \
    a2ensite api && \
    service apache2 restart

EXPOSE 80

ENTRYPOINT ["bash", "Docker.sh"]