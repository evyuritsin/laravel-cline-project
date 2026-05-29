#!/bin/bash

set -e

echo ">>> Инициализация Laravel приложения..."

# Создание директорий storage если они не существуют
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

# Установка прав на директории
chown -R www-data:www-data storage bootstrap/cache

# Генерация APP_KEY если не установлен
if [ -z "$(grep '^APP_KEY=' .env 2>/dev/null)" ]; then
    echo ">>> Генерация APP_KEY..."
    php artisan key:generate
else
    echo ">>> APP_KEY уже установлен"
fi

# Запуск миграций
echo ">>> Запуск миграций базы данных..."
php artisan migrate --force || echo ">>> Миграции уже выполнены или требуют настройки"

# Очистка и прогрев кэша
echo ">>> Очистка кэша..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Создание символической ссылки для storage
if [ ! -L public/storage ]; then
    echo ">>> Создание символической ссылки storage..."
    php artisan storage:link || true
fi

echo ">>> Инициализация завершена!"

# Запуск PHP-FPM
exec php-fpm