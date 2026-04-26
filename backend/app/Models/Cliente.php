<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Cliente extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'nombres', 'apellidos', 'telefono', 'email', 'rfc', 'calle', 'colonia', 'ciudad', 'estado', 'codigo_postal', 'ultima_compra', 'estado_servicios'
    ];
}
