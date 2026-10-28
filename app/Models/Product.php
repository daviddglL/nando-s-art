<?php

namespace App\Models;

use App\Enums\Category;
use App\Enums\Style;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory, SoftDeletes;

    // Clave de caché que cambia cada vez que se toca una obra: así la API nunca sirve datos viejos
    public const VERSION_CACHE = 'obras.version';

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

    public function precioTexto(): string
    {
        return $this->price === null
            ? 'Precio a consultar'
            : number_format((float) $this->price, 2, ',', '.').' €';
    }
}
