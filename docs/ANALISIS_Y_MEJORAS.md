# Nando's Art: análisis del proyecto y propuestas de mejora

Fecha del análisis: 28/10/2026. Rama analizada: `master` (último commit `ec3c06f`).

La sección 11 registra la evolución del proyecto respecto a este análisis.

Este documento recoge qué conviene **arreglar**, **actualizar**, **mejorar** y qué **funcionalidades faltan**. Está ordenado por prioridad dentro de cada sección. Las referencias a ficheros usan la forma `ruta:línea`.

---

## 0. Resumen rápido

| Prioridad | Tema | Por qué |
|---|---|---|
| 🔴 Crítica | Contraseña real del admin en el seeder | `database/seeders/UserSeeder.php` tiene la contraseña de `info@fernandodelgadoart.com` en claro y está en el historial de Git. Hay que cambiarla ya. |
| 🔴 Crítica | API de usuarios/obras/encargos sin autenticación | Si `routes/api.php` se carga, cualquiera puede listar, editar (incluido `role=admin`) y borrar usuarios. |
| 🔴 Crítica | Rutas que no existen (`shipments.index`, `shipments.destroy`, `shipments.store`) | El menú de un admin y el formulario de presupuesto lanzan error 500. |
| 🔴 Crítica | `User::shipments()` no existe | `/dashboard` falla con error al iniciar sesión. |
| 🟠 Alta | `routes/api.php` probablemente no se carga | La galería pide `GET /api/products?style=...` y esa ruta solo está en `api.php`. |
| 🟠 Alta | Mezcla de 3 sistemas de auth y 3 de CSS | Breeze/Volt + laravel/ui + Livewire; Tailwind CDN + Tailwind 3 + Tailwind 4 + Bootstrap. |
| 🟠 Alta | No hay arquitectura MVI real | Es MVC clásico con lógica en vistas y JS inline. |
| 🟡 Media | Tests rotos o inexistentes | Los tests son los de Breeze y apuntan a rutas que no están registradas. |

---

## 1. Errores que rompen la aplicación (arreglar primero)

1. **Rutas inexistentes**
   - `resources/views/layouts/app.blade.php:700` usa `route('shipments.index')`, que no está definida. Cualquier admin logueado verá un error 500 en **todas** las páginas.
   - `resources/views/shipments/create.blade.php:394` envía a `route('shipments.store')`, pero la ruta se llama `api.shipments.store` (`routes/web.php`).
   - `resources/views/shipments/index.blade.php:553` usa `route('shipments.destroy')`, que tampoco existe.
   - `ShipmentController::store` redirige a `shipments.index` (no existe).
   - Solución: un grupo `Route::middleware(['auth','admin'])->prefix('admin')` con `Route::resource('shipments', ...)`.

2. **Relación que falta en `User`**
   - `DashboardController::index` usa `$user->shipments`, pero `app/Models/User.php` no define `shipments()`. Hay que añadir `public function shipments() { return $this->hasMany(Shipment::class); }`.
   - Además `User` no usa `HasFactory`, y `ShipmentSeeder` y los tests llaman a `User::factory()`, que fallará.

3. **`routes/api.php` no se registra**
   - En Laravel 11/12 las rutas se registran en `bootstrap/app.php` con `withRouting(...)`. Ahí solo están `web`, `commands` y `health`; falta `api: __DIR__.'/../routes/api.php'`.
   - `RouteServiceProvider` (que sí la cargaría) no está en `bootstrap/providers.php`, así que no se ejecuta.
   - Consecuencia: `GET /api/products?style=acuarela` (que usa la galería) da 404/405 y el carrusel sale vacío. Antes de activarla, ver el punto de seguridad 2.

