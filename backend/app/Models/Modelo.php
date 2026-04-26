<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Modelo extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'modelos';

    protected $fillable = [
        'nombre', 'marca', 'anio', 'tipo_motor', 'cilindrada', 'precio', 'imagen', 'colores'
    ];

    protected $casts = [
        'colores' => 'array',
        'precio' => 'decimal:2',
    ];
}
