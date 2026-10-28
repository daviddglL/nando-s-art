<?php

namespace App\Models;

use App\Enums\Category;
use App\Enums\Style;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'height',
        'width',
        'style',
        'category',
        'imagen',
    ];

    protected $casts = [
        'height' => 'float',
        'width' => 'float',
        'style' => Style::class,
        'category' => Category::class,
    ];
}