4. **`app/Http/kernel.php` no hace nada**
   - En Laravel 11+ el Kernel ya no existe; el middleware se configura en `bootstrap/app.php` → `withMiddleware()`. Además el fichero está en minúscula (`kernel.php`), lo que en Linux rompe el autoload PSR-4.
   - El alias `admin` funciona gracias a `MiddlewareServiceProvider`. Recomendación: borrar `kernel.php`, `RouteServiceProvider`, `EventServiceProvider`, `AuthServiceProvider` (sin registrar) y `MiddlewareServiceProvider`, y registrar el alias en `bootstrap/app.php`:
     ```php
     ->withMiddleware(function (Middleware $middleware) {
         $middleware->alias(['admin' => \App\Http\Middleware\AdminMiddleware::class]);
     })
     ```

5. **`resources/views/galeria.blade.php` tiene HTML duplicado**
   - Abre `<!DOCTYPE html><html><body>` **y** hace `@extends('layouts.app')`. El script va fuera de cualquier `@section`, así que se imprime fuera del layout. Hay que quitar el HTML exterior y meter el script en `@push('scripts')` / `@section('scripts')`.

6. **`layouts/app.blade.php` no tiene `<meta charset>`, `<meta viewport>`, `<title>` ni `@vite`**. En móvil se ve mal y las tildes pueden romperse.

7. **Datos inconsistentes**
   - `ShipmentSeeder` usa estilos `abstracto`, `realismo`, `impresionismo` que no coinciden con los del formulario (`acuarela`, `rotulador`, `oleo`...).
   - `shipments/index.blade.php` colorea por `Realista`, `Cartoon`, `Animal`, `Fantasia`… valores que nunca existen.
   - `resources/js/galeria.js` usa la clave `óleo` con tilde, mientras que la BD usa `oleo`.
   - `Shipment::product()` es un `belongsTo` pero la tabla `shipments` no tiene `product_id`.
   - `Product` y `Shipment` tienen `softDeletes()` en la migración pero los modelos no usan `SoftDeletes`.

---

## 2. Seguridad

1. **🔴 Contraseña en el repositorio.** `UserSeeder.php` incluye la contraseña real del usuario `nando` y `admin/admin`. Aunque se quite del código, queda en el historial de Git.
   - Cambiar esa contraseña donde se use.
   - Leer credenciales desde `.env` (`ADMIN_EMAIL`, `ADMIN_PASSWORD`) o crear el admin con un comando `php artisan app:create-admin`.
   - Si el repo es o será público, limpiar el historial (`git filter-repo`) o, como mínimo, asumir que la contraseña está comprometida.

2. **🔴 API abierta.** `routes/api.php` expone `apiResource('users')`, `products` y `shipments` sin `auth:sanctum` ni políticas:
   - `UserController::update` hace `$user->update($request->all())`: cualquiera podría poner `role=admin` (mass assignment, porque `role` está en `$fillable`).
   - `UserController::index` devuelve todos los usuarios con email.
   - `POST /api/upload-image` permite subir ficheros sin login.
   - `ProductController` y `ShipmentController` no tienen `show`/`update`, así que `apiResource` apuntará a métodos inexistentes.
   - Solución: dejar en la API pública solo `GET /api/products` (lectura), y proteger el resto con `auth:sanctum` + `admin`, usando `FormRequest` y `Policies`.

3. **Quitar `role` de `$fillable`** en `User` y asignarlo explícitamente (`$user->role = 'admin'`).

4. **Encargos/presupuestos (`ShipmentController::store`)**: el usuario escribe a mano su `user_id`, así que puede crear encargos a nombre de otro. Debe usarse `auth()->id()`. Además el formulario envía `imagen` pero el controlador la ignora.

5. **Subida de imágenes**
   - `ProductController::store` guarda con `getClientOriginalName()` en `public/storage/obras`. Dos obras con el mismo nombre de archivo comparten imagen, y el nombre lo controla el cliente. Usar `$file->store('obras', 'public')` (nombre aleatorio) y `php artisan storage:link`.
   - `ImageUploadController` acepta `svg`, que puede contener JavaScript (XSS). Quitar `svg` o sanearlo.

