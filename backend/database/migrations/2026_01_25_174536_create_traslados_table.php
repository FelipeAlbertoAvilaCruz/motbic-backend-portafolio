<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('traslados', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('inventario_id')->constrained('inventario');
            $table->foreignUuid('origen_sucursal_id')->constrained('sucursales');
            $table->foreignUuid('destino_sucursal_id')->constrained('sucursales');
            $table->foreignId('user_id')->constrained('users');
            $table->timestamp('fecha_traslado')->useCurrent();
            $table->enum('estado', ['pendiente', 'completado', 'cancelado'])->default('completado'); 
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traslados');
    }
};
