# Nando's Art

Galería de arte de Fernando Delgado: muestra las obras por técnica y tema, cada una con su ficha, y tiene un formulario de contacto que manda un email al artista.

Hecha con Laravel 13, Tailwind CSS 4 y Vite. **No usa base de datos ni cuentas de usuario**: las obras y los textos están en ficheros del repo.

## Instalación

Requisitos: PHP 8.4.1+, Composer y Node 22.12+. La extensión GD con WebP solo hace falta para generar miniaturas.

```bash
composer install
npm ci
cp .env.example .env
php artisan key:generate
npm run build      # o `composer run dev` mientras desarrollas
```

Los estilos no vienen de un CDN: si no se ejecuta `npm run build` (o `npm run dev`), las páginas fallan porque no encuentran `public/build/manifest.json`.

## Cambiar las obras y los textos

Todo está en `resources/contenido/`:

- **`obras.php`**: la lista de obras (título, descripción, técnica, tema, medidas, año, precio, vendida, destacada y fotos). Salen en la galería en el mismo orden. Las destacadas (como mucho 6) salen en la portada. Al principio del fichero se explica cada campo.
- **`paginas.php`**: los textos de «Sobre mí» y «Contacto» (email, teléfono, Instagram y ubicación). El primer párrafo de «Sobre mí» sale también en la portada.

Las fotos de las obras van en `public/obras/` y la foto de «Sobre mí» en `public/images/`.

Después de tocar estos ficheros, `php artisan test` comprueba que todo está bien escrito (que las técnicas existen, que las fotos están, que no hay dos obras con el mismo slug…).

### Miniaturas

```bash
php artisan obras:miniaturas
```

Crea versiones WebP de 400, 800 y 1600 px de cada foto en `public/obras/miniaturas/`, para que el móvil descargue la pequeña. Es opcional: sin miniaturas se usa la foto tal cual.

## Formulario de contacto

Cada mensaje llega por email a `CONTACTO_EMAIL` (o, si está vacío, al email de `paginas.php`), con «Responder» apuntando a quien escribió. No se guarda nada. Se envía en el momento, sin colas: si el correo falla, la persona ve un aviso y no pierde lo que escribió.

Para que se envíe de verdad hay que poner los datos SMTP en `.env`. Con `MAIL_MAILER=log` (lo que viene por defecto) los mensajes solo se escriben en `storage/logs/laravel.log`, que viene bien para probar en local.

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.tu-proveedor.com
MAIL_PORT=587
MAIL_USERNAME=…
MAIL_PASSWORD=…
MAIL_FROM_ADDRESS="web@tu-dominio.com"
CONTACTO_EMAIL=info@fernandodelgadoart.com
```

Hay un límite de 3 mensajes por minuto y 10 por hora desde la misma IP, y un campo oculto que solo rellenan los bots.

## Despliegue en producción

En el `.env` del servidor: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://…`, `MAIL_*` y `CONTACTO_EMAIL`. La sesión (que solo usa el formulario) y la caché van en ficheros de `storage/`, así que esa carpeta tiene que poder escribirse.

En cada despliegue, `./deploy.sh` desde la carpeta del proyecto. Hace estos pasos y, si alguno falla, se para y vuelve a poner la web en marcha:

```bash
php artisan down
git pull
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan optimize               # cachea config, rutas, vistas y eventos
php artisan up
```

Ya no hace falta `migrate`, ni el worker de colas, ni el cron de Laravel.
