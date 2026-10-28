<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // 'role' no es asignable en masa: se asigna a mano solo donde está autorizado
    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $attributes = [
        'role' => 'user',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'role' => Role::class,
    ];

    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }
}
