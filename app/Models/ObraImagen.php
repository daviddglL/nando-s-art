<?php

namespace App\Models;

use App\Models\Concerns\TieneImagen;
use Database\Factories\ObraImagenFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

// Imagen adicional de una obra (detalles, otros ángulos...); la principal es Product::$imagen
class ObraImagen extends Model
{
    /** @use HasFactory<ObraImagenFactory> */
    use HasFactory, TieneImagen;

    protected $table = 'obra_imagenes';

    protected $fillable = ['imagen', 'sort_order'];

    protected $casts = [
        'product_id' => 'integer',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        // Las nuevas van al final de las de su obra
        static::creating(function (ObraImagen $imagen) {
            $imagen->sort_order = $imagen->sort_order
                ?: (int) static::where('product_id', $imagen->product_id)->max('sort_order') + 1;
        });

        // La API da las imágenes de cada obra: cualquier cambio renueva su caché
        $invalidar = fn () => Cache::forever(Product::VERSION_CACHE, (string) Str::uuid());
        static::saved($invalidar);
        static::deleted($invalidar);

        static::deleted(fn (ObraImagen $imagen) => $imagen->borrarImagen());
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function obra(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id')->withTrashed();
    }
}
