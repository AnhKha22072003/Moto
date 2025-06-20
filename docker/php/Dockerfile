# FROM php:8.2-fpm

# # Install system dependencies
# RUN apt-get update && apt-get install -y \
#     build-essential \
#     libpng-dev \
#     libjpeg-dev \
#     libonig-dev \
#     libxml2-dev \
#     zip \
#     unzip \
#     curl \
#     git \
#     libzip-dev \
#     libssl-dev \
#     openssl

# # Install PHP extensions
# RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath

# # Install Composer
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
FROM shinsenter/laravel:php8.3

EXPOSE 80 443