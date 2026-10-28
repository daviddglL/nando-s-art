<?php

namespace App\Models;

use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// MustVerifyEmail: al registrarse se envía un enlace de verificación y /presupuesto exige haberlo abierto
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
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

    protected static function booted(): void
    {
        // El ON DELETE CASCADE de la base de datos no borraría las imágenes privadas de sus encargos
        static::deleting(function (User $user) {
            $user->encargos->each->delete();
        });
    }

    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    /**
     * @return HasMany<Encargo, $this>
     */
    public function encargos(): HasMany
    {
        return $this->hasMany(Encargo::class);
    }
}
