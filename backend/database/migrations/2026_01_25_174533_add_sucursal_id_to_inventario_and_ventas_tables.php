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
        Schema::table('inventario', function (Blueprint $table) {
            $table->foreignUuid('sucursal_id')->nullable()->after('modelo_id')->constrained('sucursales');
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->foreignUuid('sucursal_id')->nullable()->after('id')->constrained('sucursales');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropForeign(['sucursal_id']);
            $table->dropColumn('sucursal_id');
        });

        Schema::table('inventario', function (Blueprint $table) {
            $table->dropForeign(['sucursal_id']);
            $table->dropColumn('sucursal_id');
        });
    }
};
