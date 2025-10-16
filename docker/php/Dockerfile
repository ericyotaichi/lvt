FROM php:8.3-fpm-alpine

# 基礎套件 + PHP 擴充 + DB client（容器內可測 DB）
RUN apk add --no-cache bash git curl icu-dev libzip-dev zlib-dev oniguruma-dev \
    libpng-dev libjpeg-turbo-dev freetype-dev gmp-dev mariadb-client

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif pcntl bcmath gd intl zip

# Composer（安裝到 PATH）
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN php -r "copy('https://getcomposer.org/installer','composer-setup.php');" \
 && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
 && rm composer-setup.php \
 && composer --version

# 放進 PHP 掛載目錄，會自動被載入
COPY conf.d/*.ini /usr/local/etc/php/conf.d/

WORKDIR /var/www/html
CMD ["php-fpm"]
