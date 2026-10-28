<?php

namespace App\Models;

use App\Enums\Category;
use App\Enums\ShipmentStatus;
use App\Enums\Style;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
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
    ];

    // style y category no se convierten a enum: son columnas string y puede haber
    // presupuestos antiguos con valores que ya no existen. Se leen con styleEnum()/categoryEnum().
    protected $casts = [
        'status' => ShipmentStatus::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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
