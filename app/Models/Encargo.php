<?php

namespace App\Models;

use App\Enums\Category;
use App\Enums\EstadoEncargo;
use App\Enums\Style;
use Database\Factories\EncargoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Petición de presupuesto de un cliente (en la interfaz: "presupuesto")
class Encargo extends Model
{
    /** @use HasFactory<EncargoFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'description',
        'height',
        'width',
        'style',
        'category',
        'imagen',
        'status',
        'respuesta',
        'precio',
    ];

    // style y category no se convierten a enum: son columnas string y puede haber
    // encargos antiguos con valores que ya no existen. Se leen con styleEnum()/categoryEnum().
    protected $casts = [
        'status' => EstadoEncargo::class,
        'precio' => 'decimal:2',
        'respondido_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // La imagen de referencia se guarda con su carpeta (presupuestos/...) en el disco public
    public function imagenUrl(): ?string
    {
        return $this->imagen ? asset('storage/'.$this->imagen) : null;
    }

    public function precioTexto(): string
    {
        return $this->precio === null ? 'Sin precio todavía' : number_format((float) $this->precio, 2, ',', '.').' €';
    }

    public function styleEnum(): ?Style
    {
        return Style::tryFrom((string) $this->style);
    }

    public function categoryEnum(): ?Category
    {
        return Category::tryFrom((string) $this->category);
    }
}