6. **XSS en JavaScript**: `galeria.blade.php` y `public/js/frase.js` pintan datos con `innerHTML` y template strings (`${producto.name}`, `${data.phrase}`). Si un nombre o una frase contiene HTML, se ejecuta. Usar `textContent` / `createElement`, o escapar.

7. **`/frase-proxy`** no tiene caché ni timeout ni manejo de error: si el servicio externo cae o tarda, la petición se queda colgada y cada visita a la home hace una llamada saliente. Usar `Http::timeout(3)->get(...)` dentro de `Cache::remember('frase-del-dia', now()->endOfDay(), ...)`.

8. **Rate limiting**: el login de laravel/ui tiene throttle, pero el formulario de presupuesto no. Añadir `throttle:5,1` a `POST /presupuesto`.

9. `HomeController` usa `$this->middleware('auth')` en el constructor, que ya no existe en el `Controller` base de Laravel 11+. Mover el middleware a la ruta.

---

## 3. Actualizaciones de dependencias

Versiones bloqueadas hoy en `composer.lock` / `package-lock.json`:

| Paquete | Versión actual | Recomendación |
|---|---|---|
| `laravel/framework` | 12.3.0 | Como mínimo `composer update` a la última 12.x (parches de seguridad). Valorar Laravel 13 cuando el resto esté estable. |
| `livewire/livewire` | 3.6.2 | Actualizar a la última 3.x; valorar 4.x si se mantiene Livewire. |
| `livewire/volt` | 1.7.0 | Actualizar o **eliminar** si se quita Breeze/Volt. |
| `laravel/ui` | 4.6.1 | **Eliminar** (duplica la autenticación de Breeze). |
| `doctrine/dbal` | 4.2.3 | **Eliminar**: desde Laravel 11 no hace falta para modificar columnas y no se usa. |
| `laravel/breeze` | 2.3 (dev) | Se puede quitar después de instalar (solo es un instalador). |
| `pestphp/pest` | 3.7.4 | Actualizar a la última 3.x o a Pest 4. |
| `vite` | 6.2.2 | Actualizar a Vite 7 junto con `laravel-vite-plugin` 2.x. |
| `tailwindcss` | 3.4.17 **y** `@tailwindcss/vite` 4 **y** CDN | Elegir **una**: Tailwind 4 con `@tailwindcss/vite`, quitar `postcss.config.js`, `tailwind.config.js`, `autoprefixer` y el `<script src="cdn.tailwindcss.com">` (el CDN no es para producción). |
| `bootstrap`, `@popperjs/core`, `sass` | 5.3 / 2.11 / 1.x | **Eliminar**: la interfaz está hecha con Tailwind; Bootstrap solo añade peso y conflictos de estilos. |
| `axios` | 1.8.3 | Actualizar (ha tenido avisos de seguridad) o quitarlo y usar `fetch`, que ya se usa. |

Otros puntos:
- `vite.config.js` compila `resources/sass/app.scss` (Bootstrap) pero no `resources/css/app.css` (Tailwind), y el layout no usa `@vite`, así que **nada de Vite llega a las páginas**. Hoy todo el estilo viene del CDN.
- `composer.json` sigue llamándose `laravel/laravel` con la descripción del esqueleto. Cambiar `name`, `description`.
- `.env.example`: `APP_NAME=Laravel`, `APP_LOCALE=en`. Poner `APP_NAME="Nando's Art"`, `APP_LOCALE=es`, `APP_FAKER_LOCALE=es_ES`.
- Ejecutar `composer audit` y `npm audit` en CI.

---

## 4. Arquitectura: hacia un MVI real

Ahora mismo el proyecto es **MVC clásico**: controladores que devuelven vistas Blade, lógica en las vistas (`@php switch(...)`) y un JS inline en la galería que mezcla estado, peticiones y pintado. No hay ninguna capa de Intents, Model (estado) ni View separados.

