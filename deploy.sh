#!/bin/bash
cd /var/www/restafull

echo "📦 Actualizando código..."
git pull origin main

echo "📚 Instalando dependencias..."
composer install --no-dev --optimize-autoloader

echo "🎨 Compilando assets..."
npm install
npm run build

echo "🔧 Limpiando caché..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "🗄️ Migrando base de datos..."
php artisan migrate --force

echo "🔒 Permisos..."
chown -R www-data:www-data /var/www/restafull
chmod -R 775 /var/www/restafull/storage

echo "🔄 Reiniciando servicios..."
systemctl restart php8.5-fpm
systemctl restart reverb

echo "✅ Deploy completado!"
