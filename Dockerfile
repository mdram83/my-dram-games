# install PHP
FROM php:8.3-fpm

# install node
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash && apt-get install -y nodejs

# install requied apps and extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
&& docker-php-ext-install pdo_mysql pcntl

# install composer
COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

# set workdir
WORKDIR /var/www
COPY . .
