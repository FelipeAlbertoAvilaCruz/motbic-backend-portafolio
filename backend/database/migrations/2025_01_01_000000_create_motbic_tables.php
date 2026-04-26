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
        Schema::create('modelos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nombre', 120);
            $table->string('marca', 120);
            $table->char('anio', 4);
            $table->enum('tipo_motor', ['electrica', 'gasolina', 'hibrida'])->default('gasolina');
            $table->string('cilindrada', 50);
            $table->decimal('precio', 12, 2);
            $table->string('imagen', 255)->nullable();
            $table->json('colores');
            $table->timestamps();
            
            // Fulltext indexes
            // $table->fullText('nombre'); // MySQL only
            // $table->fullText('marca'); // MySQL only
        });

        Schema::create('inventario', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('modelo_id')->constrained('modelos');
            $table->string('color', 60);
            $table->string('serie', 60)->unique();
            $table->string('motor', 60);
            $table->char('vin', 17)->unique();
            $table->enum('estado', ['disponible', 'vendida', 'reservada', 'defectuosa'])->default('disponible');
            $table->timestamps();
            
            $table->index('estado');
        });

        Schema::create('clientes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nombres', 120);
            $table->string('apellidos', 150);
            $table->string('telefono', 20)->unique();
            $table->string('email', 150)->unique();
            $table->char('rfc', 13)->unique();
            $table->string('calle', 150);
            $table->string('colonia', 120);
            $table->string('ciudad', 120);
            $table->string('estado', 120);
            $table->char('codigo_postal', 5);
            $table->date('ultima_compra')->nullable();
            $table->enum('estado_servicios', ['al-dia', 'pendiente', 'vencido'])->nullable();
            $table->timestamps();

            // $table->fullText(['nombres', 'apellidos']); // MySQL only
        });

        Schema::create('ventas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('folio', 32)->unique();
            $table->foreignUuid('cliente_id')->constrained('clientes');
            $table->foreignUuid('inventario_id')->constrained('inventario');
            $table->dateTime('fecha');
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'tarjeta-credito', 'tarjeta-debito', 'cheque', 'financiamiento', 'mixto']);
            $table->decimal('precio_total', 12, 2);
            $table->enum('estado', ['completada', 'pendiente', 'cancelada'])->default('completada');
            $table->timestamps();
        });

        Schema::create('servicios', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('cliente_id')->constrained('clientes');
            $table->foreignUuid('inventario_id')->constrained('inventario');
            $table->foreignUuid('venta_id')->nullable()->constrained('ventas');
            $table->enum('tipo_servicio', ['primer-servicio', 'segundo-servicio', 'tercer-servicio', 'mantenimiento-regular', 'reparacion']);
            $table->date('fecha_programada');
            $table->date('fecha_realizada')->nullable();
            $table->enum('estado', ['programado', 'completado', 'cancelado', 'vencido'])->default('programado');
            $table->text('notas')->nullable();
            $table->decimal('costo', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicios');
        Schema::dropIfExists('ventas');
        Schema::dropIfExists('clientes');
        Schema::dropIfExists('inventario');
        Schema::dropIfExists('modelos');
    }
};
