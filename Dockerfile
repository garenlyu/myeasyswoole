# @description php image base on the debian 9.x
#
#                       Some Information
# ------------------------------------------------------------------------------------
# @link https://hub.docker.com/_/debian/      alpine image
# @link https://hub.docker.com/_/php/         php image
# @link https://github.com/docker-library/php php dockerfiles
# @see https://github.com/docker-library/php/tree/master/7.2/stretch/cli/Dockerfile
# ------------------------------------------------------------------------------------
# @build-example docker build . -f Dockerfile -t swoft/swoft
#
FROM php:7.4-fpm

# Libs -y --no-install-recommends
RUN apt-get update \
    && apt-get install -y \
        curl wget git zip unzip less vim procps lsof tcpdump htop openssl net-tools iputils-ping \
        libzip-dev \
        libssl-dev \
        libnghttp2-dev \
        libpcre3-dev \
        libjpeg-dev \
        libpng-dev \
        libfreetype6-dev \
        librabbitmq-dev \
# Install PHP extensions
    && docker-php-ext-install \
       bcmath gd pdo_mysql sockets zip sysvmsg sysvsem sysvshm pcntl \
# Clean apt cache
    && rm -rf /var/lib/apt/lists/*

# Install composer
Run php -r "copy('https://install.phpcomposer.com/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer \
    && composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/ \
# Install redis extension
    && pecl install redis \
    && docker-php-ext-enable redis \
# Install swoole extension
    && pecl install swoole \
    && docker-php-ext-enable swoole \
# Install amqp extension
    && pecl install amqp \
    && docker-php-ext-enable amqp \
