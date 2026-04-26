<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Venta extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'folio', 'sucursal_id', 'cliente_id', 'inventario_id', 'fecha', 'metodo_pago', 'precio_total', 'estado'
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'precio_total' => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
}
