<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role_id',
    ];

    /**
     * Relación con el rol.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Verificar si el usuario tiene un rol específico (por slug).
     * Puede recibir un string o un array de roles.
     */
    public function hasRole($roles)
    {
        if (is_array($roles)) {
            return in_array($this->role->slug, $roles);
        }
        return $this->role->slug === $roles;
    }

    /**
     * Relación con sucursales.
     */
    public function sucursales()
    {
        return $this->belongsToMany(Sucursal::class, 'user_sucursal');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
