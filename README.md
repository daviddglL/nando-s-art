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
| `view.js` | Pinta el estado (siempre con `textContent`, nunca `innerHTML`). |
| `index.js` | Conecta botones y flechas del teclado con `dispatch()`. |

**Servidor (Laravel)**

- Intents: `FormRequest` en `app/Http/Requests` (validan y comprueban permisos).
- Acciones: `app/Actions` (`CreateProduct`, `SolicitarPresupuesto`).
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