### Qué significa MVI aquí
- **Intent**: lo que el usuario quiere hacer (`FiltrarPorEstilo`, `SiguienteObra`, `SolicitarPresupuesto`).
- **Model / State**: un único objeto de estado inmutable (`{ estilo, obras, indice, cargando, error }`).
- **Reducer**: función pura `(estado, intent) => nuevoEstado`.
- **View**: función que pinta el estado; nunca modifica nada directamente.
- Flujo unidireccional: `View → Intent → (efecto/API) → Reducer → State → View`.

### Propuesta concreta

**Frontend (JS), donde MVI encaja mejor:**
```
resources/js/galeria/
  intents.js     // constantes y creadores de intents
  state.js       // estado inicial
  reducer.js     // (state, intent) => state, puro y testeable
  effects.js     // llamadas a /api/products, devuelven nuevos intents
  view.js        // render(state) con createElement / textContent
  store.js       // dispatch, subscribe (o usar Alpine store)
  index.js       // conecta los botones con dispatch()
```
- Sacar todo el `<script>` de `galeria.blade.php` a estos módulos y cargarlo con `@vite('resources/js/galeria/index.js')`.
- El reducer se puede testear con **Vitest** sin navegador.

**Backend (Laravel), MVI aplicado al servidor:**
- **Intents** → `FormRequest` (`StoreProductRequest`, `StoreShipmentRequest`) que validan y autorizan.
- **Acciones** → clases `App\Actions\CreateProduct`, `RequestQuote`… con la lógica (subir imagen, crear registro).
- **State** → `API Resources` (`ProductResource`) que dan forma estable a lo que recibe el front.
- **Enums** PHP para `Style`, `Category`, `ShipmentStatus`, `Role` (hoy están repetidos como strings en migración, vistas, JS y seeders).
- **Policies** para `Product` y `Shipment` en vez de comprobar `role === 'admin'` en vistas.

**Alternativa**: Livewire ya está instalado y su modelo (estado público + acciones + render) es muy parecido a MVI. Hacer la galería como componente Livewire (`ShipmentComponent` está vacío) es otra opción coherente. Lo importante es **elegir una**: o JS MVI + API, o Livewire, no las dos a medias.

### Limpieza de código
- Quitar código muerto: `ShipmentComponent` vacío, `resources/js/galeria.js` (duplica lo de la vista y no se carga), `home.blade.php`, `welcome.blade.php`, `muestras.blade.php`, `create.blade.php` de raíz, vistas Volt de auth no usadas, `routes/auth.php` (no se incluye en ningún sitio).
- Quitar comentarios `{{-- filepath: c:\Users\ragna\... --}}` con rutas locales.
- Unificar idioma de los nombres: `Shipment` en código y "presupuesto/encargo" en la UI. Renombrar a `QuoteRequest` o `Encargo`.
- Pasar `Pint` (`./vendor/bin/pint`) para unificar el formato (hay indentación irregular en `ProductController`, `routes/web.php`, `Product.php`).
- El footer está copiado en `landing` y `galeria`: moverlo al layout o a un componente `<x-footer>`.
- Las listas de estilos/categorías se repiten en 6 sitios; con un `enum` se generan desde un solo lugar.

---

## 5. Rendimiento

- **Tailwind por CDN** compila el CSS en el navegador en cada visita: lento y no recomendado en producción. Pasar a Vite.
- **Imágenes**: el carrusel carga la imagen original. Generar miniaturas (p. ej. con `intervention/image` o `spatie/laravel-medialibrary`) en WebP/AVIF, añadir `loading="lazy"`, `width`/`height` y `srcset`.
- **`/api/products`** devuelve todas las obras de un estilo sin paginar; añadir `->paginate()` o `->select(...)` y cache (`Cache::remember`) invalidada al crear una obra.
- **Índices**: añadir índice en `products.style` y `products.category` (son los filtros).
- **`ShipmentController::index`** carga `User::all()` y `Shipment::all()`: paginar y usar `with('user')`.
- **Carrusel**: re-crea todo el DOM en cada clic y usa anchos fijos (`w-[1150px]`, cards de 400px). Mejor actualizar clases y usar `transform`.
- En producción: `php artisan optimize`, `config:cache`, `route:cache`, `view:cache`, OPcache.

