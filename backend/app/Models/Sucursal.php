<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Sucursal extends Model
{
    use HasFactory, HasUuids;
    
    protected $table = 'sucursales';

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'email',
        'activa'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_sucursal');
    }

    public function inventarios()
    {
        return $this->hasMany(Inventario::class);
    }
    
    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}
