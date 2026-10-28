<?php

namespace App\Models;

use App\Enums\Category;
use App\Enums\Style;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory, Prunable, SoftDeletes;

    // Clave de caché que cambia cada vez que se toca una obra: así la API nunca sirve datos viejos
    public const VERSION_CACHE = 'obras.version';

    // Días que pasa una obra en la papelera antes de que `model:prune` la borre definitivamente
    public const DIAS_PAPELERA = 30;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'year',
        'price',
        'available',
        'featured',
        'sort_order',
        'height',
        'width',
        'style',
        'category',
        'imagen',
    ];

    protected $casts = [
        'height' => 'float',
        'width' => 'float',
        'year' => 'integer',
        'price' => 'decimal:2',
        'available' => 'boolean',
        'featured' => 'boolean',
        'sort_order' => 'integer',
        'style' => Style::class,
        'category' => Category::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (Product $obra) {
            $obra->slug ??= static::slugUnico($obra->name);
            $obra->sort_order = $obra->sort_order ?: (int) static::withTrashed()->max('sort_order') + 1;
        });

        $invalidar = fn () => Cache::forever(self::VERSION_CACHE, (string) Str::uuid());
        static::saved($invalidar);
        static::deleted($invalidar);
        static::restored($invalidar);

        // Al borrar definitivamente (desde la papelera o al purgarla) se borran también la imagen y sus miniaturas
        static::forceDeleted(function (Product $obra) {
            if ($obra->imagen) {
                Storage::disk('public')->delete('obras/'.$obra->imagen);
                $obra->borrarMiniaturas();
            }
        });
    }

    /**
     * Obras que `php artisan model:prune` borra: las que llevan más de DIAS_PAPELERA en la papelera.
     *
     * @return Builder<Product>
     */
    public function prunable(): Builder
    {
        return static::onlyTrashed()->where('deleted_at', '<=', now()->subDays(self::DIAS_PAPELERA));
    }

    // El slug no cambia al renombrar la obra: los enlaces compartidos siguen funcionando
    public static function slugUnico(string $nombre): string
    {
        $base = Str::slug($nombre) ?: 'obra';
        $slug = $base;

        for ($i = 2; static::withTrashed()->where('slug', $slug)->exists(); $i++) {
            $slug = "{$base}-{$i}";
        }

        return $slug;
    }

    /**
     * Orden de la galería: el que fija el admin.
     *
     * @param  Builder<Product>  $query
     */
    public function scopeOrdenadas(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }

    public function imagenUrl(): string
    {
        return asset('storage/obras/'.rawurlencode((string) $this->imagen));
    }

    // Las miniaturas WebP de cada imagen (App\Actions\GenerarMiniaturas) van en su propia carpeta
    public function carpetaMiniaturas(): string
    {
        return 'obras/miniaturas/'.pathinfo((string) $this->imagen, PATHINFO_FILENAME);
    }

    /**
     * Anchos de las miniaturas que existen, de menor a mayor.
     *
     * @return list<int>
     */
    public function anchosMiniaturas(): array
    {
        if (! $this->imagen) {
            return [];
        }

        $anchos = array_map(
            fn (string $fichero) => (int) pathinfo($fichero, PATHINFO_FILENAME),
            Storage::disk('public')->files($this->carpetaMiniaturas()),
        );
        sort($anchos);

        return array_values(array_filter($anchos));
    }

    // Valor del atributo srcset ("…/400.webp 400w, …/800.webp 800w"); null si la obra no tiene miniaturas
    public function srcset(): ?string
    {
        $carpeta = asset('storage/'.$this->carpetaMiniaturas());
        $srcset = array_map(fn (int $ancho) => "{$carpeta}/{$ancho}.webp {$ancho}w", $this->anchosMiniaturas());

        return $srcset ? implode(', ', $srcset) : null;
    }

    // La miniatura más pequeña (para listados); si no hay, la imagen original
    public function miniaturaUrl(): string
    {
        $ancho = $this->anchosMiniaturas()[0] ?? null;

        return $ancho ? asset('storage/'.$this->carpetaMiniaturas()."/{$ancho}.webp") : $this->imagenUrl();
    }

    public function borrarMiniaturas(): void
    {
        if ($this->imagen) {
            Storage::disk('public')->deleteDirectory($this->carpetaMiniaturas());
        }
    }

    public function precioTexto(): string
    {
        return $this->price === null
            ? 'Precio a consultar'
            : number_format((float) $this->price, 2, ',', '.').' €';
    }
}
