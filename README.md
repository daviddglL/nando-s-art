# Nando's Art

Galería de arte de Fernando Delgado: muestra las obras por estilo y permite a los clientes pedir presupuestos.

Hecha con Laravel 13, Tailwind CSS 4 y Vite. El login es el de `laravel/ui` (rutas de `Auth::routes()`).

## Instalación

Requisitos: PHP 8.4.1+ con la extensión GD (con soporte WebP, para las miniaturas), Composer, Node 22.12+ y MySQL (o SQLite para pruebas).

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

`migrate --seed` crea el admin (con los datos de `ADMIN_*` en `.env`) y, salvo en producción, 6 obras y 5 presupuestos de ejemplo. Las imágenes de esas obras están en `database/seeders/imagenes/` y el seeder las copia a `storage/app/public/obras`. Son imágenes de relleno: si sustituyes esos ficheros por las fotos reales (con el mismo nombre), una instalación nueva ya sale con ellas.

### Imágenes y miniaturas

Al subir o cambiar la imagen de una obra se generan versiones WebP de 400, 800 y 1600 px de ancho en `storage/app/public/obras/miniaturas/` (nunca más anchas que la original). La galería, la ficha y el admin las usan con `srcset`, así que el móvil descarga la pequeña; el lightbox sigue abriendo la original.

Cada obra tiene una imagen principal (la de la galería) y hasta 12 adicionales (detalles, otros ángulos...), que se suben, ordenan, borran o se convierten en la principal en la sección "Más imágenes" de su pantalla de edición. Salen en la ficha como miniaturas y en el visor, que pasa de una a otra con flechas, teclado o deslizando el dedo. Tienen sus propias miniaturas WebP y se borran con la obra al eliminarla definitivamente.

Para las obras que ya existían (o si se borra esa carpeta):

```bash
php artisan obras:miniaturas
```

Si el servidor no tiene GD con WebP, las obras se ven igual con la imagen original.

### Cuentas de cliente

Al registrarse se envía un email de verificación, y hasta abrir el enlace no se pueden pedir presupuestos ni ver "Mis presupuestos". El registro admite como mucho 5 cuentas por hora desde la misma IP y tiene un campo oculto que solo rellenan los bots. Las cuentas que ya existían se dieron por verificadas al migrar.

### Emails

Al llegar un presupuesto se avisa por email a los usuarios admin, y al responderlo se avisa al cliente. Los emails van a una cola (`QUEUE_CONNECTION=database`), así que en el servidor tiene que estar corriendo un worker:

```bash
php artisan queue:work --tries=3
```

Configura también `MAIL_*` en `.env` (por defecto `MAIL_MAILER=log` solo los escribe en `storage/logs`).

## Despliegue en producción

En el `.env` del servidor: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://…`, `DB_*`, `MAIL_*`, `ADMIN_*` y `QUEUE_CONNECTION=database`.

En cada despliegue, `./deploy.sh` desde la carpeta del proyecto. Hace estos pasos y, si alguno falla, se para y vuelve a poner la web en marcha:

```bash
php artisan down
git pull
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan storage:link           # solo la primera vez
php artisan optimize               # cachea config, rutas, vistas y eventos
php artisan queue:restart          # el worker recoge el código nuevo
php artisan up
```
