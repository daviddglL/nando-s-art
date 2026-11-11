#!/usr/bin/env bash
# Despliegue de Nando's Art en el servidor. Uso: ./deploy.sh (desde la carpeta del proyecto)
# Pone la web en mantenimiento, actualiza el código y las dependencias, migra y la vuelve a poner en marcha.
# Si un paso falla, el script se para y la web vuelve a estar en línea (con el código que haya quedado).
set -euo pipefail

cd "$(dirname "$0")"

echo "→ Modo mantenimiento"
php artisan down --retry=30
trap 'echo "✗ Algo ha fallado: la web vuelve a estar en línea"; php artisan up' ERR

echo "→ Código"
git pull --ff-only

echo "→ Dependencias de PHP"
composer install --no-dev --optimize-autoloader --no-interaction

echo "→ CSS y JS"
npm ci
npm run build

echo "→ Base de datos"
php artisan migrate --force

# El enlace public/storage solo hace falta crearlo la primera vez
if [ ! -e public/storage ]; then
    php artisan storage:link
fi

echo "→ Cachés"
php artisan optimize

echo "→ Worker de colas"
php artisan queue:restart

php artisan up
trap - ERR
echo "✓ Desplegado"