---

## 6. UX, diseño y accesibilidad

- **Responsive**: el carrusel tiene `w-[1150px]` y tarjetas de 400px fijas; en móvil se desborda. El layout no tiene `meta viewport` ni menú hamburguesa.
- **Galería**: no hay vista de detalle de una obra, ni zoom/lightbox, ni ir al carrusel con teclado (flechas ← →) o gestos de swipe.
- **Accesibilidad**: los botones ◀ ▶ no tienen `aria-label`; los botones de filtro no indican `aria-pressed`; textos de `alt` genéricos ("Obra 1"); contraste bajo en colores pastel (`text-pink-400` sobre blanco).
- **Feedback**: no hay estado de carga ni mensaje cuando un estilo no tiene obras; los `with('success', ...)` no se muestran en ningún layout; los formularios no pintan errores de validación (`@error`) ni conservan los valores (`old()`).
- **Formulario de presupuesto**: pide "ID de Usuario" al cliente (debe salir de la sesión), y la palabra "Shipment" aparece en la UI en inglés.
- **Dashboard** dice "Bienvenido al gestor de tareas" (texto de plantilla) y no muestra los encargos que se le pasan.
- **SEO**: sin `<title>` por página, sin `meta description`, sin Open Graph para compartir obras en redes, sin `sitemap.xml`.
- **Idioma**: `APP_LOCALE=en`, así que los mensajes de validación y emails salen en inglés. Instalar `laravel-lang/common` y poner `es`.
- Enlace de Instagram con `target="_blank"` sin `rel="noopener noreferrer"`.

---

## 7. Tests y calidad

Estado actual: solo existen los tests de ejemplo de Breeze (`tests/Feature/Auth/*`, `ProfileTest`). Apuntan a componentes Volt cuyas rutas (`routes/auth.php`) no se cargan, y `User` no tiene `HasFactory`, así que la mayoría fallarán.

Propuesta:
- `UserFactory` funcionando y `ProductFactory`, `ShipmentFactory`.
- Activar SQLite en memoria en `phpunit.xml` (descomentar `DB_CONNECTION`/`DB_DATABASE`).
- Tests de Feature (Pest) mínimos:
  - La galería pública responde 200.
  - `GET /api/products?style=acuarela` devuelve solo obras de ese estilo.
  - Un usuario normal recibe 403 en `/galeria/nuevo` y en `POST` de obras.
  - Un admin puede crear una obra con imagen (`Storage::fake('public')`).
  - Un usuario logueado crea un presupuesto **a su nombre** aunque envíe otro `user_id`.
  - La API de usuarios no es accesible sin autenticación.
- Tests de JS con **Vitest** para el reducer MVI.
- CI (`.github/workflows/tests.yml`): hoy corre en `master`, pero no compila assets ni prepara la base de datos. Añadir `npm ci && npm run build`, `pint --test`, `composer audit`.
- Borrar los workflows heredados de Laravel (`issues.yml`, `pull-requests.yml`, `update-changelog.yml`) y el `CHANGELOG.md` del esqueleto: no aplican a este proyecto.
- Análisis estático con **Larastan** (nivel 5 para empezar).

---

## 8. Funcionalidades que faltan

**Imprescindibles para la galería**
1. **CRUD completo de obras en el panel admin**: hoy solo se pueden crear; faltan listar, editar, borrar (soft delete) y reordenar.
2. **Página de detalle de obra** (`/obras/{slug}`) con imágenes grandes, medidas, técnica, año, precio o "consultar".
3. **Filtros combinados**: estilo + categoría + búsqueda por nombre, con la URL reflejando el filtro (para compartir).
4. **Gestión de encargos para el admin**: listado, cambiar estado (`pending → reviewed → completed`), responder al cliente, adjuntar presupuesto.
5. **Notificaciones por email**: al artista cuando llega un presupuesto y al cliente cuando cambia el estado (Laravel Notifications + cola).
6. **Panel del cliente**: ver sus presupuestos y su estado (el dashboard ya recibe `$shipments` pero no los muestra).
7. **Subida de imagen de referencia** en el presupuesto (el campo existe en el formulario y la BD, pero no se guarda).

