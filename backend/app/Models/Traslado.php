<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Traslado extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'inventario_id',
        'origen_sucursal_id',
        'destino_sucursal_id',
        'user_id',
        'fecha_traslado',
        'estado',
        'observaciones'
    ];

    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }

    public function sucursalOrigen()
    {
        return $this->belongsTo(Sucursal::class, 'origen_sucursal_id');
    }

    public function sucursalDestino()
    {
        return $this->belongsTo(Sucursal::class, 'destino_sucursal_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
