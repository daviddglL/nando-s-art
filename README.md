# Nando's Art

Galería de arte de Fernando Delgado: muestra las obras por estilo y permite a los clientes pedir presupuestos.

Hecha con Laravel 12, Tailwind CSS 4 y Vite. El login es el de `laravel/ui` (rutas de `Auth::routes()`).

## Instalación

Requisitos: PHP 8.2+, Composer, Node 20+ y MySQL (o SQLite para pruebas).

```bash
composer install
npm ci
cp .env.example .env
php artisan key:generate
# Rellena DB_* y ADMIN_PASSWORD en .env
php artisan migrate --seed
php artisan storage:link
npm run build      # o `npm run dev` mientras desarrollas
```

Los estilos ya no vienen de un CDN: si no se ejecuta `npm run build` (o `npm run dev`), las páginas fallan porque no encuentran `public/build/manifest.json`.

## Tests

```bash
php artisan test
./vendor/bin/pint --test
```

## Documentación

El análisis del proyecto, el plan por fases y la evolución están en [`docs/ANALISIS_Y_MEJORAS.md`](docs/ANALISIS_Y_MEJORAS.md).