**Muy recomendables**
8. Varias imágenes por obra (detalles, proceso) con tabla `product_images`.
9. Campos extra en obras: `slug`, `year`, `price`, `available/vendida`, `featured`, `sort_order`.
10. Página de contacto y "Sobre mí" editable desde el admin (la bio de la landing está escrita en la vista).
11. Lightbox / zoom y modo pantalla completa.
12. Internacionalización ES/EN si hay público de fuera.
13. Página 404/500 personalizadas con el estilo de la web.
14. Favoritos o "me gusta" para usuarios registrados.

**Opcionales / a futuro**
15. Tienda: venta de originales o láminas (Stripe / Laravel Cashier).
16. Blog o "proceso creativo" con entradas.
17. Integración con Instagram para mostrar las últimas publicaciones.
18. Newsletter.
19. Estadísticas simples en el admin (obras más vistas, presupuestos por mes).
20. Despliegue documentado (Forge, Ploi, Docker/Sail) y copias de seguridad de BD e imágenes.

---

## 9. Documentación

- `README.md` solo tiene un título. Añadir: qué es el proyecto, requisitos (PHP 8.2+, Node 20+, MySQL), instalación (`composer install`, `npm ci`, `cp .env.example .env`, `php artisan key:generate`, `migrate --seed`, `storage:link`, `npm run dev`), cómo crear el admin, y una sección explicando la arquitectura MVI elegida con un diagrama del flujo.

---

## 10. Plan sugerido por fases

1. **Fase 1, que funcione y sea seguro (1–2 días)**: cambiar la contraseña filtrada, arreglar rutas y relación `shipments()`, registrar/proteger la API, `user_id` desde la sesión, subida de imágenes segura, `HasFactory` en `User`.
2. **Fase 2, limpieza (1–2 días)**: quitar laravel/ui o Breeze (dejar uno), quitar Bootstrap/Sass/CDN y dejar Tailwind 4 con Vite, borrar Kernel y providers muertos, enums, Pint, actualizar dependencias.
3. **Fase 3, MVI y tests (2–4 días)**: galería en módulos MVI con reducer testeado, FormRequests/Actions/Resources/Policies en backend, tests Pest y CI.
4. **Fase 4, funcionalidades**: CRUD admin, detalle de obra, gestión de encargos con emails, panel del cliente, SEO y responsive.

---

## 11. Evolución del proyecto

Esta sección se actualiza cada vez que se cierra un bloque de tareas, comparando el estado de `master` con lo que pedía este informe.

Leyenda: ✅ hecho · 🟡 a medias · ❌ pendiente.

### Revisión 1 (28/10/2026), tras la fase 1: PRs #2, #3 y #4

**Resumen:** la web ya no se rompe en los flujos principales (galería, presupuesto, dashboard y panel de admin) y se han cerrado los dos agujeros de seguridad más graves. La fase 1 está hecha en un 80 % aproximadamente. Las fases 2, 3 y 4 no se han empezado.

#### Qué se ha resuelto

