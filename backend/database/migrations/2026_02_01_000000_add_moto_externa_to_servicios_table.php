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
        Schema::table('servicios', function (Blueprint $table) {
            $table->boolean('moto_externa')->default(false)->after('costo');
            $table->text('detalles_moto_externa')->nullable()->after('moto_externa');
            // Make inventario_id nullable for external motorcycles
            $table->dropForeign(['inventario_id']);
            $table->uuid('inventario_id')->nullable()->change();
            $table->foreign('inventario_id')->references('id')->on('inventario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            $table->dropForeign(['inventario_id']);
            $table->dropColumn(['moto_externa', 'detalles_moto_externa']);
            $table->uuid('inventario_id')->change(); // Make it not nullable again, but this might fail if there are nulls
            $table->foreign('inventario_id')->references('id')->on('inventario');
        });
    }
};