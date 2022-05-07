FROM php:8.1.5-fpm AS fpm-base

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
			   libpng-dev \
			   libzip-dev \
			   libicu-dev \
			   libzip4 \
	&& pecl install xdebug \
	&& docker-php-ext-install opcache \
    && docker-php-ext-enable xdebug \
	&& docker-php-ext-install pdo_mysql \
	&& docker-php-ext-install exif \
	&& docker-php-ext-install zip \
	&& docker-php-ext-install gd \
	&& docker-php-ext-install intl \
	&& docker-php-ext-install mysqli

FROM fpm-base
# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /srv/app

RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini
RUN echo "date.timezone=Europe/Warsaw" >> /usr/local/etc/php/php.ini

EXPOSE 9000
CMD ["php-fpm"]