| Punto del informe | Estado | Cómo se resolvió |
|---|---|---|
| 1.1 Rutas inexistentes (`shipments.index`, `.store`, `.destroy`) | ✅ | Rutas nuevas en `routes/web.php`: `/presupuesto` (auth) y `/admin/shipments` (auth + admin). El redirect de `store` va a `dashboard` o a `shipments.index` según el rol (#4). |
| 1.2 `User::shipments()` y `HasFactory` | ✅ | Añadidos en `app/Models/User.php` (#4). |
| 1.3 `routes/api.php` no se cargaba | ✅ | `api:` registrado en `bootstrap/app.php`; `ProductController::index` filtra por `?style=`. El carrusel ya recibe obras (#4). |
| 2.1 Contraseña real en `UserSeeder` | 🟡 | Se lee de `.env` (`ADMIN_PASSWORD` mediante `config/admin.php`); si no existe, se genera una aleatoria. El usuario `admin/admin` solo se crea en local (#2). **Falta:** cambiar la contraseña antigua, que sigue en el historial de Git. |
| 2.2 API abierta | 🟡 | `users` exige sesión (`web` + `auth`) y `UserPolicy` controla ver, editar, cambiar el rol y borrar. `products` es solo lectura. `shipments` ya no está en la API (#3, #4). **Falta:** `POST /api/upload-image` sigue sin login y acepta SVG, y `/api/test` sigue expuesta. |
| 2.3 `role` en `$fillable` | 🟡 | El controlador ya no usa `$request->all()` y asigna el rol aparte, pero `role` sigue en `$fillable` de `User`. |
| 2.4 `user_id` escrito a mano en el presupuesto | ✅ | Se toma de `$request->user()` y se ha quitado el campo del formulario (#4). |
| 2.4 Imagen del presupuesto ignorada | ✅ | Se guarda en `storage/app/public/presupuestos` con nombre aleatorio (#4). |
| 6 Dashboard con texto de plantilla | ✅ | Ahora es "Mis presupuestos" y lista los encargos del usuario con su estado (#4). |
| 6 Mensajes `success` y errores de validación | 🟡 | Se muestran en el dashboard y en el formulario de presupuesto. Faltan en la galería y en el formulario de obra. |
| 7 Tests | 🟡 | Hay 13 tests nuevos en `tests/Feature/PaginasRotasTest.php` y `UserApiTest.php` (rutas, permisos, API y carrusel). Los tests heredados de Breeze/Volt (`tests/Feature/Auth`, `ProfileTest`) siguen fallando. |

#### Cómo ha evolucionado la web

- **Antes:** un admin logueado veía un error 500 en todas las páginas, `/dashboard` fallaba, el formulario de presupuesto no enviaba, el carrusel salía vacío y cualquiera podía hacerse admin a través de la API.
- **Ahora:** un visitante ve la galería con obras filtradas por estilo. Un cliente puede pedir un presupuesto con imagen y seguir su estado en "Mis presupuestos". El admin ve y borra presupuestos, y la API de usuarios respeta los permisos.

#### Qué falta (por prioridad)

**Fase 1, lo que queda:**
1. 🔴 Cambiar la contraseña antigua del usuario `nando` en cualquier sitio donde se use (sigue en el historial de Git).
2. 🔴 Proteger `POST /api/upload-image` (auth + admin) y quitar `svg` de los tipos permitidos. Borrar `/api/test`.
3. 🟠 `ProductController::store` sigue guardando la imagen con el nombre original en `public/storage/obras`; cambiar a `store('obras', 'public')`.
4. 🟠 XSS en `innerHTML` (galería y `frase.js`) sin tocar.
5. 🟡 Quitar `role` de `$fillable`.
6. 🟡 Caché y timeout en `/frase-proxy`; `throttle` en `POST /presupuesto`.
7. 🟡 Actualizar el comentario de `tests/Feature/UserApiTest.php` que dice que `api.php` "todavía no se registra" (ya se registra).

**Sin cambios desde el análisis (fases 2 a 4):**
- `galeria.blade.php` con HTML duplicado y script fuera del layout; layout sin `meta viewport`, `charset` ni `title` (punto 1.5 y 1.6).
- Kernel y providers muertos, `HomeController` con `$this->middleware()` (1.4, 2.9).
- Datos inconsistentes entre seeders, vistas y JS; `SoftDeletes` sin usar; `Shipment::product()` sin columna (1.7).
- Mezcla de laravel/ui + Breeze/Volt y de Tailwind CDN + Tailwind 3/4 + Bootstrap; dependencias sin actualizar (punto 3).
- Arquitectura MVI: todavía no hay intents, reducer ni vista separados (punto 4).
- Rendimiento, responsive, accesibilidad y SEO (puntos 5 y 6).
- CI: no compila assets ni ejecuta Pint (punto 7).
- Funcionalidades nuevas: CRUD completo de obras, detalle de obra, cambio de estado de presupuestos, emails… (punto 8).
- README (punto 9).

**Siguiente paso recomendado (en su momento):** cerrar los puntos 1 y 2 de la lista anterior (seguridad), y después empezar la fase 2 (limpieza de dependencias y CSS), que es la base para hacer el MVI.

### Revisión 2 (28/10/2026), cierre de la fase 1

**Resumen:** la fase 1 está **terminada**. David ha cambiado la contraseña antigua y se han cerrado los puntos de seguridad que quedaban. La web se comporta igual para el visitante, pero ya no se puede subir nada sin ser admin ni inyectar HTML en la galería.

| Pendiente de la revisión 1 | Estado | Cómo se resolvió |
|---|---|---|
| Cambiar la contraseña antigua del admin | ✅ | La ha cambiado David. |
| `POST /api/upload-image` sin login y con SVG | ✅ | Ahora exige sesión y rol admin (`web` + `auth` + `admin`). Se quita `svg`, se permite `webp` y el nombre del fichero es aleatorio. |
| `/api/test` expuesta | ✅ | Eliminada. |
| Imagen de obra con el nombre original | ✅ | `ProductController::store` usa `store('obras', 'public')` con un nombre aleatorio. Dos obras con el mismo nombre de archivo ya no se pisan. Además, el estilo y la categoría se validan contra los valores de la base de datos. |
| XSS por `innerHTML` | ✅ | Las tarjetas de la galería y la frase del día se construyen con `textContent`. |
| `role` en `$fillable` | ✅ | Eliminado; el rol solo se asigna a mano donde `UserPolicy` lo permite. |
| `/frase-proxy` sin caché ni timeout | ✅ | Timeout de 3 s, caché hasta medianoche, respuesta 503 si el servicio cae y `throttle:60,1`. |
| `POST /presupuesto` sin límite | ✅ | `throttle:5,1` (5 envíos por minuto). |
| Comentario obsoleto en `UserApiTest` | ✅ | Eliminado junto con el código que cargaba `api.php` a mano. |

**Extras arreglados de paso:**
- `galeria.blade.php` ya no repite `<html>`/`<body>`: el script va en `@section('scripts')` dentro del layout (punto 1.5).
- El layout tiene `charset`, `viewport`, `csrf-token` y `<title>` (punto 1.6).
- La galería muestra "Todavía no hay obras de este estilo" en vez de romperse con un estilo vacío.

**Tests:** 10 tests nuevos en `tests/Feature/Fase1SeguridadTest.php`. Pasan 39 tests en total. Los 10 que fallan son los heredados de Breeze/Volt (`tests/Feature/Auth`, `ProfileTest`), igual que antes; se resolverán en la fase 2 al quedarse con un solo sistema de login.

**Importante para producción:** las imágenes nuevas se guardan en `storage/app/public/obras`, así que el servidor necesita `php artisan storage:link` (lo mismo que ya pedían las imágenes de presupuestos).

#### Qué falta ahora

La fase 1 está cerrada. El siguiente bloque es la **fase 2 (limpieza)**:
1. Quedarse con un solo sistema de login (laravel/ui o Breeze/Volt) y arreglar o borrar los 10 tests heredados.
2. Tailwind 4 con Vite; quitar el CDN, Bootstrap y Sass.
3. Borrar `kernel.php` y los providers muertos.
4. Enums para estilo, categoría, estado y rol, y unificar los datos de seeders y vistas.
5. Actualizar dependencias y pasar Pint.

Después vendrán la fase 3 (MVI y CI) y la fase 4 (funcionalidades), sin cambios desde la revisión 1.
