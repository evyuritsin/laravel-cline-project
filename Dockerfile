# PHP 8.2 с FPM для Laravel
FROM php:8.2-fpm

# Установка базовых пакетов
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    supervisor

# Очистка кэша
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Установка PHP расширений
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Установка Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Пользователь www-data уже существует в базовом образе php:8.2-fpm
# Проверяем и создаём только если отсутствует
RUN getent group www-data || groupadd -g 1000 www-data

# Создание директорий для Laravel
RUN mkdir -p /var/www/html/storage/logs \
    /var/www/html/storage/framework/cache \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/bootstrap/cache \
    /run/logs

# Установка прав
RUN chown -R www-data:www-data /var/www/html

# Копирование приложения
COPY --chown=www-data:www-data . /var/www/html

# Конфигурация PHP (выполняется от root)
RUN echo "short_open_tag = Off" >> /usr/local/etc/php/conf.d/docker.ini && \
    echo "post_max_size = 100M" >> /usr/local/etc/php/conf.d/docker.ini && \
    echo "upload_max_filesize = 100M" >> /usr/local/etc/php/conf.d/docker.ini

# Установка зависимостей Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Переключение на пользователя www-data
USER www-data

# Рабочая директория
WORKDIR /var/www/html

EXPOSE 9000

CMD ["php-fpm"]