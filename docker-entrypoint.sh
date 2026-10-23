#!/bin/bash
set -e

# Support Render and other cloud hostings dynamic PORT variable
PORT=${PORT:-80}

# Update Apache port listening
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

# Ensure permissions on writable directories
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Clear previous caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run database migrations automatically if DB is connected
if [ -n "$DB_HOST" ]; then
    echo "Running database migrations..."
    php artisan migrate --force --isolated || true
fi

echo "Starting Apache on port ${PORT}..."
exec apache2-foreground
