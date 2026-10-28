# Nando's Art

Galería de arte de Fernando Delgado: muestra las obras por estilo y permite a los clientes pedir presupuestos.

Hecha con Laravel 12, Tailwind CSS 4 y Vite. El login es el de `laravel/ui` (rutas de `Auth::routes()`).

## Instalación

Requisitos: PHP 8.2+ con la extensión GD (con soporte WebP, para las miniaturas), Composer, Node 20+ y MySQL (o SQLite para pruebas).

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

En cada despliegue:

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

`php artisan optimize` congela la configuración: tras cambiar el `.env` hay que volver a ejecutarlo (o `php artisan optimize:clear`).

**OPcache.** Actívalo en el `php.ini` de producción; es lo que más acelera PHP:

```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0   ; con 0 hay que recargar PHP-FPM en cada despliegue
```

**Worker de colas** (los emails van en cola). Con Supervisor, en `/etc/supervisor/conf.d/nandos-art.conf`:

```ini
[program:nandos-art-worker]
command=php /ruta/a/nandos-art/artisan queue:work --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
stopwaitsecs=60
```

**Cabeceras de seguridad.** Las pone la propia aplicación (`App\Http\Middleware\CabecerasSeguridad`): `Content-Security-Policy` (solo scripts propios, sin JS en línea), `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy` y, por HTTPS, `Strict-Transport-Security`. Si se añade un script o una imagen de otro dominio, hay que permitirlo en la CSP de ese middleware. Detrás de un proxy o balanceador con HTTPS, configura los proxies de confianza para que Laravel sepa que la petición es segura.

**Copias de seguridad.** Hay que guardar la base de datos y `storage/app` (las imágenes de las obras están en `storage/app/public` y las de los presupuestos, privadas, en `storage/app/private`). Por ejemplo, cada noche con cron:

```bash
mysqldump --single-transaction nandos_art | gzip > /backups/db-$(date +%F).sql.gz
tar -czf /backups/storage-$(date +%F).tar.gz -C /ruta/a/nandos-art storage/app
find /backups -mtime +30 -delete
```

Y copia `/backups` fuera del servidor.

## Qué puede hacer cada uno

- **Visitantes**: galería por estilos, ficha de cada obra en `/obras/{slug}` (con imagen a pantalla completa) y `sitemap.xml`.
- **Clientes**: pedir presupuestos (con imagen de referencia, que es privada: solo la ven el cliente y el admin) y ver en "Mis presupuestos" el estado, la respuesta y el precio.
- **Admin** (`/admin`): crear, editar, ordenar, borrar (papelera) y restaurar obras en `/admin/obras`; responder presupuestos y cambiar su estado en `/admin/encargos`.

## Arquitectura (MVI)

El flujo va siempre en un sentido: **vista → intent → reducer → estado → vista**.

**Galería (JS, `resources/js/galeria/`)**

| Fichero | Papel |
|---|---|
| `intents.js` | Lo que puede pasar: elegir estilo, obras cargadas, error, siguiente/anterior obra. |
| `state.js` | Estado inicial (`estilo`, `obras`, `indice`, `cargando`, `error`) y qué tarjetas se ven. |
| `reducer.js` | Función pura `(estado, intent) => nuevo estado`. |
| `effects.js` | Llama a `/api/products` y devuelve el resultado como otro intent. |
| `store.js` | Guarda el estado, aplica el reducer y avisa a la vista. |
| `view.js` | Pinta el estado (siempre con `textContent`, nunca `innerHTML`) y enlaza cada tarjeta con su ficha. |
| `swipe.js` | Traduce el gesto de deslizar en móvil a siguiente/anterior obra. |
| `index.js` | Conecta botones, flechas del teclado y swipe con `dispatch()`. |

**Servidor (Laravel)**

- Intents: `FormRequest` en `app/Http/Requests` (validan y comprueban permisos).
- Acciones: `app/Actions` (`CreateProduct`, `UpdateProduct`, `MoverObra`, `SolicitarPresupuesto`, `ResponderEncargo`).
- Avisos por email: `app/Notifications` (`NuevoPresupuesto`, `PresupuestoActualizado`).
- Estado para el front: `app/Http/Resources/ProductResource`.
- Permisos: `app/Policies` (`ProductPolicy`, `EncargoPolicy`, `UserPolicy`).

Un **encargo** (`App\Models\Encargo`) es una petición de presupuesto de un cliente. Los mensajes de validación están en español en `lang/es`.

## Tests y calidad

```bash
php artisan test                 # Pest
npm test                         # Vitest (galería MVI)
./vendor/bin/pint --test         # formato
./vendor/bin/phpstan analyse     # Larastan, nivel 5
```

La CI (`.github/workflows/tests.yml`) ejecuta todo esto, compila los assets y pasa `composer audit` y `npm audit`.
